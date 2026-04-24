<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use App\Services\GatewayService;
use App\Models\Ext\ExtSolicitud;
use App\Models\Ext\ExtVehiculo;
use App\Models\Ext\ExtEspecie;

class TrazabilidadController extends Controller
{
    protected GatewayService $gateway;
    protected string $apiUrl = 'http://gatealas.dasalas.shop/api/gateway';

    public function __construct(GatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    // Dashboard
    public function index()
    {
        // CLAVE: llenar BD local ANTES de mostrar
        $this->gateway->syncListados();

        $solicitudes = ExtSolicitud::orderBy('updated_at', 'desc')->get();
        $vehiculos   = ExtVehiculo::orderBy('updated_at', 'desc')->get();
        $especies    = ExtEspecie::orderBy('updated_at', 'desc')->get();

        return view('gateway.index', compact('solicitudes', 'vehiculos', 'especies'));
    }

    // Opcional: endpoint para botón "Actualizar"
    public function sync()
    {
        $r = $this->gateway->syncListados();
        return redirect()->route('gateway.trazabilidad.index')
            ->with('status', "Sync OK: sol={$r['solicitudes']} veh={$r['vehiculos']} esp={$r['especies']}");
    }

    public function showPaquete($codigo)
    {
        $url = "{$this->apiUrl}/trazabilidad/paquete/{$codigo}";
        $paquete = $this->gateway->obtenerDetalle(
            ExtSolicitud::class,
            $codigo,
            $url
        );

        return view('gateway.show_paquete', compact('paquete'));
    }

    public function showVehiculo($placa)
    {
        $url = "{$this->apiUrl}/trazabilidad/vehiculo/{$placa}";
        $vehiculo = $this->gateway->obtenerDetalle(
            ExtVehiculo::class,
            $placa,
            $url
        );

        return view('gateway.show_vehiculo', compact('vehiculo'));
    }

        public function showEspecie($nombre)
        {
            // Endpoint REAL que sí existe
            $url = "{$this->apiUrl}/trazabilidad/animales/liberados";

            // Guardamos la respuesta en cache local usando ExtEspecie como contenedor
            $especie = $this->gateway->obtenerDetalle(
                ExtEspecie::class,
                $nombre, // identificador local (Ave, etc.)
                $url
            );

            // FILTRAR EN VISTA (o aquí si prefieres)
            return view('gateway.show_especie', compact('especie', 'nombre'));
        }

}
