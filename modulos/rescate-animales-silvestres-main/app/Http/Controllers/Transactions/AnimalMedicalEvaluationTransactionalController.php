<?php

namespace Modules\Rescate\Http\Controllers\Transactions;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Requests\Transactions\MedicalEvaluationProcessRequest;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\TreatmentType;
use Modules\Rescate\Models\Veterinarian;
use Modules\Rescate\Services\Animal\AnimalMedicalEvaluationTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalMedicalEvaluationTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalMedicalEvaluationTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with(['animal.report.person','animalStatus'])
            ->leftJoin('releases', 'releases.animal_file_id', '=', 'animal_files.id')
            ->whereNull('releases.animal_file_id')
            ->orderByDesc('animal_files.id')
            ->get(['animal_files.id','animal_files.animal_id','animal_files.estado_id','animal_files.imagen_url']);
		$treatmentTypes = TreatmentType::orderBy('nombre')->get(['id','nombre']);
		$veterinarians = Veterinarian::with('person')->where('aprobado', true)->orderBy('id')->get();
		$statuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);

		// Pre-evaluación: últimas 3 evaluaciones y último historial (no médico)
		$lastDataByAnimalFile = [];
		foreach ($animalFiles as $af) {
			$lastEvals = \Modules\Rescate\Models\MedicalEvaluation::with('treatmentType')
				->where('animal_file_id', $af->id)
				->orderByDesc('fecha')->orderByDesc('id')->limit(3)
				->get(['id','fecha','descripcion','diagnostico','tratamiento_id','tratamiento_texto','peso','temperatura','imagen_url'])
				->map(function ($e) {
					return [
						'id' => $e->id,
						'fecha' => optional($e->fecha)->toDateString(),
						'descripcion' => $e->descripcion,
						'diagnostico' => $e->diagnostico,
						'tratamiento_id' => $e->tratamiento_id,
						'tratamiento_nombre' => $e->treatmentType?->nombre,
						'tratamiento_texto' => $e->tratamiento_texto,
						'peso' => $e->peso,
						'temperatura' => $e->temperatura,
						'imagen_url' => $e->imagen_url,
					];
				});

			$lastNonMedHistory = \Modules\Rescate\Models\AnimalHistory::where('animal_file_id', $af->id)
				->where(function($q){ $q->whereNull('valores_nuevos->evaluacion_medica'); })
				->orderByDesc('changed_at')->orderByDesc('id')->first(['id','changed_at','valores_nuevos','observaciones']);

			$lastDataByAnimalFile[$af->id] = [
				'lastEvaluations' => $lastEvals,
				'lastHistory' => $lastNonMedHistory,
			];
		}

		// Metadatos para JS (evita expresiones complejas dentro de @json en Blade)
		$afMeta = $animalFiles->mapWithKeys(function ($af) {
			return [
				$af->id => [
					'name' => ($af->animal?->nombre ?? ('#' . $af->animal?->id)),
					'status' => $af->animalStatus?->nombre,
					'img' => $af->imagen_url ? asset('storage/' . $af->imagen_url) : null,
				],
			];
		})->toArray();

		// Datos para cards de Paso 1
		$afCards = $animalFiles->map(function ($af) {
			return [
				'id' => $af->id,
				'img' => $af->imagen_url ? asset('storage/'.$af->imagen_url) : null,
				'status' => $af->animalStatus?->nombre,
				'reporter' => $af->animal?->report?->person?->nombre,
				'name' => ($af->animal?->nombre ?? ('#' . $af->animal?->id)),
			];
		})->values()->toArray();

		return view('transactions.animal.medical-evaluation.create', compact(
			'animalFiles',
			'treatmentTypes',
			'veterinarians',
			'statuses',
			'lastDataByAnimalFile',
			'afMeta',
			'afCards'
		));
	}

	public function store(MedicalEvaluationProcessRequest $request): RedirectResponse
	{
		$data = $request->validated();
		$image = $request->file('imagen');

		$this->service->registerEvaluation($data, $image);

		return Redirect::route('medical-evaluations.index')
			->with('success', 'Evaluación médica registrada.');
	}
}




