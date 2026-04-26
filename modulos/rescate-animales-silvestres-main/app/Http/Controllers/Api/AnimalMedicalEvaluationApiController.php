<?php

namespace Modules\Rescate\Http\Controllers\Api;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Requests\Transactions\MedicalEvaluationProcessRequest;
use Modules\Rescate\Models\MedicalEvaluation;
use Modules\Rescate\Services\Animal\AnimalMedicalEvaluationTransactionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalMedicalEvaluationApiController extends Controller
{
    public function __construct(
        private readonly AnimalMedicalEvaluationTransactionalService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de evaluaciones médicas.
     * Permite filtrar por hoja de animal con ?animal_file_id=ID.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MedicalEvaluation::with([
            'animalFile.animal',
            'treatmentType',
            'veterinarian.person',
        ])->orderByDesc('id');

        if ($request->filled('animal_file_id')) {
            $animalFileId = (int) $request->input('animal_file_id');
            $query->where('animal_file_id', $animalFileId);
        }

        $evaluations = $query->paginate(20);

        return response()->json($evaluations);
    }

    /**
     * Registrar una evaluación médica para una Hoja de Animal.
     * Llama al servicio transaccional que también crea historial y actualiza estado.
     */
    public function store(MedicalEvaluationProcessRequest $request): JsonResponse
    {
        $data = $request->validated();
        $image = $request->file('imagen');

        $result = $this->service->registerEvaluation($data, $image);

        return response()->json([
            'message'     => 'Evaluación médica registrada correctamente.',
            'evaluation'  => $result['evaluation']->load([
                'animalFile.animal',
                'treatmentType',
                'veterinarian.person',
            ]),
            'animalFile'  => $result['animalFile'],
        ], 201);
    }

    /**
     * Detalle de una evaluación médica.
     */
    public function show(MedicalEvaluation $animal_medical_evaluation): JsonResponse
    {
        return response()->json(
            $animal_medical_evaluation->load([
                'animalFile.animal',
                'treatmentType',
                'veterinarian.person',
            ])
        );
    }
}

