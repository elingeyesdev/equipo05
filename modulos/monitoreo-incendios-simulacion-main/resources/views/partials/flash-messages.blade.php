@if ($message = Session::get('success'))
    <x-adminlte-alert theme="success" dismissable class="mb-3">
        {{ $message }}
    </x-adminlte-alert>
@endif

@if ($message = Session::get('info'))
    <x-adminlte-alert theme="info" dismissable class="mb-3">
        {{ $message }}
    </x-adminlte-alert>
@endif

@if ($message = Session::get('error'))
    <x-adminlte-alert theme="danger" dismissable class="mb-3">
        {{ $message }}
    </x-adminlte-alert>
@endif
