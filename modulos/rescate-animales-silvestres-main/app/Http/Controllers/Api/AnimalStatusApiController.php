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
}