<?php

namespace Modules\Incendios\Console\Commands;

use Illuminate\Console\Command;
use Modules\Incendios\Services\FirmsDataService;
use Modules\Incendios\Models\FocoIncendio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateFirmsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firms:update 
                            {--days=1 : Number of days to fetch (1-10)}
                            {--product=VIIRS_NOAA20_NRT : FIRMS product (VIIRS_NOAA20_NRT, VIIRS_SNPP_NRT, MODIS_NRT)}
                            {--area=-62.5,-18.5,-57.5,-14.5 : Bounding box (west,south,east,north)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los focos de incendio desde NASA FIRMS API y los almacena en la base de datos';

    protected FirmsDataService $firmsService;

    public function __construct(FirmsDataService $firmsService)
    {
        parent::__construct();
        $this->firmsService = $firmsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Iniciando actualización de focos de incendio desde FIRMS...');
        
        $days = (int) $this->option('days');
        $product = $this->option('product');
        $area = $this->option('area');

        $this->info("Parámetros: {$product}, Área: {$area}, Días: {$days}");

        // Fetch data from FIRMS
        $result = $this->firmsService->getFireData(
            product: $product,
            area: $area,
            days: $days,
            cluster: false // No clustering for DB storage
        );

        if (!$result['ok']) {
            $this->error('❌ Error al obtener datos: ' . ($result['error'] ?? 'Unknown error'));
            Log::error('FIRMS Update Failed', $result);
            return 1;
        }

        // Mostrar fuente de datos
        $source = $result['source'] ?? 'unknown';
        $sourceName = match($source) {
            'firms' => 'NASA FIRMS',
            'fallback' => 'API Alternativa (Fallback)',
            default => 'Desconocida'
        };

        if ($source === 'fallback') {
            $this->warn("⚠️  FIRMS no disponible - Usando {$sourceName}");
        } else {
            $this->info("✓ Fuente: {$sourceName}");
        }

        $fires = $result['data'];
        $count = count($fires);

        if ($count === 0) {
            $this->warn('⚠️  No se encontraron focos de incendio activos.');
            return 0;
        }

        $this->info("✓ Obtenidos {$count} focos de incendio");

        // Start database transaction
        DB::beginTransaction();
        
        try {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            foreach ($fires as $fire) {
                // Create unique identifier based on coordinates and date
                $lat = $fire['lat'] ?? null;
                $lng = $fire['lng'] ?? null;
                $acqDate = $fire['date'] ?? $fire['acq_date'] ?? null;
                $acqTime = $fire['time'] ?? $fire['acq_time'] ?? null;

                if (!$lat || !$lng || !$acqDate) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Identificar si viene de fallback
                $isFallback = isset($fire['_source']) && $fire['_source'] === 'fallback';

                // Format datetime
                $datetime = $this->parseFireDateTime($acqDate, $acqTime);

                // Round coordinates to avoid duplicates from minor GPS variations
                $latRounded = round($lat, 4);
                $lngRounded = round($lng, 4);

                // Check if foco already exists (same location and date within 1 hour)
                $existing = FocoIncendio::where('fecha', '>=', $datetime->copy()->subHour())
                    ->where('fecha', '<=', $datetime->copy()->addHour())
                    ->whereRaw("(coordenadas->>'lat')::numeric BETWEEN ? AND ?", [$latRounded - 0.0001, $latRounded + 0.0001])
                    ->whereRaw("(coordenadas->>'lng')::numeric BETWEEN ? AND ?", [$lngRounded - 0.0001, $lngRounded + 0.0001])
                    ->first();

                if ($existing) {
                    // Update if confidence/intensity is higher
                    $newIntensity = $fire['frp'] ?? $fire['confidence'] ?? 0;
                    if ($newIntensity > $existing->intensidad) {
                        $existing->update([
                            'intensidad' => $newIntensity,
                            'coordenadas' => [
                                'lat' => $lat,
                                'lng' => $lng,
                            ],
                        ]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    // Create new foco
                    $focoData = [
                        'fecha' => $datetime,
                        'ubicacion' => $this->formatLocation($lat, $lng),
                        'coordenadas' => [
                            'lat' => $lat,
                            'lng' => $lng,
                        ],
                        'intensidad' => $fire['frp'] ?? $fire['confidence'] ?? 0,
                    ];

                    // Agregar metadata de fallback si aplica
                    if ($isFallback && isset($fire['_original_id'])) {
                        $focoData['descripcion'] = "Fallback API - ID: {$fire['_original_id']}";
                    }

                    FocoIncendio::create($focoData);
                    $created++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            DB::commit();

            $this->info("✅ Actualización completada:");
            $this->table(
                ['Acción', 'Cantidad'],
                [
                    ['Creados', $created],
                    ['Actualizados', $updated],
                    ['Saltados (duplicados)', $skipped],
                    ['Total procesados', $count],
                ]
            );

            Log::info('FIRMS data updated successfully', [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'total' => $count,
            ]);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error al guardar en base de datos: ' . $e->getMessage());
            Log::error('FIRMS DB Storage Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }

    /**
     * Parse FIRMS date and time into Carbon datetime
     */
    protected function parseFireDateTime(string $date, ?string $time): \Carbon\Carbon
    {
        // Date format: YYYY-MM-DD
        // Time format: HHMM (24h)
        
        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
        
        if ($time && strlen($time) === 4) {
            $hour = substr($time, 0, 2);
            $minute = substr($time, 2, 2);
            $datetime->setTime((int)$hour, (int)$minute);
        }

        return $datetime;
    }

    /**
     * Format location string from coordinates
     */
    protected function formatLocation(float $lat, float $lng): string
    {
        $latDir = $lat >= 0 ? 'N' : 'S';
        $lngDir = $lng >= 0 ? 'E' : 'W';
        
        return sprintf(
            "%.4f°%s, %.4f°%s (Santa Cruz, Bolivia)",
            abs($lat),
            $latDir,
            abs($lng),
            $lngDir
        );
    }
}
