<?php

namespace App\Exports;

use App\Models\Donacion;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents; // <--- INDISPENSABLE
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CierreCajaExport implements FromView, WithStyles, WithEvents // Quitamos ShouldAutoSize para controlar el ancho manualmente
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function view(): View
    {
        $query = Donacion::with(['usuario', 'campania', 'estado', 'asignacionesPivot.asignacion.detalles']);

        // --- TUS FILTROS (Se mantienen igual) ---
        if (!empty($this->filtros['campaniaid'])) $query->where('campaniaid', $this->filtros['campaniaid']);
        if (!empty($this->filtros['desde'])) $query->whereDate('fechadonacion', '>=', $this->filtros['desde']);
        if (!empty($this->filtros['hasta'])) $query->whereDate('fechadonacion', '<=', $this->filtros['hasta']);
        if (!empty($this->filtros['estadoid'])) $query->where('estadoid', $this->filtros['estadoid']);
        if (!empty($this->filtros['tipodonacion'])) $query->where('tipodonacion', $this->filtros['tipodonacion']);
        if (!empty($this->filtros['donante'])) {
            $term = $this->filtros['donante'];
            $query->whereHas('usuario', function($q) use ($term) {
                $q->where('nombre', 'LIKE', "%{$term}%")->orWhere('apellido', 'LIKE', "%{$term}%");
            });
        }
        if (!empty($this->filtros['min_monto'])) $query->where('monto', '>=', $this->filtros['min_monto']);
        if (!empty($this->filtros['max_monto'])) $query->where('monto', '<=', $this->filtros['max_monto']);
        if (isset($this->filtros['esanonima']) && $this->filtros['esanonima'] !== null) {
            $query->where('esanonima', $this->filtros['esanonima']);
        }

        // ORDENAMIENTO (Vital para agrupar)
        $donaciones = $query->orderBy('campaniaid', 'asc')
                            ->orderBy('fechadonacion', 'desc')
                            ->get();

        $grupos = $donaciones->groupBy(function($item) {
            return $item->campania ? $item->campania->titulo : 'GENERAL';
        });

        return view('reportes.cierre_caja_excel', [
            'grupos' => $grupos,
            'totalGeneral' => $donaciones->sum('monto')
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // 1. ANCHOS DE COLUMNA FIJOS (Para evitar los '##')
                $sheet->getColumnDimension('A')->setWidth(10); // ID
                $sheet->getColumnDimension('B')->setWidth(15); // Fecha
                $sheet->getColumnDimension('C')->setWidth(35); // Donante (Ancho para nombres largos)
                $sheet->getColumnDimension('D')->setWidth(15); // Tipo
                $sheet->getColumnDimension('E')->setWidth(15); // Estado
                $sheet->getColumnDimension('F')->setWidth(10); // Priv
                $sheet->getColumnDimension('G')->setWidth(20); // Monto (Suficiente para números grandes)

                // 2. ALINEACIÓN GENERAL (Todo centrado verticalmente)
                $sheet->getStyle('A1:G'.$highestRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // 3. LOGICA DEL SEPARADOR
                for ($i = 1; $i <= $highestRow; $i++) {
                    $cellValue = $sheet->getCell('A' . $i)->getValue();

                    // Buscamos la marca que pusimos en el HTML
                    if ($cellValue === '[[SEPARADOR]]') {
                        $sheet->setCellValue('A' . $i, ''); // Borramos el texto
                        
                        // ALTO DE FILA: 40 puntos (Esto separa visualmente las campañas)
                        $sheet->getRowDimension($i)->setRowHeight(40);
                        
                        // Estilo blanco sin bordes
                        $sheet->getStyle("A$i:G$i")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']]
                        ]);
                    }
                }
                
                // Congelar encabezado
                $sheet->freezePane('A3');
            },
        ];
    }
}