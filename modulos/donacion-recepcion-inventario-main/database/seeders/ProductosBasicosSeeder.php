<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosBasicosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure we have a category for food items
        $categoriaAlimentos = DB::table('categorias_productos')->where('nombre', 'Alimentos')->first();
        if (!$categoriaAlimentos) {
            DB::table('categorias_productos')->insert([
                'nombre' => 'Alimentos'
            ]);
            $categoriaAlimentos = DB::table('categorias_productos')->where('nombre', 'Alimentos')->first();
            $idCategoriaAlimentos = $categoriaAlimentos->id_categoria;
        } else {
            $idCategoriaAlimentos = $categoriaAlimentos->id_categoria;
        }

        // Common donation products
        $productos = [
            ['nombre' => 'Arroz', 'descripcion' => 'Arroz blanco', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Agua', 'descripcion' => 'Agua embotellada', 'unidad_medida' => 'litros', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Azúcar', 'descripcion' => 'Azúcar blanca', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Café', 'descripcion' => 'Café molido', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Aceite', 'descripcion' => 'Aceite vegetal', 'unidad_medida' => 'litros', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Fideo', 'descripcion' => 'Fideos/Pasta', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Leche', 'descripcion' => 'Leche en polvo o líquida', 'unidad_medida' => 'litros', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Harina', 'descripcion' => 'Harina de trigo', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Sal', 'descripcion' => 'Sal de mesa', 'unidad_medida' => 'kg', 'id_categoria' => $idCategoriaAlimentos],
            ['nombre' => 'Atún', 'descripcion' => 'Atún enlatado', 'unidad_medida' => 'unidades', 'id_categoria' => $idCategoriaAlimentos],
        ];

        foreach ($productos as $producto) {
            // Check if product already exists
            $exists = DB::table('productos')
                ->where('nombre', $producto['nombre'])
                ->exists();

            if (!$exists) {
                DB::table('productos')->insert($producto);
            }
        }

        $this->command->info('Productos básicos de donación creados exitosamente.');
    }
}



