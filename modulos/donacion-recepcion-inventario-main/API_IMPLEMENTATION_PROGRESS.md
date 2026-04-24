# Implementación API Laravel - Progreso

## ✅ Completado

### 1. Instalación de Sanctum
- ✅ Ejecutado `php artisan install:api`
- ✅ Sanctum instalado correctamente

### 2. Modelos Actualizados
- ✅ **Usuario**: Agregado `HasApiTokens`, `Authenticatable`, `getAuthPassword()`, `$hidden`
- ✅ **Donante**: Agregado `HasApiTokens`

### 3. Controllers Creados
- ✅ DonanteAuthController (implementado con login)
- ✅ VoluntarioAuthController (implementado con login)
- ✅ DonacionController (creado, pendiente implementación)
- ✅ CampanaController (creado, pendiente)
- ✅ PuntoRecoleccionController (creado, pendiente)
- ✅ AlmacenController (creado, pendiente)
- ✅ EstanteController (creado, pendiente)
- ✅ InventarioController (creado, pendiente)
- ✅ DashboardController (creado, pendiente)
- ✅ SolicitudRecoleccionController (creado, pendiente)
- ✅ ImagenController (creado, pendiente)
- ✅ UserController (creado, pendiente)

## 📝 Siguiente: Implementar Controllers

### DonacionController
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donacione;
use App\Models\DonacionesDinero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DonacionController extends Controller
{
    // Ver código en comentario anterior
}
```

### CampanaController
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campana;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    public function index()
    {
        $campanas = Campana::where('fecha_fin', '>=', now())->get();
        return response()->json($campanas);
    }

    public function show($id)
    {
        $campana = Campana::findOrFail($id);
        return response()->json($campana);
    }
}
```

### AlmacenController
```php
public function index()
{
    $almacenes = Almacene::select('id_almacen', 'nombre', 'direccion', 'latitud', 'longitud')->get();
    return response()->json($almacenes);
}
```

### EstanteController
```php
public function index()
{
    $estantes = Estante::with('almacen')->get();
    return response()->json($estantes);
}

public function getByAlmacen($almacenId)
{
    $estantes = Estante::where('id_almacen', $almacenId)->get();
    return response()->json($estantes);
}
```

## 🔄 Pendiente Implementar

1. InventarioController (endpoints de stock)
2. DashboardController (total donaciones, por mes)
3. SolicitudRecoleccionController
4. ImagenController (upload)
5. UserController (show)
6. Rutas en routes/api.php
7. Actualizar app móvil Flutter

## 📋 Notas Importantes

- La tabla `donantes` NO tiene campo `contraseña_hash` - verificar autenticación
- Revisar si donantes tienen login directo o solo voluntarios
- Configurar storage link: `php artisan storage:link`
