<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\TreatmentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class TreatmentTypeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            TreatmentType::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(TreatmentType $treatmentType): JsonResponse
    {
        return response()->json($treatmentType->only(['id','nombre']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:treatment_types,nombre'],
        ]);

        $treatmentType = TreatmentType::create($data);

        return response()->json($treatmentType->only(['id', 'nombre']), 201);
    }

    public function update(Request $request, TreatmentType $treatmentType): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:treatment_types,nombre,' . $treatmentType->id],
        ]);

        $treatmentType->update($data);

        return response()->json($treatmentType->only(['id', 'nombre']));
    }

    public function destroy(TreatmentType $treatmentType): JsonResponse
    {
        $treatmentType->delete();

        return response()->json([], 204);
    }
}