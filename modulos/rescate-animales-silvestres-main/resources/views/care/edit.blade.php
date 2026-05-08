@extends('layouts.app')

@section('title')
{{ __('Update') }} {{ __('Care') }}
@endsection

@section('content_body')
    <section class="content container-fluid page-pad">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} {{ __('Care') }}</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('rescate.cares.update', $care->id) }}"  role="form" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf

                            @include('care.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('partials.page-pad')
@endsection
