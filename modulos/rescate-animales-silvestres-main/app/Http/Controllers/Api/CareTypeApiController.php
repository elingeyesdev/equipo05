<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\CareType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class CareTypeApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            CareType::orderBy('nombre')->get(['id','nombre'])
        );
    }
 
    public function show(CareType $careType): JsonResponse
    {
        return response()->json($careType->only(['id','nombre']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:care_types,nombre'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $careType = CareType::create($data);

        return response()->json($careType, 201);
    }

    public function update(Request $request, CareType $careType): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:care_types,nombre,' . $careType->id],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $careType->update($data);

        return response()->json($careType);
    }

    public function destroy(CareType $careType): JsonResponse
    {
        $careType->delete();

        return response()->json([], 204);
    }
}