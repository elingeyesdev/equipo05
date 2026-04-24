<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donacione;
use App\Models\DonacionesDinero;
use App\Models\Donante;
use App\Models\Campana;
use Carbon\Carbon;

class DonacionesDineroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer donante y la primera campaña
        $donante = Donante::first();
        $campana = Campana::first();

        if (!$donante || !$campana) {
            $this->command->error('Se requiere al menos un donante y una campaña para crear donaciones de dinero.');
            return;
        }

        // Donación 1
        $donacion1 = Donacione::create([
            'id_donante' => $donante->id_donante,
            'tipo' => 'dinero',
            'id_campana' => $campana->id_campana,
            'id_punto_recoleccion' => null,
            'observaciones' => 'Donación en efectivo para la campaña',
            'fecha' => Carbon::now()->subDays(5),
        ]);

        DonacionesDinero::create([
            'id_donacion' => $donacion1->id_donacion,
            'monto' => 500.00,
            'moneda' => 'BOB',
            'metodo_pago' => 'Efectivo',
            'referencia_pago' => 'https://cofers.mx/wp-content/uploads/image-21.png',
        ]);

        // Donación 2
        $donacion2 = Donacione::create([
            'id_donante' => $donante->id_donante,
            'tipo' => 'dinero',
            'id_campana' => $campana->id_campana,
            'id_punto_recoleccion' => null,
            'observaciones' => 'Transferencia bancaria para la campaña solidaria',
            'fecha' => Carbon::now()->subDays(2),
        ]);

        DonacionesDinero::create([
            'id_donacion' => $donacion2->id_donacion,
            'monto' => 1200.00,
            'moneda' => 'BOB',
            'metodo_pago' => 'Transferencia',
            'referencia_pago' => 'https://portal.unsa.mx/ArchivosModales/EjemploReferencias.png',
        ]);

        $this->command->info('Se crearon 2 donaciones en dinero exitosamente.');
    }
}



