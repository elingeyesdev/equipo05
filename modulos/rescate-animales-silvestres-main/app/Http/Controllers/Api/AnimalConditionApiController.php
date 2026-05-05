<?php

namespace Modules\Rescate\Http\Controllers\Api;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Resources\AnimalConditionResource;
use Modules\Rescate\Models\AnimalCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnimalConditionApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = AnimalCondition::query();

        // Filter by active status if requested
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        $conditions = $query->orderBy('nombre')->get();

        return AnimalConditionResource::collection($conditions);
    }

    /**
     * Display the specified resource.
     */
    public function show(AnimalCondition $animalCondition): AnimalConditionResource
    {
        return new AnimalConditionResource($animalCondition);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): AnimalConditionResource
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:animal_conditions,nombre'],
            'severidad' => ['required', 'integer', 'between:1,5'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $animalCondition = AnimalCondition::create($data);

        return new AnimalConditionResource($animalCondition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnimalCondition $animalCondition): AnimalConditionResource
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:animal_conditions,nombre,' . $animalCondition->id],
            'severidad' => ['required', 'integer', 'between:1,5'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $animalCondition->update($data);

        return new AnimalConditionResource($animalCondition->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnimalCondition $animalCondition): JsonResponse
    {
        $animalCondition->delete();

        return response()->json([], 204);
    }
}

