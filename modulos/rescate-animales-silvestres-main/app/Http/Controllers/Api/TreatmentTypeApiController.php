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
}