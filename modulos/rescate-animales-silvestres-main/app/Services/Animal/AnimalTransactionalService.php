<?php

namespace Modules\Rescate\Services\Animal;

use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Transfer;
use Illuminate\Http\UploadedFile;
use App\Support\RescateMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalTransactionalService
{
	/**
	 * Crea un Animal y su Hoja de Animal (AnimalFile) en una transacción.
	 *
	 * @param array $animalData Campos para App\Models\Animal
	 * @param array $animalFileData Campos para App\Models\AnimalFile (sin animal_id ni imagen_url)
	 * @param UploadedFile|null $image Archivo de imagen opcional
	 * @return array{animal: Animal, animalFile: AnimalFile}
	 */
	public function createWithFile(array $animalData, array $animalFileData, ?UploadedFile $image = null): array
	{
		DB::beginTransaction();

		$storedPath = null;
		$copiedFromReport = null;
		try {
            // Si viene de un reporte, validar que esté aprobado
            if (!empty($animalData['reporte_id'])) {
                $report = Report::find($animalData['reporte_id']);
                if (!$report) {
                    throw new \DomainException('El reporte especificado no existe.');
                }
                if (!$report->aprobado) {
                    throw new \DomainException('No se puede crear la Hoja de Vida: el hallazgo debe estar aprobado.');
                }
                // Verificar que el reporte no esté ya asignado a otro animal
                $existingAnimal = Animal::where('reporte_id', $animalData['reporte_id'])->first();
                if ($existingAnimal) {
                    throw new \DomainException('Este hallazgo ya está asignado a un animal existente.');
                }
            }

			$animal = Animal::create($animalData);

			if ($image) {
				$storedPath = $image->store('animal-files', 'public');
				$animalFileData['imagen_url'] = $storedPath;
            } elseif (! empty($animalData['reporte_id'])) {
				$rep = Report::find($animalData['reporte_id']);
				if ($rep && $rep->imagen_url && Storage::disk('public')->exists($rep->imagen_url)) {
					$basename = basename($rep->imagen_url);
					$target = 'animal-files/'.uniqid('from_report_').'_'.$basename;
					if (Storage::disk('public')->copy($rep->imagen_url, $target)) {
						$animalFileData['imagen_url'] = $target;
						$copiedFromReport = $target;
					}
				}
			}

			if (empty($animalFileData['imagen_url']) && ! empty($animalFileData['especie_id'])) {
				$speciesPath = RescateMedia::assignSpeciesImage((int) $animalFileData['especie_id']);
				if ($speciesPath) {
					$animalFileData['imagen_url'] = $speciesPath;
				}
			}

            $animalFileData['animal_id'] = $animal->id;

            // Si viene de un reporte con primer traslado, usar ese centro como centro actual (opcional)
            if (!empty($animalData['reporte_id']) && empty($animalFileData['centro_id'])) {
                $firstCenterId = Transfer::where('reporte_id', $animalData['reporte_id'])
                    ->where('primer_traslado', true)
                    ->value('centro_id');
                if ($firstCenterId) {
                    $animalFileData['centro_id'] = $firstCenterId;
                }
            }

            $animalFile = AnimalFile::create($animalFileData);
            $animalFile->load('animalStatus');

			AnimalHistory::recordEvent(
                $animalFile->id,
                'Sin ficha',
                $animalFile->animalStatus?->nombre ?? 'En custodia',
                'Creación de Hoja de Vida',
                null,
                [
                    'animal' => [
                        'id' => $animal->id,
                        'nombre' => $animal->nombre,
                        'sexo' => $animal->sexo,
                    ],
                    'animal_file' => [
                        'id' => $animalFile->id,
                        'estado_id' => $animalFile->estado_id ?? null,
                        'tipo_id' => $animalFile->tipo_id ?? null,
                        'especie_id' => $animalFile->especie_id ?? null,
                        'estado_inicial_id' => $animalData['estado_inicial_id'] ?? null,
                    ],
                ],
            );

            // Reclamar por reporte (auto) si hay report_id
            if (!empty($animalData['reporte_id'])) {
                $reportId = (string)$animalData['reporte_id'];

                // Enlazar por report_id en traslados ya registrados en historial (primer traslado)
                AnimalHistory::whereNull('animal_file_id')
                    ->whereNotNull(AnimalHistory::newValuesColumn())
                    ->whereRaw(AnimalHistory::jsonPath("->'transfer'->>'report_id'").' = ?', [$reportId])
                    ->update(['animal_file_id' => $animalFile->id]);

                AnimalHistory::whereNull('animal_file_id')
                    ->whereNotNull(AnimalHistory::newValuesColumn())
                    ->whereRaw(AnimalHistory::jsonPath("->'report'->>'id'").' = ?', [$reportId])
                    ->update(['animal_file_id' => $animalFile->id]);

                $hasFirstTransferHistory = AnimalHistory::where('animal_file_id', $animalFile->id)
                    ->whereNotNull(AnimalHistory::newValuesColumn())
                    ->whereRaw(AnimalHistory::jsonPath("->'transfer'->>'primer_traslado'")." = 'true'")
                    ->exists();

                if (!$hasFirstTransferHistory) {
                    $firstTransfer = Transfer::where('reporte_id', $reportId)
                        ->where('primer_traslado', true)
                        ->orderBy('id')
                        ->first();

                    if ($firstTransfer) {
                        AnimalHistory::recordEvent(
                            $animalFile->id,
                            'Hallazgo registrado',
                            'En traslado',
                            'Primer traslado desde reporte de hallazgo',
                            null,
                            [
                                'transfer' => [
                                    'id' => $firstTransfer->id,
                                    'persona_id' => $firstTransfer->persona_id,
                                    'reporte_id' => (int) $reportId,
                                    'centro_id' => $firstTransfer->centro_id,
                                    'observaciones' => $firstTransfer->observaciones,
                                    'primer_traslado' => true,
                                    'latitud' => $firstTransfer->latitud,
                                    'longitud' => $firstTransfer->longitud,
                                    'created_at' => $firstTransfer->created_at ? $firstTransfer->created_at->toDateTimeString() : null,
                                ],
                            ],
                            $firstTransfer->created_at,
                        );
                    }
                }
            }

			DB::commit();

			return [
				'animal' => $animal,
				'animalFile' => $animalFile,
			];
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($storedPath) {
				Storage::disk('public')->delete($storedPath);
			}
			if ($copiedFromReport) {
				Storage::disk('public')->delete($copiedFromReport);
			}
			throw $e;
		}
	}

	/**
	 * Crea múltiples Animales y sus Hojas desde un mismo conjunto de datos.
	 *
	 * @param int $count
	 * @param array $animalData
	 * @param array $animalFileData
	 * @param UploadedFile|null $image
	 * @return array<int, array{animal: Animal, animalFile: AnimalFile}>
	 */
	public function createManyWithFile(int $count, array $animalData, array $animalFileData, ?UploadedFile $image = null): array
	{
		if ($count < 1) $count = 1;

		DB::beginTransaction();
		$results = [];
		$firstStored = null;
		$firstCreatedPaths = [];
		try {
			for ($i = 0; $i < $count; $i++) {
				$storedPath = null;
				$animalDataEach = $animalData;
				// Para modo por cada, el arrived_count = 1 en el historial

				$animal = Animal::create($animalDataEach);

				$afData = $animalFileData;
				if ($image) {
					if ($i === 0) {
						$firstStored = $image->store('animal-files', 'public');
						$afData['imagen_url'] = $firstStored;
					} else {
						$copyTo = 'animal-files/'.uniqid('copy_').'_'.basename($firstStored);
						Storage::disk('public')->copy($firstStored, $copyTo);
						$afData['imagen_url'] = $copyTo;
						$firstCreatedPaths[] = $copyTo;
					}
				} elseif (!empty($animalData['reporte_id'])) {
					$rep = Report::find($animalData['reporte_id']);
					if ($rep && $rep->imagen_url && Storage::disk('public')->exists($rep->imagen_url)) {
						$copyTo = 'animal-files/'.uniqid('from_report_').'_'.basename($rep->imagen_url);
						Storage::disk('public')->copy($rep->imagen_url, $copyTo);
						$afData['imagen_url'] = $copyTo;
						$firstCreatedPaths[] = $copyTo;
					}
				}

                $afData['animal_id'] = $animal->id;

                // Si viene de un reporte con primer traslado, usar ese centro como centro actual (opcional)
                if (!empty($animalData['reporte_id']) && empty($afData['centro_id'])) {
                    $firstCenterId = Transfer::where('reporte_id', $animalData['reporte_id'])
                        ->where('primer_traslado', true)
                        ->value('centro_id');
                    if ($firstCenterId) {
                        $afData['centro_id'] = $firstCenterId;
                    }
                }
				$animalFile = AnimalFile::create($afData);
                $animalFile->load('animalStatus');

				AnimalHistory::recordEvent(
                    $animalFile->id,
                    'Sin ficha',
                    $animalFile->animalStatus?->nombre ?? 'En custodia',
                    'Creación de Hoja de Vida',
                    null,
                    [
                        'animal' => [
                            'id' => $animal->id,
                            'nombre' => $animal->nombre,
                            'sexo' => $animal->sexo,
                        ],
                        'animal_file' => [
                            'id' => $animalFile->id,
                            'estado_id' => $animalFile->estado_id ?? null,
                            'tipo_id' => $animalFile->tipo_id ?? null,
                            'especie_id' => $animalFile->especie_id ?? null,
                            'arrived_count' => 1,
                        ],
                    ],
                );

				// Enlazar historiales por reporte si aplica
				if (!empty($animalData['reporte_id'])) {
                    $reportId = (string) $animalData['reporte_id'];
					AnimalHistory::whereNull('animal_file_id')
						->whereNotNull(AnimalHistory::newValuesColumn())
						->whereRaw(AnimalHistory::jsonPath("->'transfer'->>'report_id'").' = ?', [$reportId])
						->update(['animal_file_id' => $animalFile->id]);
					AnimalHistory::whereNull('animal_file_id')
						->whereNotNull(AnimalHistory::newValuesColumn())
						->whereRaw(AnimalHistory::jsonPath("->'report'->>'id'").' = ?', [$reportId])
						->update(['animal_file_id' => $animalFile->id]);
				}

				$results[] = ['animal' => $animal, 'animalFile' => $animalFile];
			}

			DB::commit();
			return $results;
		} catch (\Throwable $e) {
			DB::rollBack();
			if ($firstStored) {
				Storage::disk('public')->delete($firstStored);
			}
			foreach ($firstCreatedPaths as $p) {
				Storage::disk('public')->delete($p);
			}
			throw $e;
		}
	}
}


