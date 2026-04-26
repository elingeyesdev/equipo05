<?php

namespace Modules\Rescate\Http\Controllers\Transactions;

use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Http\Requests\Transactions\FeedingProcessRequest;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\FeedingFrequency;
use Modules\Rescate\Models\FeedingPortion;
use Modules\Rescate\Models\FeedingType;
use Modules\Rescate\Services\Animal\AnimalFeedingTransactionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalFeedingTransactionalController extends Controller
{
	public function __construct(
		private readonly AnimalFeedingTransactionalService $service
	) {
		$this->middleware('auth');
	}

	public function create(): View
	{
		$animalFiles = AnimalFile::with(['animal.report.person','animalStatus'])
			->whereDoesntHave('release')
			->orderByDesc('id')
			->get(['id','animal_id','estado_id','imagen_url']);
		$feedingTypeOptions = FeedingType::orderBy('nombre')->pluck('nombre', 'id');
		$feedingFrequencyOptions = FeedingFrequency::orderBy('nombre')->pluck('nombre', 'id');
		$feedingPortionOptions = FeedingPortion::orderBy('cantidad')->get()->mapWithKeys(function ($portion) {
			return [$portion->id => $portion->cantidad.' '.$portion->unidad];
		});

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

		return view('transactions.animal.feeding.create', compact(
			'animalFiles',
			'feedingTypeOptions',
			'feedingFrequencyOptions',
			'feedingPortionOptions',
			'afCards'
		));
	}

	public function store(FeedingProcessRequest $request): RedirectResponse
	{
		$this->service->registerFeeding($request->validated());

		return Redirect::route('rescate.care-feedings.index')
			->with('success', 'Alimentación registrada correctamente.');
	}
}


