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
}

