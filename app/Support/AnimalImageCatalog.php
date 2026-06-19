<?php

namespace App\Support;

/**
 * URLs de referencia por especie (Wikimedia Commons) para demo de rescate.
 */
final class AnimalImageCatalog
{
    /** @var array<string, string> */
    private const IMAGES = [
        'zorro' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Cerdocyon_thous_-_pampas_fox.jpg/640px-Cerdocyon_thous_-_pampas_fox.jpg',
        'perezoso' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/Bradypus.jpg/640px-Bradypus.jpg',
        'loro' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Ara_ararauna_Luc_Viatour.jpg/640px-Ara_ararauna_Luc_Viatour.jpg',
        'guacamayo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Ara_ararauna_Luc_Viatour.jpg/640px-Ara_ararauna_Luc_Viatour.jpg',
        'jaguar' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Panthera_onca_at_the_Toronto_Zoo_2.jpg/640px-Panthera_onca_at_the_Toronto_Zoo_2.jpg',
        'tapir' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Tapir_Terrestre.jpg/640px-Tapir_Terrestre.jpg',
        'serpiente' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Boa_constrictor_%28female%29.jpg/640px-Boa_constrictor_%28female%29.jpg',
        'boa' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Boa_constrictor_%28female%29.jpg/640px-Boa_constrictor_%28female%29.jpg',
        'mono' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/43/Cebus_albifrons_edit1.jpg/640px-Cebus_albifrons_edit1.jpg',
        'tucan' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Toco_toucan_%28Ramphastos_toco%29.jpg/640px-Toco_toucan_%28Ramphastos_toco%29.jpg',
        'oso' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Myrmecophaga_tridactyla_%28Giant_anteater%29.jpg/640px-Myrmecophaga_tridactyla_%28Giant_anteater%29.jpg',
        'ciervo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Marsh_deer_%28Blastocerus_dichotomus%29_male.JPG/640px-Marsh_deer_%28Blastocerus_dichotomus%29_male.JPG',
        'huron' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Mustela_putorius_5.jpg/640px-Mustela_putorius_5.jpg',
        'perro' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/YellowLabradorLooking_new.jpg/640px-YellowLabradorLooking_new.jpg',
        'gato' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Cat03.jpg/640px-Cat03.jpg',
        'nutria' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Lontra_canadensis_2.jpg/640px-Lontra_canadensis_2.jpg',
        'tortuga' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Chelonoidis_nigra_%28Galapagos_tortoise%29.jpg/640px-Chelonoidis_nigra_%28Galapagos_tortoise%29.jpg',
        'puercoespin' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Coendou_prehensilis.jpg/640px-Coendou_prehensilis.jpg',
        'aguila' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Haliaeetus_leucocephalus_-Alaska-8a.jpg/640px-Haliaeetus_leucocephalus_-Alaska-8a.jpg',
        'coati' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Nasua_narica.jpg/640px-Nasua_narica.jpg',
        'capuchino' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/43/Cebus_albifrons_edit1.jpg/640px-Cebus_albifrons_edit1.jpg',
        'hormiguero' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Myrmecophaga_tridactyla_%28Giant_anteater%29.jpg/640px-Myrmecophaga_tridactyla_%28Giant_anteater%29.jpg',
        'pantanos' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Marsh_deer_%28Blastocerus_dichotomus%29_male.JPG/640px-Marsh_deer_%28Blastocerus_dichotomus%29_male.JPG',
        'callejero' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/YellowLabradorLooking_new.jpg/640px-YellowLabradorLooking_new.jpg',
        'abandonado' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Cat03.jpg/640px-Cat03.jpg',
        'fauna' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Walking_tiger_female.jpg/640px-Walking_tiger_female.jpg',
    ];

    public static function seedFor(string $label): string
    {
        $normalized = self::normalize($label);

        foreach (array_keys(self::IMAGES) as $key) {
            if ($key === 'fauna') {
                continue;
            }
            if (str_contains($normalized, $key)) {
                return $key;
            }
        }

        return 'fauna';
    }

    public static function urlFor(string $label): string
    {
        $key = self::seedFor($label);

        return self::IMAGES[$key] ?? self::IMAGES['fauna'];
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
