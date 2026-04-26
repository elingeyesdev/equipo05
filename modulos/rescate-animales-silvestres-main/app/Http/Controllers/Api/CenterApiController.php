<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\Center;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
 
class CenterApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
 
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $centers = Center::orderBy('nombre')
            ->paginate($perPage, ['id','nombre','direccion','latitud','longitud','contacto']);
 
        return response()->json($centers);
    }
 
    public function show(Center $center): JsonResponse
    {
        return response()->json($center->only(['id','nombre','direccion','latitud','longitud','contacto']));
    }
}