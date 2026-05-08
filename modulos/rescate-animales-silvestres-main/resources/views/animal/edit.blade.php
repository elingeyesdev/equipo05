@extends('layouts.app')

@section('title')
{{ __('Update') }} Animal
@endsection

@section('content_body')
    <section class="content container-fluid page-pad">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Animal</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('rescate.animals.update', $animal->id) }}"  role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf

                            @include('animal.form', [
                                'animal' => $animal ?? null,
                                'reports' => $reports ?? [],
                                'animalStatuses' => (\Modules\Rescate\Models\AnimalStatus::orderBy('nombre')->get())
                            ])

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
