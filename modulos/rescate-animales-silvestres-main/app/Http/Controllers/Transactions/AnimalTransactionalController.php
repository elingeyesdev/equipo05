<?php

namespace Modules\Rescate\Http\Controllers\Transactions;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Requests\Transactions\AnimalWithFileRequest;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Species;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Services\Animal\AnimalTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AnimalTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalTransactionalService $service
	) {
		$this->middleware('auth');
	}

	/**
	 * Formulario combinado para crear Animal + Hoja de Animal en una sola operación.
	 */
	public function index(): RedirectResponse
	{
		return Redirect::route('rescate.animal-files.index');
	}

	public function create(): View
	{
		$animal = new Animal();
		$animalFile = new AnimalFile();

		// Defaults
		$defaultStatusId = AnimalStatus::whereRaw('LOWER(nombre) = ?', ['en recuperación'])->value('id');
        // Default especie "Desconocido" en el formulario de hoja
        $unknownSpeciesId = Species::whereRaw('LOWER(nombre) = ?', ['desconocido'])->value('id');
        if ($unknownSpeciesId && empty($animalFile->especie_id)) {
            $animalFile->especie_id = $unknownSpeciesId;
        }

		$availableReports = Report::query()
			->where('reports.aprobado', 1)
			->whereNotExists(function ($query) {
				$query->select(DB::raw(1))
					->from('animals')
					->whereColumn('animals.reporte_id', 'reports.id');
			})
			->with(['person', 'condicionInicial', 'incidentType'])
			->orderByDesc('reports.id')
			->get();

		$reportCards = $availableReports;
		$reports = $availableReports;

		// Datos requeridos por el form de AnimalFile (salvo animales)
		$species = Species::orderBy('nombre')->get(['id','nombre']);
		$animalStatuses = AnimalStatus::orderBy('nombre')->get(['id','nombre']);

		return view('transactions.animal.create', compact(
			'animal',
			'animalFile',
			'reports',
			'species',
			'animalStatuses',
			'reportCards',
            'defaultStatusId'
		));
	}

	public function show($id): RedirectResponse
	{
		return Redirect::route('rescate.animal-records.create');
	}

	public function edit($id): RedirectResponse
	{
		return Redirect::route('rescate.animal-records.create');
	}

	/**
	 * Persiste Animal + Hoja de Animal (transaccional).
	 */
	public function store(AnimalWithFileRequest $request): RedirectResponse
	{
		try {
			$animalData = $request->only(['nombre','sexo','descripcion','reporte_id','transfer_history_ids','llegaron_cantidad','estado_inicial_id']);
			$animalFileData = $request->only(['tipo_id','especie_id','estado_id']);
			$image = $request->file('imagen');

			$rep = null;
			if (! empty($animalData['reporte_id'])) {
				$rep = Report::with('condicionInicial')->find($animalData['reporte_id']);
			}

			if (empty($animalFileData['estado_id']) && $rep && $rep->condicionInicial?->nombre) {
				$status = AnimalStatus::whereRaw('LOWER(nombre) = ?', [mb_strtolower($rep->condicionInicial->nombre)])->value('id');
				$animalFileData['estado_id'] = $status
					?: AnimalStatus::whereRaw('LOWER(nombre) = ?', ['en atención'])->value('id');
			}

			if (empty($animalData['descripcion']) && $rep && ! empty($rep->observaciones)) {
				$animalData['descripcion'] = $rep->observaciones;
			}

			$result = $this->service->createWithFile($animalData, $animalFileData, $image);
			$animal = $result['animal'];
			$label = $animal->nombre ?: ('Animal #'.$animal->id);

			return Redirect::route('rescate.animal-files.index')
				->with('success', __('Hoja de vida creada para :nombre.', ['nombre' => $label]));
		} catch (\DomainException $e) {
			return Redirect::back()
				->withInput()
				->withErrors(['general' => $e->getMessage()]);
		} catch (\Throwable $e) {
			report($e);

			return Redirect::back()
				->withInput()
				->withErrors(['general' => __('No se pudo registrar la hoja del animal en este momento.')]);
		}
	}
}


