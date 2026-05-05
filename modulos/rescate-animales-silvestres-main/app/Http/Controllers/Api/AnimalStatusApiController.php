<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\AnimalStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class AnimalStatusApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            AnimalStatus::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(AnimalStatus $animalStatus): JsonResponse
    {
        return response()->json($animalStatus->only(['id','nombre']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:animal_statuses,nombre'],
        ]);

        $animalStatus = AnimalStatus::create($data);

        return response()->json($animalStatus->only(['id', 'nombre']), 201);
    }

    public function update(Request $request, AnimalStatus $animalStatus): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:animal_statuses,nombre,' . $animalStatus->id],
        ]);

        $animalStatus->update($data);

        return response()->json($animalStatus->only(['id', 'nombre']));
    }

    public function destroy(AnimalStatus $animalStatus): JsonResponse
    {
        $animalStatus->delete();

        return response()->json([], 204);
    }
}