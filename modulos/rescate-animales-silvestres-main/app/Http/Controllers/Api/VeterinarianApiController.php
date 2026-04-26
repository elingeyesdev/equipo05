<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\Veterinarian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class VeterinarianApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        $items = Veterinarian::with('person')
            ->orderBy('id')
            ->get(['id','persona_id','especialidad','aprobado']);
        return response()->json($items);
    }
 
    public function show(Veterinarian $veterinarian): JsonResponse
    {
        return response()->json(
            $veterinarian->load('person')
        );
    }
}