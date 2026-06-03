<?php

namespace App\Support;

use Modules\Rescate\Models\Animal;

class RescateAnimalNameCleaner
{
    /**
     * Quita el sufijo " demo" y el número opcional al final (p. ej. "Tucán demo 3" → "Tucán").
     */
    public static function sanitize(string $nombre): string
    {
        $clean = preg_replace('/\s+demo(\s+\d+)?$/iu', '', trim($nombre));

        return $clean === '' ? trim($nombre) : $clean;
    }

    /**
     * @return int Cantidad de registros actualizados
     */
    public static function cleanAll(): int
    {
        $updated = 0;

        Animal::query()->select(['id', 'nombre'])->orderBy('id')->each(function (Animal $animal) use (&$updated) {
            $clean = self::sanitize((string) $animal->nombre);
            if ($clean === $animal->nombre) {
                return;
            }

            $animal->update(['nombre' => $clean]);
            $updated++;
        });

        return $updated;
    }
}
