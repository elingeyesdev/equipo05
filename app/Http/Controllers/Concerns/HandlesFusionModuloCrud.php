<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait HandlesFusionModuloCrud
{
    abstract protected function moduloConnection(): string;

    abstract protected function moduloRoutePrefix(): string;

    abstract protected function moduloCrudView(): string;

    abstract protected function seccionesConfig(): array;

    protected function getOptionsForColumn(string $column): array
    {
        return [];
    }

    protected function columnsForCrud(string $tabla, string $pk): array
    {
        $connection = $this->moduloConnection();
        $columns = Schema::connection($connection)->getColumnListing($tabla);

        return array_values(array_filter($columns, function ($col) use ($pk) {
            return ! in_array($col, [$pk, 'created_at', 'updated_at', 'deleted_at'], true);
        }));
    }

    protected function normalizeCrudColumns(string $seccion, array $columns): array
    {
        return $columns;
    }

    protected function prepareStoreData(string $tabla, array $data): array
    {
        return $data;
    }

    public function crudCreate(string $seccion): View
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $columns = $this->normalizeCrudColumns($seccion, $this->columnsForCrud($config['tabla'], $config['pk']));
        $options = [];
        foreach ($columns as $column) {
            $options[$column] = $this->getOptionsForColumn($column);
        }

        return view($this->moduloCrudView(), [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'tabla' => $config['tabla'],
            'primaryKey' => $config['pk'],
            'columns' => $columns,
            'options' => $options,
            'registro' => null,
        ]);
    }

    public function crudStore(Request $request, string $seccion): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = $this->moduloConnection();
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $data = $this->prepareStoreData($tabla, collect($request->only($columns))
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray());

        if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
            $data['created_at'] = now();
        }
        if (Schema::connection($connection)->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection($connection)->table($tabla)->insert($data);

        return redirect()->route("{$this->moduloRoutePrefix()}.$seccion")->with('success', 'Registro creado correctamente.');
    }

    public function crudEdit(string $seccion, int $id): View
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $connection = $this->moduloConnection();
        $columns = $this->normalizeCrudColumns($seccion, $this->columnsForCrud($config['tabla'], $config['pk']));
        $options = [];
        foreach ($columns as $column) {
            $options[$column] = $this->getOptionsForColumn($column);
        }

        $registro = DB::connection($connection)
            ->table($config['tabla'])
            ->where($config['pk'], $id)
            ->first();
        abort_unless($registro, 404);

        return view($this->moduloCrudView(), [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'tabla' => $config['tabla'],
            'primaryKey' => $config['pk'],
            'columns' => $columns,
            'options' => $options,
            'registro' => $registro,
        ]);
    }

    public function crudUpdate(Request $request, string $seccion, int $id): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = $this->moduloConnection();
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $data = collect($request->only($columns))
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray();

        if (Schema::connection($connection)->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection($connection)
            ->table($tabla)
            ->where($config['pk'], $id)
            ->update($data);

        return redirect()->route("{$this->moduloRoutePrefix()}.$seccion")->with('success', 'Registro actualizado correctamente.');
    }

    public function crudDestroy(string $seccion, int $id): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        DB::connection($this->moduloConnection())
            ->table($config['tabla'])
            ->where($config['pk'], $id)
            ->delete();

        return redirect()->route("{$this->moduloRoutePrefix()}.$seccion")->with('success', 'Registro eliminado correctamente.');
    }
}
