<?php

namespace App\Support;

/**
 * Referencias por especie (Wikimedia Commons) para demo de rescate.
 */
final class AnimalImageCatalog
{
    /** @var array<string, string> Nombre de archivo en Commons */
    private const FILES = [
        'zorro' => 'Lycalopex gymnocercus.jpg',
        'perezoso' => 'Bradypus.jpg',
        'loro' => 'Ara ararauna Luc Viatour.jpg',
        'guacamayo' => 'Ara ararauna Luc Viatour.jpg',
        'jaguar' => 'Panthera onca at the Toronto Zoo 2.jpg',
        'tapir' => 'Tapirus terrestris.jpg',
        'serpiente' => 'Boa constrictor imperator.jpg',
        'boa' => 'Boa constrictor imperator.jpg',
        'mono' => 'Capuchin Costa Rica.jpg',
        'tucan' => 'Toco toucan (Ramphastos toco).jpg',
        'oso' => 'Giant Anteater.jpg',
        'ciervo' => 'Blastocerus dichotomus.jpg',
        'huron' => 'Mustela putorius.jpg',
        'perro' => 'YellowLabradorLooking new.jpg',
        'gato' => 'Cat03.jpg',
        'nutria' => 'Lontra canadensis.jpg',
        'tortuga' => 'Galapagos tortoise.jpg',
        'puercoespin' => 'Coendou prehensilis.jpg',
        'aguila' => 'Bald Eagle (Haliaeetus leucocephalus).jpg',
        'coati' => 'Nasua narica.jpg',
        'capuchino' => 'Capuchin Costa Rica.jpg',
        'hormiguero' => 'Giant Anteater.jpg',
        'pantanos' => 'Blastocerus dichotomus.jpg',
        'callejero' => 'YellowLabradorLooking new.jpg',
        'abandonado' => 'Cat03.jpg',
        'fauna' => 'Walking tiger female.jpg',
    ];

    public static function seedFor(string $label): string
    {
        $normalized = self::normalize($label);

        foreach (array_keys(self::FILES) as $key) {
            if ($key === 'fauna') {
                continue;
            }
            if (str_contains($normalized, $key)) {
                return $key;
            }
        }

        return 'fauna';
    }

    /** URL para descarga server-side (Commons FilePath, evita 403 de /thumb/). */
    public static function downloadUrlFor(string $label): string
    {
        $key = self::seedFor($label);
        $file = self::FILES[$key] ?? self::FILES['fauna'];

        return 'https://commons.wikimedia.org/wiki/Special:FilePath/'
            .rawurlencode($file).'?width=640';
    }

    /** @deprecated Solo referencia; no usar en atributos src del navegador. */
    public static function urlFor(string $label): string
    {
        return self::downloadUrlFor($label);
    }

    public static function publicRelativePath(string $label): string
    {
        return 'images/rescate/'.self::seedFor($label).'.jpg';
    }

    /** @return list<string> Claves del catálogo (una imagen por archivo en public/images/rescate). */
    public static function imageSeeds(): array
    {
        return array_values(array_unique(array_keys(self::FILES)));
    }

    private static function normalize(string $label): string
    {
        $text = mb_strtolower($label, 'UTF-8');
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $text
        );

        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}
