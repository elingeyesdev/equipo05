<?php

namespace Modules\Rescate\Services\Animal;

use Modules\Rescate\Models\Transfer;
use Modules\Rescate\Models\AnimalFile;
use Illuminate\Support\Facades\DB;
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
}




