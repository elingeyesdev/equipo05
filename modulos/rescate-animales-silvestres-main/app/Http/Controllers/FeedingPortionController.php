<?php

namespace Modules\Rescate\Http\Controllers;

use Modules\Rescate\Models\FeedingPortion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\FeedingPortionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class FeedingPortionController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar porciones de alimentación
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $feedingPortions = FeedingPortion::paginate();

        return view('feeding-portion.index', compact('feedingPortions'))
            ->with('i', ($request->input('page', 1) - 1) * $feedingPortions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $feedingPortion = new FeedingPortion();

        return view('feeding-portion.create', compact('feedingPortion'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeedingPortionRequest $request): RedirectResponse
    {
        FeedingPortion::create($request->validated());

        return Redirect::route('rescate.feeding-portions.index')
            ->with('success', 'Porción de alimentación creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $feedingPortion = FeedingPortion::find($id);

        return view('feeding-portion.show', compact('feedingPortion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $feedingPortion = FeedingPortion::find($id);

        return view('feeding-portion.edit', compact('feedingPortion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeedingPortionRequest $request, FeedingPortion $feedingPortion): RedirectResponse
    {
        $feedingPortion->update($request->validated());

        return Redirect::route('rescate.feeding-portions.index')
            ->with('success', 'Porción de alimentación actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        FeedingPortion::find($id)->delete();

        return Redirect::route('rescate.feeding-portions.index')
            ->with('success', 'Porción de alimentación eliminada correctamente');
    }
}
