<?php

namespace Modules\Rescate\Http\Controllers\Api;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Requests\Transactions\CareProcessRequest;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Services\Animal\AnimalCareTransactionalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalCareApiController extends Controller
{
    public function __construct(
        private readonly AnimalCareTransactionalService $service
    ) {
        // Igual que otras APIs de animales: protegido con Sanctum
        $this->middleware('auth:sanctum');
    }

    /**
     * Listado de cuidados registrados.
     * Permite filtrar por hoja de animal con ?animal_file_id=ID.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Care::with(['animalFile.animal', 'careType'])
            ->orderByDesc('id');

        if ($request->filled('animal_file_id')) {
            $animalFileId = (int) $request->input('animal_file_id');
            $query->where('hoja_animal_id', $animalFileId);
        }

        $cares = $query->paginate(20);

        return response()->json($cares);
    }

    /**
     * Registrar un cuidado para una Hoja de Animal.
     * Reutiliza la lógica transaccional que ya registra historial.
     */
    public function store(CareProcessRequest $request): JsonResponse
    {
        $data = $request->validated();
        $image = $request->file('imagen');

        $result = $this->service->registerCare($data, $image);

        return response()->json([
            'message'    => 'Cuidado registrado correctamente.',
            'care'       => $result['care']->load(['animalFile.animal', 'careType']),
            'animalFile' => $result['animalFile'],
        ], 201);
    }

    /**
     * Detalle de un cuidado.
     */
    public function show(Care $animal_care): JsonResponse
    {
        // El parámetro de ruta se llamará animal_care por convención de apiResource
        return response()->json(
            $animal_care->load(['animalFile.animal', 'careType'])
        );
    }
}

