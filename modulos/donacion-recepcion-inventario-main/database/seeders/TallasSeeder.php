<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TallasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tallas = [
            ['talla' => 'XS'],
            ['talla' => 'S'],
            ['talla' => 'M'],
            ['talla' => 'L'],
            ['talla' => 'XL'],
            ['talla' => 'XXL'],
            ['talla' => 'XXXL'],
        ];

        foreach ($tallas as $talla) {
            // Check if talla already exists to avoid duplicates
            $exists = DB::table('tallas')
                ->where('talla', $talla['talla'])
                ->exists();

            if (!$exists) {
                DB::table('tallas')->insert($talla);
            }
        }

        $this->command->info('Tallas estándar creadas exitosamente.');
    }
}



