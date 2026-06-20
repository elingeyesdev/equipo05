<?php

namespace Modules\Rescate\Services\Animal;

use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Models\CareFeeding;
use Modules\Rescate\Models\CareType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Rescate\Services\User\UserTrackingService;

class AnimalFeedingTransactionalService
{
	/**
	 * Registra Alimentación: crea Care (tipo Alimentación) + CareFeeding + AnimalHistory.
	 * Maneja commit/rollback explícitos.
	 *
	 * @param array $data Datos validados desde FeedingProcessRequest
	 * @return array{care: Care, careFeeding: CareFeeding}
	 */
	public function registerFeeding(array $data): array
	{
		DB::beginTransaction();
		try {
			$animalFile = AnimalFile::findOrFail($data['animal_file_id']);

			$careTypeId = CareType::where('nombre', 'LIKE', '%Aliment%')->value('id');
			if (!$careTypeId) {
				$careType = CareType::create([
					'nombre' => 'Alimentación',
					'descripcion' => 'Cuidados relacionados con alimentación',
				]);
				$careTypeId = $careType->id;
			}

			$care = Care::create([
				'hoja_animal_id' => $animalFile->id,
				'tipo_cuidado_id' => $careTypeId,
				'descripcion' => $data['descripcion'] ?? ('Registro de alimentación del animal #' . ($animalFile->animal?->id ?? $animalFile->id)),
				'fecha' => Carbon::now(),
			]);

			$careFeeding = CareFeeding::create([
				'care_id' => $care->id,
				'feeding_type_id' => $data['feeding_type_id'],
				'feeding_frequency_id' => $data['feeding_frequency_id'],
				'feeding_portion_id' => $data['feeding_portion_id'],
			]);

			AnimalHistory::recordEvent(
                $animalFile->id,
                $animalFile->animalStatus?->nombre ?? 'En custodia',
                $animalFile->animalStatus?->nombre ?? 'En custodia',
                $data['observaciones'] ?? 'Registro de alimentación',
                null,
                [
                    'care' => [
                        'id' => $care->id,
                        'descripcion' => $care->descripcion,
                        'fecha' => (string) $care->fecha,
                        'tipo_cuidado_id' => $care->tipo_cuidado_id,
                    ],
                    'care_feeding' => [
                        'id' => $careFeeding->id,
                        'feeding_type_id' => $careFeeding->feeding_type_id,
                        'feeding_frequency_id' => $careFeeding->feeding_frequency_id,
                        'feeding_portion_id' => $careFeeding->feeding_portion_id,
                    ],
                ],
            );

			// Registrar tracking de alimentación
			try {
				app(UserTrackingService::class)->logFeeding($careFeeding, $care);
			} catch (\Exception $e) {
				\Log::warning('Error registrando tracking de alimentación: ' . $e->getMessage());
			}

			DB::commit();

			return ['care' => $care, 'careFeeding' => $careFeeding];
		} catch (\Throwable $e) {
			DB::rollBack();
			throw $e;
		}
	}
}


