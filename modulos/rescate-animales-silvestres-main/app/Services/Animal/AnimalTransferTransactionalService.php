<?php

namespace Modules\Rescate\Services\Animal;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Rescuer;
use Modules\Rescate\Models\Transfer;
use Modules\Rescate\Services\Animal\AnimalTransferHistoryService;

class AnimalTransferTransactionalService
{
    public function __construct(
        private readonly AnimalTransferHistoryService $historyService
    ) {
    }
	public function create(array $data): Transfer
	{
		return DB::transaction(function () use ($data) {
            // Bloquear traslados si el animal ya fue liberado
            if (!empty($data['animal_id'])) {
                $afIds = \Modules\Rescate\Models\AnimalFile::where('animal_id', $data['animal_id'])->pluck('id');
                if ($afIds->isNotEmpty()) {
                    $released = \Modules\Rescate\Models\Release::whereIn('animal_file_id', $afIds)->exists();
                    if ($released) {
                        throw new \DomainException('No se puede trasladar: el animal ya fue liberado.');
                    }
                }
            }
            $transfer = Transfer::create($data);

			// Registrar historial según el caso
            if (!empty($data['animal_id'])) {
                // Traslado entre centros
                $this->historyService->logInternalTransfer($transfer);

                // Actualizar centro actual en la hoja de vida del animal
                if (!empty($data['centro_id'])) {
                    $animalFile = AnimalFile::where('animal_id', $data['animal_id'])
                        ->orderByDesc('id')
                        ->first();
                    if ($animalFile) {
                        $animalFile->centro_id = $data['centro_id'];
                        $animalFile->save();
                    }
                }
            } else {
                // Primer traslado desde hallazgo (sin hoja aún)
                $this->historyService->logFirstTransfer($transfer, $data['reporte_id'] ?? ($transfer->reporte_id ?? null));
            }

			return $transfer;
		});
	}

    /**
     * Compatibilidad con esquemas unificados que aún exigen rescatista_id.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeTransferPayload(array $data): array
    {
        if (! $this->transfersRequireRescatistaId()) {
            return $data;
        }

        if (! empty($data['rescatista_id'])) {
            return $data;
        }

        $rescuerId = null;
        if (! empty($data['persona_id'])) {
            $rescuerId = Rescuer::where('persona_id', $data['persona_id'])->value('id');
        }

        $data['rescatista_id'] = $rescuerId ?? Rescuer::where('aprobado', true)->value('id');

        return $data;
    }

    private function transfersRequireRescatistaId(): bool
    {
        static $required = null;
        if ($required !== null) {
            return $required;
        }

        if (DB::connection('rescate')->getDriverName() !== 'pgsql') {
            return $required = Schema::connection('rescate')->hasColumn('transfers', 'rescatista_id');
        }

        $row = DB::connection('rescate')->selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND table_name = 'transfers'
              AND column_name = 'rescatista_id'
            LIMIT 1
        ");

        return $required = $row !== null;
    }
}




