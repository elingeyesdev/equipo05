<?php

namespace App\Exports;

use App\Models\TrazabilidadItem;
use App\Models\Campania;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TrazabilidadExport implements FromView, WithStyles, WithEvents
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function view(): View
    {
        $query = TrazabilidadItem::query();

        if (!empty($this->filtros['campaniaid'])) {
            $query->where('campaniaid', $this->filtros['campaniaid']);
        }
        if (!empty($this->filtros['desde'])) {
            $query->whereDate('fecha_donacion', '>=', $this->filtros['desde']);
        }
        if (!empty($this->filtros['hasta'])) {
            $query->whereDate('fecha_donacion', '<=', $this->filtros['hasta']);
        }

        // ✅ JOIN con ext_paquetes para traer datos_gateway local (sin HTTP)
        $items = $query->leftJoin('ext_paquetes', 'trazabilidad_items.codigo_paquete', '=', 'ext_paquetes.codigo_paquete')
            ->select(
                'trazabilidad_items.*',
                'ext_paquetes.datos_gateway as datos_gateway',
                'ext_paquetes.estado as paquete_estado_local',
                'ext_paquetes.fecha_creacion as paquete_fecha_local'
            )
            ->orderBy('almacen_nombre', 'asc')
            ->orderBy('estante_codigo', 'asc')
            ->orderBy('espacio_codigo', 'asc')
            ->orderBy('fecha_donacion', 'desc')
            ->get();

        $grupos = $items->groupBy(function ($item) {
            return $item->almacen_nombre ?: 'SIN UBICACIÓN / EN TRÁNSITO';
        });

        $campaniaNombre = !empty($this->filtros['campaniaid'])
            ? Campania::find($this->filtros['campaniaid'])?->titulo
            : 'Todas las Campañas';

        return view('reportes.trazabilidad.excel', [
            'grupos' => $grupos,
            'campaniaNombre' => $campaniaNombre,
            'totalItems' => $items->count()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // ---------------------------------------------------------
                // 1. ANCHOS DE COLUMNA (Ahora son más columnas)
                // ---------------------------------------------------------
                $sheet->getColumnDimension('A')->setWidth(15); // Código
                $sheet->getColumnDimension('B')->setWidth(30); // Producto
                $sheet->getColumnDimension('C')->setWidth(20); // Categoría
                $sheet->getColumnDimension('D')->setWidth(10); // Talla
                $sheet->getColumnDimension('E')->setWidth(25); // Ubicación
                $sheet->getColumnDimension('F')->setWidth(15); // Cantidad
                $sheet->getColumnDimension('G')->setWidth(25); // Donante
                $sheet->getColumnDimension('H')->setWidth(15); // Estado Item
                $sheet->getColumnDimension('I')->setWidth(15); // Ingreso

                // ✅ NUEVAS (Gateway/Paquete)
                $sheet->getColumnDimension('J')->setWidth(20); // Código Paquete
                $sheet->getColumnDimension('K')->setWidth(18); // Estado Paquete
                $sheet->getColumnDimension('L')->setWidth(20); // Fecha Paquete
                $sheet->getColumnDimension('M')->setWidth(28); // Destino

                // 2. ALINEACIÓN / WRAP (Actualizar rango A:M)
                $sheet->getStyle('A1:M' . $highestRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:M' . $highestRow)->getAlignment()->setWrapText(true);

                // 3. BUSCAR Y PROCESAR SEPARADORES (Actualizar rango A:M)
                for ($i = 1; $i <= $highestRow; $i++) {
                    $cellValue = $sheet->getCell('A' . $i)->getValue();

                    if ($cellValue === '[[SEPARADOR]]') {
                        $sheet->setCellValue('A' . $i, '');
                        $sheet->getRowDimension($i)->setRowHeight(40);

                        $sheet->getStyle("A$i:M$i")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']]
                        ]);
                    }
                }

                // Congelar encabezado (si tu header está en fila 6, se mantiene)
                $sheet->freezePane('A6');
            },
        ];
    }
}
