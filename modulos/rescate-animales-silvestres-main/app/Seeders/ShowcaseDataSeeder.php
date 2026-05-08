<?php

namespace Modules\Rescate\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalCondition;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Models\CareFeeding;
use Modules\Rescate\Models\CareType;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\ContactMessage;
use Modules\Rescate\Models\FeedingFrequency;
use Modules\Rescate\Models\FeedingPortion;
use Modules\Rescate\Models\FeedingType;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\MedicalEvaluation;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Rescuer;
use Modules\Rescate\Models\Species;
use Modules\Rescate\Models\TreatmentType;
use Modules\Rescate\Models\User;
use Modules\Rescate\Models\Veterinarian;

class ShowcaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $centerNorte = Center::firstOrCreate(
            ['nombre' => 'Centro Norte de Rescate'],
            ['direccion' => 'Av. Banzer km 12', 'latitud' => -17.7452, 'longitud' => -63.1601, 'contacto' => '70011122']
        );
        $centerEste = Center::firstOrCreate(
            ['nombre' => 'Centro Este de Rehabilitación'],
            ['direccion' => 'Ruta a Cotoca km 9', 'latitud' => -17.7922, 'longitud' => -63.0483, 'contacto' => '70033344']
        );
        $centerSur = Center::firstOrCreate(
            ['nombre' => 'Centro Sur de Fauna'],
            ['direccion' => 'Doble vía La Guardia km 6', 'latitud' => -17.8527, 'longitud' => -63.2215, 'contacto' => '70055566']
        );

        $statusEstable = AnimalStatus::firstOrCreate(['nombre' => 'Estable']);
        $statusRecuperacion = AnimalStatus::firstOrCreate(['nombre' => 'En recuperación']);
        $statusCritico = AnimalStatus::firstOrCreate(['nombre' => 'Crítico']);

        $speciesZorro = Species::firstOrCreate(['nombre' => 'Zorro de monte']);
        $speciesPerezoso = Species::firstOrCreate(['nombre' => 'Perezoso']);
        $speciesLoro = Species::firstOrCreate(['nombre' => 'Loro hablador']);

        $careCuracion = CareType::firstOrCreate(['nombre' => 'Curación'], ['descripcion' => 'Limpieza y tratamiento de heridas']);
        $careRehab = CareType::firstOrCreate(['nombre' => 'Rehabilitación'], ['descripcion' => 'Fortalecimiento y adaptación']);
        $careAlim = CareType::firstOrCreate(['nombre' => 'Alimentación asistida'], ['descripcion' => 'Plan nutricional controlado']);

        $feedType = FeedingType::firstOrCreate(['nombre' => 'Frutas y semillas'], ['descripcion' => 'Dieta blanda de recuperación']);
        $feedFrequency = FeedingFrequency::firstOrCreate(['nombre' => '3 veces al día'], ['descripcion' => 'Mañana, tarde y noche']);
        $feedPortion = FeedingPortion::firstOrCreate(['cantidad' => 250, 'unidad' => 'gramos']);

        $treatmentSuero = TreatmentType::firstOrCreate(['nombre' => 'Fluidoterapia']);
        $treatmentAntibiotico = TreatmentType::firstOrCreate(['nombre' => 'Antibiótico']);

        $condGrave = AnimalCondition::firstOrCreate(['nombre' => 'Herido grave'], ['severidad' => 5, 'activo' => true]);
        $condQuemaduras = AnimalCondition::firstOrCreate(['nombre' => 'Quemaduras'], ['severidad' => 4, 'activo' => true]);
        $condDesconocido = AnimalCondition::firstOrCreate(['nombre' => 'Desconocido'], ['severidad' => 3, 'activo' => true]);

        $incIncendio = IncidentType::firstOrCreate(['nombre' => 'Incendio'], ['riesgo' => 2, 'activo' => true]);
        $incAtropello = IncidentType::firstOrCreate(['nombre' => 'Atropello'], ['riesgo' => 2, 'activo' => true]);
        $incOtro = IncidentType::firstOrCreate(['nombre' => 'Otro'], ['riesgo' => 1, 'activo' => true]);

        $rescuerUser = User::firstOrCreate(
            ['email' => 'rescatista.demo@rescate.local'],
            ['password' => Hash::make('rescate123')]
        );
        $rescuerPerson = Person::firstOrCreate(
            ['usuario_id' => $rescuerUser->id],
            ['nombre' => 'Luis Ortega', 'ci' => '4455667', 'telefono' => '71100001', 'es_cuidador' => false]
        );

        $vetUser = User::firstOrCreate(
            ['email' => 'veterinaria.demo@rescate.local'],
            ['password' => Hash::make('rescate123')]
        );
        $vetPerson = Person::firstOrCreate(
            ['usuario_id' => $vetUser->id],
            ['nombre' => 'María Salvatierra', 'ci' => '5566778', 'telefono' => '71100002', 'es_cuidador' => false]
        );

        $caregiverUser = User::firstOrCreate(
            ['email' => 'cuidador.demo@rescate.local'],
            ['password' => Hash::make('rescate123')]
        );
        Person::firstOrCreate(
            ['usuario_id' => $caregiverUser->id],
            [
                'nombre' => 'Ana Vega',
                'ci' => '6677889',
                'telefono' => '71100003',
                'es_cuidador' => true,
                'cuidador_center_id' => $centerEste->id,
                'cuidador_aprobado' => true,
            ]
        );

        $citizenUser = User::firstOrCreate(
            ['email' => 'ciudadano.demo@rescate.local'],
            ['password' => Hash::make('rescate123')]
        );
        $citizenPerson = Person::firstOrCreate(
            ['usuario_id' => $citizenUser->id],
            ['nombre' => 'Carlos Ibañez', 'ci' => '7788990', 'telefono' => '71100004', 'es_cuidador' => false]
        );

        $rescuer = Rescuer::firstOrCreate(
            ['persona_id' => $rescuerPerson->id],
            [
                'cv_documentado' => 'demo/luis-ortega-cv.pdf',
                'motivo_postulacion' => 'Apoyo en respuesta rápida de campo.',
                'aprobado' => true,
            ]
        );

        $veterinarian = Veterinarian::firstOrCreate(
            ['persona_id' => $vetPerson->id],
            [
                'especialidad' => 'Fauna silvestre',
                'cv_documentado' => 'demo/maria-salvatierra-cv.pdf',
                'motivo_postulacion' => 'Atención clínica y seguimiento post-rescate.',
                'aprobado' => true,
            ]
        );

        $reportFire = Report::firstOrCreate(
            ['direccion' => 'Zona de amortiguación del Urubó', 'tipo_incidente_id' => $incIncendio->id],
            [
                'persona_id' => $citizenPerson->id,
                'aprobado' => 1,
                'imagen_url' => 'reports/demo-incendio-zorro.jpg',
                'observaciones' => 'Zorro afectado por humo y deshidratación.',
                'latitud' => -17.6891,
                'longitud' => -63.2124,
                'condicion_inicial_id' => $condQuemaduras->id,
                'tamano' => 'mediano',
                'puede_moverse' => false,
                'urgencia' => 5,
            ]
        );

        $reportRoad = Report::firstOrCreate(
            ['direccion' => 'Ruta a Cotoca, km 14', 'tipo_incidente_id' => $incAtropello->id],
            [
                'persona_id' => $citizenPerson->id,
                'aprobado' => 1,
                'imagen_url' => 'reports/demo-atropello-perezoso.jpg',
                'observaciones' => 'Perezoso con movilidad reducida en extremidad delantera.',
                'latitud' => -17.8042,
                'longitud' => -63.0117,
                'condicion_inicial_id' => $condGrave->id,
                'tamano' => 'mediano',
                'puede_moverse' => false,
                'urgencia' => 5,
            ]
        );

        $reportOther = Report::firstOrCreate(
            ['direccion' => 'Barrio Paurito, plaza central', 'tipo_incidente_id' => $incOtro->id],
            [
                'persona_id' => $citizenPerson->id,
                'aprobado' => 0,
                'imagen_url' => 'reports/demo-otro-loro.jpg',
                'observaciones' => 'Loro desorientado y con signos de estrés.',
                'latitud' => -17.8792,
                'longitud' => -62.9743,
                'condicion_inicial_id' => $condDesconocido->id,
                'tamano' => 'pequeno',
                'puede_moverse' => true,
                'urgencia' => 3,
            ]
        );

        $animalZorro = Animal::firstOrCreate(
            ['nombre' => 'Zorro Urubó', 'reporte_id' => $reportFire->id],
            ['sexo' => 'M', 'descripcion' => 'Adulto joven con irritación ocular y fatiga.']
        );
        $animalPerezoso = Animal::firstOrCreate(
            ['nombre' => 'Perezoso Cotoca', 'reporte_id' => $reportRoad->id],
            ['sexo' => 'F', 'descripcion' => 'Lesión en miembro superior, requiere observación.']
        );
        $animalLoro = Animal::firstOrCreate(
            ['nombre' => 'Loro Paurito', 'reporte_id' => $reportOther->id],
            ['sexo' => 'M', 'descripcion' => 'Ave en evaluación inicial de comportamiento.']
        );

        $fileZorro = AnimalFile::firstOrCreate(
            ['animal_id' => $animalZorro->id],
            ['especie_id' => $speciesZorro->id, 'imagen_url' => 'animal-files/zorro-urubo.jpg', 'estado_id' => $statusRecuperacion->id, 'centro_id' => $centerNorte->id]
        );
        $filePerezoso = AnimalFile::firstOrCreate(
            ['animal_id' => $animalPerezoso->id],
            ['especie_id' => $speciesPerezoso->id, 'imagen_url' => 'animal-files/perezoso-cotoca.jpg', 'estado_id' => $statusCritico->id, 'centro_id' => $centerEste->id]
        );
        $fileLoro = AnimalFile::firstOrCreate(
            ['animal_id' => $animalLoro->id],
            ['especie_id' => $speciesLoro->id, 'imagen_url' => 'animal-files/loro-paurito.jpg', 'estado_id' => $statusEstable->id, 'centro_id' => $centerSur->id]
        );

        DB::connection('rescate')->table('transfers')->updateOrInsert(
            ['reporte_id' => $reportFire->id, 'animal_id' => $animalZorro->id],
            [
                'rescatista_id' => $rescuer->id,
                'persona_id' => $rescuerPerson->id,
                'centro_id' => $centerNorte->id,
                'observaciones' => 'Traslado inicial coordinado con brigada local.',
                'primer_traslado' => true,
                'latitud' => -17.6891,
                'longitud' => -63.2124,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        DB::connection('rescate')->table('transfers')->updateOrInsert(
            ['reporte_id' => $reportRoad->id, 'animal_id' => $animalPerezoso->id],
            [
                'rescatista_id' => $rescuer->id,
                'persona_id' => $rescuerPerson->id,
                'centro_id' => $centerEste->id,
                'observaciones' => 'Traslado con inmovilización preventiva.',
                'primer_traslado' => true,
                'latitud' => -17.8042,
                'longitud' => -63.0117,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        MedicalEvaluation::firstOrCreate(
            ['animal_file_id' => $fileZorro->id, 'fecha' => now()->subDays(1)->toDateString()],
            [
                'tratamiento_id' => $treatmentSuero->id,
                'tratamiento_texto' => 'Hidratación intravenosa y observación respiratoria.',
                'descripcion' => 'Evidencia de exposición a humo, sin fracturas.',
                'diagnostico' => 'Estrés térmico moderado',
                'peso' => 6.2,
                'temperatura' => 38.5,
                'recomendacion' => 'Monitoreo 72h y reevaluación clínica.',
                'apto_traslado' => true,
                'veterinario_id' => $veterinarian->id,
                'imagen_url' => 'medical-evaluations/zorro-eval.jpg',
            ]
        );
        MedicalEvaluation::firstOrCreate(
            ['animal_file_id' => $filePerezoso->id, 'fecha' => now()->toDateString()],
            [
                'tratamiento_id' => $treatmentAntibiotico->id,
                'tratamiento_texto' => 'Cobertura antibiótica y analgésica.',
                'descripcion' => 'Contusión de tejidos blandos y debilidad general.',
                'diagnostico' => 'Trauma por impacto vehicular',
                'peso' => 4.7,
                'temperatura' => 37.9,
                'recomendacion' => 'Reposo total y fisioterapia temprana.',
                'apto_traslado' => false,
                'veterinario_id' => $veterinarian->id,
                'imagen_url' => 'medical-evaluations/perezoso-eval.jpg',
            ]
        );

        $careRecord1 = Care::firstOrCreate(
            ['hoja_animal_id' => $fileZorro->id, 'tipo_cuidado_id' => $careCuracion->id, 'fecha' => now()->toDateString()],
            [
                'descripcion' => 'Limpieza de heridas superficiales y control de hidratación.',
                'imagen_url' => 'cares/zorro-curacion.jpg',
            ]
        );
        Care::firstOrCreate(
            ['hoja_animal_id' => $filePerezoso->id, 'tipo_cuidado_id' => $careRehab->id, 'fecha' => now()->toDateString()],
            [
                'descripcion' => 'Ejercicios pasivos y control de dolor.',
                'imagen_url' => 'cares/perezoso-rehab.jpg',
            ]
        );
        $careRecord3 = Care::firstOrCreate(
            ['hoja_animal_id' => $fileLoro->id, 'tipo_cuidado_id' => $careAlim->id, 'fecha' => now()->toDateString()],
            [
                'descripcion' => 'Alimentación supervisada por cuidadores.',
                'imagen_url' => 'cares/loro-alimentacion.jpg',
            ]
        );

        CareFeeding::firstOrCreate(
            ['care_id' => $careRecord3->id],
            [
                'feeding_type_id' => $feedType->id,
                'feeding_frequency_id' => $feedFrequency->id,
                'feeding_portion_id' => $feedPortion->id,
            ]
        );

        Release::firstOrCreate(
            ['animal_file_id' => $fileZorro->id],
            [
                'direccion' => 'Reserva ecológica Güembé',
                'detalle' => 'Liberación controlada tras recuperación satisfactoria.',
                'latitud' => -17.7599,
                'longitud' => -63.1522,
                'aprobada' => true,
                'imagen_url' => 'releases/zorro-liberado.jpg',
            ]
        );

        ContactMessage::firstOrCreate(
            [
                'user_id' => $citizenUser->id,
                'motivo' => 'contacto_directo',
            ],
            [
                'mensaje' => 'Nos organizamos para reportar y apoyar traslados en eventos de incendio.',
                'leido' => false,
            ]
        );
    }
}
