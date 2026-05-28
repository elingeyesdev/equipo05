<?php

namespace App\Support;

class ModuloCrudEjemplos
{
    public static function placeholder(string $modulo, string $seccion, string $column): string
    {
        $column = strtolower($column);
        $seccion = strtolower($seccion);

        $especificos = [
            'logistica' => [
                'solicitante' => [
                    'nombre' => 'Ejemplo: Maria',
                    'apellido' => 'Ejemplo: Gutierrez',
                    'ci' => 'Ejemplo: 12345678',
                    'telefono' => 'Ejemplo: 70012345',
                    'email' => 'Ejemplo: maria.gutierrez@correo.com',
                ],
                'destino' => [
                    'comunidad' => 'Ejemplo: Comunidad San Juan',
                    'provincia' => 'Ejemplo: Chiquitos',
                    'direccion' => 'Ejemplo: Barrio Central, calle 4',
                ],
                'ubicacion' => [
                    'zona' => 'Ejemplo: Zona Norte - Almacen central',
                    'latitud' => 'Ejemplo: -17.886',
                    'longitud' => 'Ejemplo: -63.755',
                ],
                'vehiculo' => [
                    'placa' => 'Ejemplo: 1234-ABC',
                    'capacidad' => 'Ejemplo: 1500',
                    'anio' => 'Ejemplo: 2020',
                ],
                'conductor' => [
                    'nombre' => 'Ejemplo: Juan',
                    'apellido' => 'Ejemplo: Perez',
                    'ci' => 'Ejemplo: 87654321',
                    'telefono' => 'Ejemplo: 71122334',
                ],
                'marca' => [
                    'nombre_marca' => 'Ejemplo: Toyota',
                    'nombre' => 'Ejemplo: Toyota',
                ],
                'tipo-vehiculo' => [
                    'nombre_tipovehiculo' => 'Ejemplo: Camioneta 4x4',
                    'tipo_vehiculo' => 'Ejemplo: Camioneta',
                ],
                'tipo-emergencia' => [
                    'tipo_emergencia' => 'Ejemplo: Inundacion',
                    'descripcion' => 'Ejemplo: Emergencia por desborde de rio',
                ],
                'tipo-licencia' => [
                    'tipo_licencia' => 'Ejemplo: Licencia profesional C',
                ],
                'estado' => [
                    'nombre_estado' => 'Ejemplo: En transito',
                ],
                'paquete' => [
                    'codigo' => 'Ejemplo: PKG-202605281030',
                    'ubicacion_actual' => 'Ejemplo: Deposito Santa Cruz',
                ],
                'solicitud' => [
                    'codigo_seguimiento' => 'Ejemplo: SOL-202605281030',
                    'tipo_emergencia' => 'Ejemplo: Incendio forestal',
                    'cantidad_personas' => 'Ejemplo: 25',
                    'insumos_necesarios' => 'Ejemplo: Agua, frazadas y alimentos',
                ],
            ],
            'seguimiento' => [
                'voluntarios' => [
                    'nombre' => 'Ejemplo: Carlos',
                    'apellido' => 'Ejemplo: Mamani',
                    'ci' => 'Ejemplo: 11223344',
                    'telefono' => 'Ejemplo: 71234567',
                    'email' => 'Ejemplo: carlos.mamani@correo.com',
                    'carrera' => 'Ejemplo: Ingenieria ambiental',
                ],
                'evaluacion' => [
                    'titulo' => 'Ejemplo: Evaluacion de campo 2026',
                    'descripcion' => 'Ejemplo: Prueba teorica y practica de rescate',
                    'puntaje_minimo' => 'Ejemplo: 70',
                ],
                'capacitaciones' => [
                    'titulo' => 'Ejemplo: Primeros auxilios basicos',
                    'lugar' => 'Ejemplo: Centro comunitario El Torno',
                    'descripcion' => 'Ejemplo: Capacitacion de 8 horas para voluntarios',
                ],
                'necesidades' => [
                    'titulo' => 'Ejemplo: Necesidad de botiquines',
                    'descripcion' => 'Ejemplo: Se requieren 15 botiquines para brigadas',
                    'cantidad' => 'Ejemplo: 15',
                ],
                'universidades' => [
                    'nombre' => 'Ejemplo: Universidad Mayor de San Simon',
                    'sigla' => 'Ejemplo: UMSS',
                    'ciudad' => 'Ejemplo: Cochabamba',
                ],
                'ayudas-solicitadas' => [
                    'descripcion' => 'Ejemplo: Apoyo logistico para evacuados',
                    'ubicacion' => 'Ejemplo: Comunidad Lomerio',
                ],
            ],
            'cuadrillas' => [
                'reportes' => [
                    'titulo' => 'Ejemplo: Reporte de patrulla sector norte',
                    'descripcion' => 'Ejemplo: Recorrido sin novedad en zona boscosa',
                    'ubicacion' => 'Ejemplo: Parque Nacional Noel Kempff',
                ],
                'reportes-incendio' => [
                    'titulo' => 'Ejemplo: Foco activo cerca del rio',
                    'descripcion' => 'Ejemplo: Humo visible a 2 km del puesto',
                    'latitud' => 'Ejemplo: -17.912',
                    'longitud' => 'Ejemplo: -63.201',
                ],
                'equipos' => [
                    'nombre' => 'Ejemplo: Brigada Alpha',
                    'descripcion' => 'Ejemplo: Equipo de respuesta rapida',
                ],
                'recursos' => [
                    'nombre' => 'Ejemplo: Motobomba portatil',
                    'cantidad' => 'Ejemplo: 3',
                    'estado' => 'Ejemplo: Operativo',
                ],
                'noticias' => [
                    'titulo' => 'Ejemplo: Campana de prevencion 2026',
                    'contenido' => 'Ejemplo: Jornada informativa sobre quema controlada',
                ],
                'cursos' => [
                    'nombre' => 'Ejemplo: Manejo de fuego nivel I',
                    'descripcion' => 'Ejemplo: Curso de 40 horas para comunarios',
                    'duracion_horas' => 'Ejemplo: 40',
                ],
                'inscritos' => [
                    'observaciones' => 'Ejemplo: Participante con experiencia previa',
                ],
                'comunarios' => [
                    'nombre' => 'Ejemplo: Pedro',
                    'apellido' => 'Ejemplo: Roca',
                    'ci' => 'Ejemplo: 99887766',
                    'telefono' => 'Ejemplo: 71334455',
                ],
                'usuarios' => [
                    'nombre' => 'Ejemplo: Ana',
                    'apellido' => 'Ejemplo: Flores',
                    'email' => 'Ejemplo: ana.flores@correo.com',
                ],
                'roles' => [
                    'name' => 'Ejemplo: Coordinador de brigada',
                    'nombre' => 'Ejemplo: Coordinador de brigada',
                ],
                'generos' => [
                    'nombre' => 'Ejemplo: Femenino',
                ],
                'tipos-sangre' => [
                    'tipo' => 'Ejemplo: O+',
                    'nombre' => 'Ejemplo: O positivo',
                ],
                'niveles-entrenamiento' => [
                    'nombre' => 'Ejemplo: Basico',
                    'descripcion' => 'Ejemplo: Entrenamiento inicial de 20 horas',
                ],
                'niveles-gravedad' => [
                    'nombre' => 'Ejemplo: Alta',
                    'descripcion' => 'Ejemplo: Riesgo inmediato para la comunidad',
                ],
                'tipos-incidente' => [
                    'nombre' => 'Ejemplo: Quema no controlada',
                ],
                'tipos-recurso' => [
                    'nombre' => 'Ejemplo: Equipo de proteccion personal',
                ],
                'condiciones-climaticas' => [
                    'nombre' => 'Ejemplo: Viento fuerte',
                    'descripcion' => 'Ejemplo: Ráfagas mayores a 40 km/h',
                ],
                'estados-sistema' => [
                    'nombre' => 'Ejemplo: Activo',
                ],
                'kardex' => [
                    'observacion' => 'Ejemplo: Aprobo curso con nota 88',
                ],
            ],
        ];

        if (isset($especificos[$modulo][$seccion][$column])) {
            return $especificos[$modulo][$seccion][$column];
        }

        return match (true) {
            str_contains($column, 'email') => 'Ejemplo: usuario@correo.com',
            str_contains($column, 'telefono') || str_contains($column, 'celular') => 'Ejemplo: 70012345',
            str_contains($column, 'apellido') => 'Ejemplo: Gutierrez',
            str_contains($column, 'nombre') => 'Ejemplo: Juan Perez',
            str_contains($column, 'ci') || str_contains($column, 'cedula') => 'Ejemplo: 12345678',
            str_contains($column, 'codigo') => 'Ejemplo: COD-202605281030',
            str_contains($column, 'comunidad') => 'Ejemplo: Comunidad San Juan',
            str_contains($column, 'provincia') => 'Ejemplo: Santa Cruz',
            str_contains($column, 'direccion') || str_contains($column, 'ubicacion') => 'Ejemplo: Av. Principal #123',
            str_contains($column, 'descripcion') || str_contains($column, 'detalle') => 'Ejemplo: Describa el caso con datos claros',
            str_contains($column, 'titulo') => 'Ejemplo: Registro de apoyo comunitario',
            str_contains($column, 'contenido') || str_contains($column, 'mensaje') => 'Ejemplo: Escriba el detalle del mensaje',
            str_contains($column, 'latitud') => 'Ejemplo: -17.886',
            str_contains($column, 'longitud') => 'Ejemplo: -63.755',
            str_contains($column, 'placa') => 'Ejemplo: 1234-ABC',
            str_contains($column, 'fecha') => 'Ejemplo: 2026-05-28',
            str_contains($column, 'cantidad') => 'Ejemplo: 10',
            str_contains($column, 'estado') => 'Ejemplo: Pendiente',
            str_contains($column, 'tipo') => 'Ejemplo: General',
            str_contains($column, 'puntaje') || str_contains($column, 'nota') => 'Ejemplo: 75',
            default => 'Ejemplo: ingrese ' . str_replace('_', ' ', $column),
        };
    }
}
