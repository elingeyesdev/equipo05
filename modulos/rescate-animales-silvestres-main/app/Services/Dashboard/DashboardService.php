<?php

namespace Modules\Rescate\Services\Dashboard;

use Modules\Rescate\Models\ContactMessage;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Rescuer;
use Modules\Rescate\Models\Veterinarian;
use Modules\Rescate\Models\Transfer;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\MedicalEvaluation;
use Modules\Rescate\Models\Care;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardService
{
    /**
     * Obtiene todos los datos del dashboard según el rol del usuario
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        $user = Auth::user();
        $data = ['user' => $user];

        // Datos comunes para todos los roles
        $data = array_merge($data, $this->getGeneralStatistics());

        // Panel principal del módulo: KPIs operativos para cualquier usuario autenticado
        $data = array_merge($data, $this->getAdminDashboardData());

        // Datos específicos por rol (vistas secundarias)
        if ($user->hasAnyRole(['admin', 'encargado'])) {
            // reservado para widgets exclusivos de administración si se añaden
        }

        if ($user->hasRole('veterinario')) {
            $data = array_merge($data, $this->getVeterinarianDashboardData($user));
        }

        if ($user->hasRole('rescatista') && $user->person) {
            $data = array_merge($data, $this->getRescuerDashboardData($user));
        }

        return $data;
    }

    /**
     * Obtiene estadísticas generales visibles para todos los roles
     *
     * @return array
     */
    private function getGeneralStatistics(): array
    {
        return DB::transaction(function () {
            return [
                'totalAnimals' => AnimalFile::count(),
                'releasedAnimals' => Release::count(),
                'totalReports' => Report::count(),
                'approvedReports' => Report::where('aprobado', true)->count(),
                'totalTransfers' => Transfer::count(),
            ];
        });
    }

    /**
     * Obtiene datos del dashboard para administradores y encargados
     *
     * @return array
     */
    private function getAdminDashboardData(): array
    {
        return DB::transaction(function () {
            return [
                // Mensajes de contacto no leídos
                'unreadMessages' => $this->getUnreadMessages(),
                'unreadMessagesCount' => $this->getUnreadMessagesCount(),

                // Reportes pendientes de aprobación
                'pendingReports' => $this->getPendingReports(),
                'pendingReportsCount' => $this->getPendingReportsCount(),

                // Solicitudes pendientes
                'pendingRescuers' => $this->getPendingRescuers(),
                'pendingRescuersCount' => $this->getPendingRescuersCount(),

                'pendingVeterinarians' => $this->getPendingVeterinarians(),
                'pendingVeterinariansCount' => $this->getPendingVeterinariansCount(),

                'pendingCaregivers' => $this->getPendingCaregivers(),
                'pendingCaregiversCount' => $this->getPendingCaregiversCount(),

                // Estadísticas para gráficos
                'reportsByMonth' => $this->getReportsByMonth(),
                'transfersByMonth' => $this->getTransfersByMonth(),
                'releasesByMonth' => $this->getReleasesByMonth(),
                'animalFilesByMonth' => $this->getAnimalFilesByMonth(),
                'animalsByStatus' => $this->getAnimalsByStatus(),
                'applicationsByType' => $this->getApplicationsByType(),
                
                // Datos detallados para filtrado por fecha
                'reportsDetailed' => $this->getReportsDetailed(),
                'transfersDetailed' => $this->getTransfersDetailed(),
                'releasesDetailed' => $this->getReleasesDetailed(),
                'animalFilesDetailed' => $this->getAnimalFilesDetailed(),

                // KPIs de Actividad (presente)
                'animalsBeingRescued' => $this->getAnimalsBeingRescued(),
                'animalsBeingTransferred' => $this->getAnimalsBeingTransferred(),
                'animalsBeingTreated' => $this->getAnimalsBeingTreated(),

                // KPIs de Eficacia
                'efficiencyAttendedRescued' => $this->getEfficiencyAttendedRescued(),
                'efficiencyReadyAttended' => $this->getEfficiencyReadyAttended(),

                // KPI de Efectividad
                'effectivenessReleasedRescued' => $this->getEffectivenessReleasedRescued(),

                // Top 5 voluntarios más activos
                'topVolunteers' => $this->getTopVolunteers(5),
            ];
        });
    }

    /**
     * Obtiene datos del dashboard para veterinarios
     *
     * @param \Modules\Rescate\Models\User $user
     * @return array
     */
    private function getVeterinarianDashboardData($user): array
    {
        return DB::transaction(function () use ($user) {
            if (!$user->person) {
                return [
                    'myAnimalFiles' => 0,
                    'recentEvaluations' => 0,
                    'animalsInTreatment' => 0,
                ];
            }

            // Buscar el veterinario asociado a la persona del usuario
            $veterinarian = Veterinarian::where('persona_id', $user->person->id)->first();
            
            if (!$veterinarian) {
                return [
                    'myAnimalFiles' => 0,
                    'recentEvaluations' => 0,
                    'animalsInTreatment' => 0,
                ];
            }

            // Contar hojas de animales únicas que tienen evaluaciones médicas de este veterinario
            $myAnimalFiles = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->whereNotNull('animal_file_id')
                ->distinct('animal_file_id')
                ->count('animal_file_id');

            // Evaluaciones médicas recientes (últimos 7 días)
            $recentEvaluations = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            // Animales en tratamiento actualmente (con evaluaciones médicas sin release)
            $animalsInTreatment = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->whereNotNull('animal_file_id')
                ->whereHas('animalFile', function($query) {
                    $query->whereDoesntHave('release');
                })
                ->distinct('animal_file_id')
                ->count('animal_file_id');

            return [
                'myAnimalFiles' => $myAnimalFiles,
                'recentEvaluations' => $recentEvaluations,
                'animalsInTreatment' => $animalsInTreatment,
            ];
        });
    }

    /**
     * Obtiene datos del dashboard para rescatistas
     *
     * @param \Modules\Rescate\Models\User $user
     * @return array
     */
    private function getRescuerDashboardData($user): array
    {
        return DB::transaction(function () use ($user) {
            if (!$user->person) {
                return [
                    'myTransfers' => 0,
                    'recentTransfers' => 0,
                ];
            }

            // Total de traslados del rescatista
            $myTransfers = Transfer::where('persona_id', $user->person->id)->count();

            // Traslados recientes (últimos 7 días)
            $recentTransfers = Transfer::where('persona_id', $user->person->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            return [
                'myTransfers' => $myTransfers,
                'recentTransfers' => $recentTransfers,
            ];
        });
    }

    /**
     * Obtiene mensajes de contacto no leídos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUnreadMessages()
    {
        return ContactMessage::where('leido', false)
            ->with('user.person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de mensajes no leídos
     *
     * @return int
     */
    private function getUnreadMessagesCount(): int
    {
        return ContactMessage::where('leido', false)->count();
    }

    /**
     * Obtiene reportes pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingReports()
    {
        return Report::where('aprobado', false)
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de reportes pendientes
     *
     * @return int
     */
    private function getPendingReportsCount(): int
    {
        return Report::where('aprobado', false)->count();
    }

    /**
     * Obtiene rescatistas pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingRescuers()
    {
        return Rescuer::whereNull('aprobado')
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de rescatistas pendientes
     *
     * @return int
     */
    private function getPendingRescuersCount(): int
    {
        return Rescuer::whereNull('aprobado')->count();
    }

    /**
     * Obtiene veterinarios pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingVeterinarians()
    {
        return Veterinarian::whereNull('aprobado')
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de veterinarios pendientes
     *
     * @return int
     */
    private function getPendingVeterinariansCount(): int
    {
        return Veterinarian::whereNull('aprobado')->count();
    }

    /**
     * Obtiene cuidadores pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingCaregivers()
    {
        return Person::where('es_cuidador', true)
            ->whereNull('cuidador_motivo_revision')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de cuidadores pendientes
     *
     * @return int
     */
    private function getPendingCaregiversCount(): int
    {
        return Person::where('es_cuidador', true)
            ->whereNull('cuidador_motivo_revision')
            ->count();
    }

    /**
     * Obtiene reportes agrupados por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getReportsByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            return Report::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            // MySQL/MariaDB
            return Report::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    /**
     * Obtiene traslados agrupados por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getTransfersByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return Transfer::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            return Transfer::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    /**
     * Obtiene liberaciones agrupadas por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getReleasesByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return Release::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            return Release::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    /**
     * Obtiene hojas de animales (AnimalFile) agrupadas por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getAnimalFilesByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return AnimalFile::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            return AnimalFile::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    /**
     * Obtiene animales agrupados por estado
     *
     * @return array
     */
    private function getAnimalsByStatus(): array
    {
        return AnimalFile::join('animal_statuses', 'animal_files.estado_id', '=', 'animal_statuses.id')
            ->select('animal_statuses.nombre', DB::raw('COUNT(*) as count'))
            ->groupBy('animal_statuses.nombre')
            ->get()
            ->pluck('count', 'nombre')
            ->toArray();
    }

    /**
     * Obtiene solicitudes agrupadas por tipo
     *
     * @return array
     */
    private function getApplicationsByType(): array
    {
        return [
            'Rescatistas' => Rescuer::count(),
            'Veterinarios' => Veterinarian::count(),
            'Cuidadores' => Person::where('es_cuidador', true)->count(),
        ];
    }

    /**
     * Obtiene reportes con fechas detalladas para filtrado
     *
     * @return array
     */
    private function getReportsDetailed(): array
    {
        return Report::select('created_at')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at')
            ->get()
            ->map(function ($report) {
                return $report->created_at->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Obtiene traslados con fechas detalladas para filtrado
     *
     * @return array
     */
    private function getTransfersDetailed(): array
    {
        return Transfer::select('created_at')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at')
            ->get()
            ->map(function ($transfer) {
                return $transfer->created_at->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Obtiene liberaciones con fechas detalladas para filtrado
     *
     * @return array
     */
    private function getReleasesDetailed(): array
    {
        return Release::select('created_at')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at')
            ->get()
            ->map(function ($release) {
                return $release->created_at->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Obtiene hojas de animales con fechas detalladas para filtrado
     *
     * @return array
     */
    private function getAnimalFilesDetailed(): array
    {
        return AnimalFile::select('created_at')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderBy('created_at')
            ->get()
            ->map(function ($animalFile) {
                return $animalFile->created_at->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * KPIs DE ACTIVIDAD (Presente)
     */

    /**
     * Cantidad de animales que están siendo rescatados
     * (AnimalFiles creados en los últimos 7 días sin release)
     *
     * @return int
     */
    private function getAnimalsBeingRescued(): int
    {
        return AnimalFile::whereDoesntHave('release')->count();
    }

    /**
     * Cantidad de animales que están siendo trasladados
     * (Transfers creados en los últimos 7 días donde el animal no tiene release)
     *
     * @return int
     */
    private function getAnimalsBeingTransferred(): int
    {
        $animalIds = Transfer::whereNotNull('animal_id')->pluck('animal_id')->unique();

        $fromReports = Transfer::whereNotNull('reporte_id')
            ->whereNull('animal_id')
            ->pluck('reporte_id')
            ->unique()
            ->flatMap(fn ($reportId) => Animal::where('reporte_id', $reportId)->pluck('id'));

        $allAnimalIds = $animalIds->merge($fromReports)->unique()->filter();

        if ($allAnimalIds->isEmpty()) {
            return 0;
        }

        return AnimalFile::whereIn('animal_id', $allAnimalIds)
            ->whereDoesntHave('release')
            ->count();
    }

    /**
     * Cantidad de animales que están siendo tratados
     * (AnimalFiles con MedicalEvaluation o Care en los últimos 7 días sin release)
     *
     * @return int
     */
    private function getAnimalsBeingTreated(): int
    {
        $withEvaluation = MedicalEvaluation::whereNotNull('animal_file_id')->pluck('animal_file_id');
        $withCare = Care::whereNotNull('hoja_animal_id')->pluck('hoja_animal_id');
        $fileIds = $withEvaluation->merge($withCare)->unique()->filter();

        if ($fileIds->isEmpty()) {
            return 0;
        }

        return AnimalFile::whereIn('id', $fileIds)
            ->whereDoesntHave('release')
            ->count();
    }

    /**
     * KPIs DE EFICACIA
     */

    /**
     * Eficacia: Cantidad de animales atendidos / cantidad de animales rescatados
     * Atendidos = animales que ya tienen hoja de vida, primer traslado o algo más aparte del hallazgo aprobado
     * Rescatados = hallazgos aprobados (reports aprobados)
     *
     * @return array ['attended' => int, 'rescued' => int, 'percentage' => float]
     */
    private function getEfficiencyAttendedRescued(): array
    {
        // Total de hallazgos aprobados (rescatados)
        $totalRescued = Report::where('aprobado', true)->count();
        
        // Animales atendidos = aquellos que tienen:
        // 1. Hoja de vida (AnimalFile) O
        // 2. Primer traslado (Transfer con primer_traslado=true) O
        // 3. Cualquier otra actividad más allá del hallazgo aprobado
        
        // Contar reportes aprobados que tienen al menos una de estas actividades:
        $reportsWithAnimalFiles = Report::where('aprobado', true)
            ->whereHas('animalFiles')
            ->pluck('id');
        
        $reportsWithFirstTransfer = Report::where('aprobado', true)
            ->whereHas('transfers', function($query) {
                $query->where('primer_traslado', true);
            })
            ->pluck('id');
        
        // Combinar reportes que tienen al menos una actividad
        $attendedReportIds = $reportsWithAnimalFiles->merge($reportsWithFirstTransfer)->unique();
        $attended = $attendedReportIds->count();
        
        $percentage = $totalRescued > 0 ? round(($attended / $totalRescued) * 100, 2) : 0;
        
        return [
            'attended' => $attended,
            'rescued' => $totalRescued,
            'percentage' => $percentage,
        ];
    }

    /**
     * Eficacia: Cantidad de animales listos para liberar / cantidad de animales siendo atendidos
     * Listos = animales con estado "Estable"
     * En Atención = cualquier otro animal que ya está en el sistema (ya rescatado y actualmente siendo tratado)
     *
     * @return array ['ready' => int, 'attended' => int, 'percentage' => float]
     */
    private function getEfficiencyReadyAttended(): array
    {
        // Obtener ID del estado "Estable"
        $estableStatusId = \Modules\Rescate\Models\AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        
        // Si no existe "Estable", buscar por LIKE
        if (!$estableStatusId) {
            $estableStatusId = \Modules\Rescate\Models\AnimalStatus::whereRaw('LOWER(nombre) LIKE ?', ['%estable%'])->value('id');
        }
        
        // Animales en atención = todos los animales en el sistema (sin release)
        $attended = AnimalFile::whereDoesntHave('release')->count();
        
        // Animales listos (con estado "Estable" y sin release)
        $ready = 0;
        if ($estableStatusId) {
            $ready = AnimalFile::where('estado_id', $estableStatusId)
                ->whereDoesntHave('release')
                ->count();
        }
        
        $percentage = $attended > 0 ? round(($ready / $attended) * 100, 2) : 0;
        
        return [
            'ready' => $ready,
            'attended' => $attended,
            'percentage' => $percentage,
        ];
    }

    /**
     * KPIs DE EFECTIVIDAD
     */

    /**
     * Efectividad: Cantidad de animales liberados / cantidad de animales rescatados
     * (Releases / Total AnimalFiles)
     *
     * @return array ['released' => int, 'rescued' => int, 'percentage' => float]
     */
    private function getEffectivenessReleasedRescued(): array
    {
        $released = Release::count();
        $rescued = AnimalFile::count();
        
        $percentage = $rescued > 0 ? round(($released / $rescued) * 100, 2) : 0;
        
        return [
            'released' => $released,
            'rescued' => $rescued,
            'percentage' => $percentage,
        ];
    }

    /**
     * Obtiene el Top N de voluntarios más activos
     * Excluye admin, encargado y actualizaciones de perfiles
     * Cuenta: reportes, traslados, evaluaciones médicas
     * 
     * @param int $limit
     * @return array
     */
    private function getTopVolunteers(int $limit = 5): array
    {
        $userKey = \Modules\Rescate\Models\User::relationKey();

        $adminUserIds = \Modules\Rescate\Models\User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'encargado']);
        })->pluck($userKey);

        $adminPersonIds = Person::whereIn('usuario_id', $adminUserIds)->pluck('id');

        $people = Person::whereNotNull('usuario_id')
            ->whereNotIn('usuario_id', $adminUserIds)
            ->whereNotIn('id', $adminPersonIds)
            ->with('user')
            ->get();

        $ranking = [];

        foreach ($people as $person) {
            $reports = Report::where('persona_id', $person->id)->count();
            $transfers = Transfer::where('persona_id', $person->id)->count();
            $evaluations = MedicalEvaluation::whereHas('veterinarian', function ($q) use ($person) {
                $q->where('persona_id', $person->id);
            })->count();

            $total = $reports + $transfers + $evaluations;
            if ($total === 0) {
                continue;
            }

            $ranking[] = [
                'user_id' => $person->usuario_id,
                'person_id' => $person->id,
                'nombre' => $person->nombre ?? 'Sin nombre',
                'email' => $person->user?->email ?? '',
                'total' => $total,
                'reports' => $reports,
                'transfers' => $transfers,
                'evaluations' => $evaluations,
            ];
        }

        usort($ranking, fn ($a, $b) => $b['total'] <=> $a['total']);

        return array_slice($ranking, 0, $limit);
    }
}

