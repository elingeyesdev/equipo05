<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichSeguimientoDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('seguimiento')->hasTable('usuario')) {
            return;
        }

        $db = DB::connection('seguimiento');
        $now = Carbon::now();

        // 1. Usuarios / Voluntarios
        $nombres = ['Juan', 'María', 'Pedro', 'Lucía', 'Roberto', 'Elena', 'Carlos', 'Sofía', 'Luis', 'Ana', 'Diego', 'Carmen', 'Jorge', 'Raquel', 'Fernando'];
        $apellidos = ['Pérez', 'González', 'Ramos', 'Vargas', 'Díaz', 'Suárez', 'Flores', 'Mendoza', 'Quispe', 'Mamani', 'Rojas', 'Blanco', 'Torres', 'Sosa', 'Luna'];

        for ($i = 0; $i < 20; $i++) {
            $nombre = $nombres[rand(0, count($nombres) - 1)];
            $apellido = $apellidos[rand(0, count($apellidos) - 1)];
            $email = strtolower($nombre . '.' . $apellido . rand(1, 99) . '@voluntario.bo');

            if ($db->table('usuario')->where('email', $email)->exists()) continue;

            $row = [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'activo' => (bool) rand(0, 1),
                'administrador' => false,
                'created_at' => $now->subDays(rand(1, 60)),
                'updated_at' => $now,
            ];
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'ci')) {
                $row['ci'] = (string) rand(5000000, 9999999);
            }
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'tipo_sangre')) {
                $row['tipo_sangre'] = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'][rand(0, 7)];
            }
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'telefono')) {
                $row['telefono'] = '7'.rand(1000000, 9999999);
            }

            $db->table('usuario')->insert($row);
        }

        // 2. Capacitaciones
        $capacitacionesData = [
            'Primeros auxilios básicos' => 'Curso para aprender a responder en emergencias médicas básicas, RCP y control de hemorragias en el campo.',
            'Manejo de incendios forestales' => 'Capacitación avanzada sobre técnicas de combate de incendios, contrafuegos, herramientas de zapa y seguridad en línea de fuego.',
            'Logística humanitaria' => 'Distribución eficiente de recursos, gestión de almacenes temporales y coordinación de suministros de ayuda.',
            'Comunicación en emergencias' => 'Uso de equipos de radio VHF/UHF, protocolos de radiotransmisión y coordinación de brigadas.',
            'Trabajo en equipo y liderazgo' => 'Formación de brigadas de respuesta rápida, roles de mando en el incidente y toma de decisiones.',
            'Uso de GPS y cartografía' => 'Lectura de mapas topográficos, navegación terrestre con brújula y geolocalización en incendios forestales.',
            'Rescate en estructuras colapsadas' => 'Técnicas de búsqueda, apuntalamiento básico y extracción segura de víctimas en estructuras inestables.',
            'Gestión de refugios temporales' => 'Organización de albergues para evacuados, control de sanidad y registro de damnificados.',
            'Psicología en desastres' => 'Primeros auxilios psicológicos para brigadistas y contención emocional para familias afectadas.',
            'Evaluación de daños y necesidades (EDAN)' => 'Metodología oficial de evaluación de daños en viviendas, cultivos e infraestructura tras un siniestro.',
            'Soporte vital avanzado' => 'Técnicas prehospitalarias avanzadas para traumas severos e intoxicaciones por inhalación de humo.',
            'Manejo de materiales peligrosos' => 'Identificación de sustancias químicas peligrosas, descontaminación y perímetros de seguridad.'
        ];

        foreach ($capacitacionesData as $nombre => $desc) {
            if ($db->table('capacitacion')->where('nombre', $nombre)->exists()) continue;
            $db->table('capacitacion')->insert([
                'nombre' => $nombre,
                'descripcion' => $desc,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Necesidades
        if (Schema::connection('seguimiento')->hasTable('necesidad')) {
            $necesidadesData = [
                'Agua potable' => ['Suministro de agua embotellada para rehidratación de combatientes.', 'Víveres'],
                'Alimentos no perecederos' => ['Raciones secas, enlatados y barras energéticas para brigadas.', 'Víveres'],
                'Transporte de carga' => ['Camioneta 4x4 para transporte de personal y herramientas a zonas inaccesibles.', 'Logística'],
                'Medicamentos traslúcidos' => ['Colirios, analgésicos, gasas y cremas para quemaduras.', 'Salud'],
                'Refugio temporal' => ['Carpas de campaña de rápido armado para brigadas en pernocte.', 'Equipo'],
                'Herramientas de zapa' => ['Macleods, pulaskis, batefuegos y palas para construcción de líneas de defensa.', 'Herramientas'],
                'EPP Forestal' => ['Cascos, antiparras, guantes de cuero y camisas ignífugas.', 'Equipo'],
                'Kit de higiene' => ['Jabón líquido, toallas húmedas y repelente de insectos para brigadistas.', 'Víveres']
            ];

            foreach ($necesidadesData as $nombre => $info) {
                if ($db->table('necesidad')->where('nombre', $nombre)->exists()) continue;
                $db->table('necesidad')->insert([
                    'nombre' => $nombre,
                    'descripcion' => $info[0],
                    'tipo' => $info[1],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Obtener ids de voluntarios para relacionar
        $voluntariosIds = $db->table('usuario')->where('administrador', false)->pluck('id_usuario')->toArray();

        // 3b. Administradores del modulo
        $adminsDemo = [
            ['nombre' => 'Patricia', 'apellido' => 'Vargas', 'email' => 'patricia.vargas@seguimiento.bo', 'ci' => '4567890', 'telefono' => '71234501'],
            ['nombre' => 'Ricardo', 'apellido' => 'Salazar', 'email' => 'ricardo.salazar@seguimiento.bo', 'ci' => '5678901', 'telefono' => '71234502'],
            ['nombre' => 'Gabriela', 'apellido' => 'Condori', 'email' => 'gabriela.condori@seguimiento.bo', 'ci' => '6789012', 'telefono' => '71234503'],
            ['nombre' => 'Hugo', 'apellido' => 'Aguilar', 'email' => 'hugo.aguilar@seguimiento.bo', 'ci' => '7890123', 'telefono' => '71234504'],
        ];
        foreach ($adminsDemo as $admin) {
            if ($db->table('usuario')->where('email', $admin['email'])->exists()) {
                $db->table('usuario')->where('email', $admin['email'])->update([
                    'administrador' => true,
                    'activo' => true,
                    'updated_at' => $now,
                ]);
                continue;
            }
            $row = [
                'nombre' => $admin['nombre'],
                'apellido' => $admin['apellido'],
                'email' => $admin['email'],
                'activo' => true,
                'administrador' => true,
                'created_at' => $now->copy()->subDays(rand(30, 120)),
                'updated_at' => $now,
            ];
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'ci')) {
                $row['ci'] = $admin['ci'];
            }
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'telefono')) {
                $row['telefono'] = $admin['telefono'];
            }
            if (Schema::connection('seguimiento')->hasColumn('usuario', 'tipo_sangre')) {
                $row['tipo_sangre'] = 'O+';
            }
            $db->table('usuario')->insert($row);
        }

        // 3c. Universidades
        $universidadesDemo = [
            ['nombre' => 'Universidad Mayor de San Simón', 'sigla' => 'UMSS', 'ciudad' => 'Cochabamba'],
            ['nombre' => 'Universidad Autónoma Gabriel René Moreno', 'sigla' => 'UAGRM', 'ciudad' => 'Santa Cruz'],
            ['nombre' => 'Universidad Mayor de San Andrés', 'sigla' => 'UMSA', 'ciudad' => 'La Paz'],
            ['nombre' => 'Universidad Católica Boliviana', 'sigla' => 'UCB', 'ciudad' => 'La Paz'],
            ['nombre' => 'Universidad Privada de Santa Cruz de la Sierra', 'sigla' => 'UPSA', 'ciudad' => 'Santa Cruz'],
            ['nombre' => 'Universidad Autónoma Tomás Frías', 'sigla' => 'UATF', 'ciudad' => 'Potosí'],
            ['nombre' => 'Universidad Tecnica de Oruro', 'sigla' => 'UTO', 'ciudad' => 'Oruro'],
            ['nombre' => 'Universidad Autónoma Juan Misael Saracho', 'sigla' => 'UAJMS', 'ciudad' => 'Tarija'],
        ];
        $universidadIds = [];
        if (Schema::connection('seguimiento')->hasTable('universidad')) {
            foreach ($universidadesDemo as $uni) {
                $existing = $db->table('universidad')->where('nombre', $uni['nombre'])->first();
                if ($existing) {
                    $update = ['updated_at' => $now];
                    if (Schema::connection('seguimiento')->hasColumn('universidad', 'sigla')) {
                        $update['sigla'] = $uni['sigla'];
                    }
                    if (Schema::connection('seguimiento')->hasColumn('universidad', 'ciudad')) {
                        $update['ciudad'] = $uni['ciudad'];
                    }
                    $db->table('universidad')->where('id_universidad', $existing->id_universidad)->update($update);
                    $universidadIds[] = $existing->id_universidad;
                    continue;
                }
                $row = [
                    'nombre' => $uni['nombre'],
                    'created_at' => $now->copy()->subDays(rand(60, 200)),
                    'updated_at' => $now,
                ];
                if (Schema::connection('seguimiento')->hasColumn('universidad', 'sigla')) {
                    $row['sigla'] = $uni['sigla'];
                }
                if (Schema::connection('seguimiento')->hasColumn('universidad', 'ciudad')) {
                    $row['ciudad'] = $uni['ciudad'];
                }
                $universidadIds[] = $db->table('universidad')->insertGetId($row, 'id_universidad');
            }
        }

        if ($universidadIds !== [] && Schema::connection('seguimiento')->hasColumn('usuario', 'id_universidad')) {
            $volSinUni = $db->table('usuario')
                ->where('administrador', false)
                ->whereNull('id_universidad')
                ->pluck('id_usuario')
                ->toArray();
            foreach ($volSinUni as $i => $volId) {
                $db->table('usuario')->where('id_usuario', $volId)->update([
                    'id_universidad' => $universidadIds[$i % count($universidadIds)],
                    'updated_at' => $now,
                ]);
            }
        }

        // 3d. Centro de soporte / consultas
        if (Schema::connection('seguimiento')->hasTable('consultas') && count($voluntariosIds) > 0) {
            if ($db->table('consultas')->count() < 5) {
                $consultasDemo = [
                    ['asunto' => 'Problema con acceso al portal', 'descripcion' => 'No puedo ingresar con mi correo registrado desde ayer por la tarde.', 'estado' => 'abierta', 'prioridad' => 'alta'],
                    ['asunto' => 'Actualización de datos personales', 'descripcion' => 'Necesito cambiar mi número de teléfono y tipo de sangre en el perfil.', 'estado' => 'en_proceso', 'prioridad' => 'media'],
                    ['asunto' => 'Certificado de capacitación', 'descripcion' => 'Solicito constancia del curso de primeros auxilios realizado el mes pasado.', 'estado' => 'resuelta', 'prioridad' => 'baja'],
                    ['asunto' => 'Error al registrar participación', 'descripcion' => 'El formulario de participación en brigada no guarda la fecha seleccionada.', 'estado' => 'abierta', 'prioridad' => 'alta'],
                    ['asunto' => 'Consulta sobre turnos de guardia', 'descripcion' => '¿Cómo confirmo mi disponibilidad para el fin de semana en San Matías?', 'estado' => 'en_proceso', 'prioridad' => 'media'],
                    ['asunto' => 'Duplicidad de registro', 'descripcion' => 'Aparezco dos veces en la lista de voluntarios activos con el mismo CI.', 'estado' => 'cerrada', 'prioridad' => 'media'],
                    ['asunto' => 'Solicitud de baja temporal', 'descripcion' => 'Por motivos académicos necesito pausar mi participación por 2 meses.', 'estado' => 'abierta', 'prioridad' => 'baja'],
                    ['asunto' => 'Incidencia en evaluación online', 'descripcion' => 'El enlace del token de evaluación muestra error 404 al abrirlo.', 'estado' => 'resuelta', 'prioridad' => 'alta'],
                ];
                foreach ($consultasDemo as $idx => $consulta) {
                    if ($db->table('consultas')->where('asunto', $consulta['asunto'])->exists()) {
                        continue;
                    }
                    $row = [
                        'asunto' => $consulta['asunto'],
                        'created_at' => $now->copy()->subDays(rand(1, 20))->subHours($idx),
                        'updated_at' => $now,
                    ];
                    if (Schema::connection('seguimiento')->hasColumn('consultas', 'descripcion')) {
                        $row['descripcion'] = $consulta['descripcion'];
                    }
                    if (Schema::connection('seguimiento')->hasColumn('consultas', 'estado')) {
                        $row['estado'] = $consulta['estado'];
                    }
                    if (Schema::connection('seguimiento')->hasColumn('consultas', 'prioridad')) {
                        $row['prioridad'] = $consulta['prioridad'];
                    }
                    if (Schema::connection('seguimiento')->hasColumn('consultas', 'id_usuario')) {
                        $row['id_usuario'] = $voluntariosIds[$idx % count($voluntariosIds)];
                    }
                    $db->table('consultas')->insert($row);
                }
            }
        }

        // 4. Solicitudes de Ayuda
        if (Schema::connection('seguimiento')->hasTable('solicitudes_ayuda') && count($voluntariosIds) > 0) {
            $db->table('solicitudes_ayuda')->delete(); // Limpiar antiguas para evitar datos inconsistentes

            $solicitudesMock = [
                ['tipo' => 'Incendio Forestal', 'prioridad' => 'alto', 'estado' => 'sin responder', 'dir' => 'Comunidad Naranjos, Roboré', 'desc' => 'Fuego descontrolado avanzando hacia las viviendas. Se requiere brigada de ataque rápido.'],
                ['tipo' => 'Emergencia Médica', 'prioridad' => 'alto', 'estado' => 'en progreso', 'dir' => 'Barrio Los Olivos, San Ignacio de Velasco', 'desc' => 'Brigadista con signos de intoxicación por monóxido de carbono e insolación extrema.'],
                ['tipo' => 'Falta de Víveres', 'prioridad' => 'medio', 'estado' => 'respondido', 'dir' => 'Campamento Zona Norte, Concepción', 'desc' => 'Brigada de voluntarios se está quedando sin agua potable y raciones secas de alimentos.'],
                ['tipo' => 'Rescate Animal', 'prioridad' => 'bajo', 'estado' => 'resuelto', 'dir' => 'Carretera a Cotoca KM 12, Santa Cruz', 'desc' => 'Oso perezoso rescatado del fuego forestal con quemaduras leves en extremidades.'],
                ['tipo' => 'Incendio Forestal', 'prioridad' => 'alto', 'estado' => 'sin responder', 'dir' => 'Comunidad El Puente, San Matías', 'desc' => 'Vientos de 40 km/h están reactivando las cenizas. Necesitamos herramientas de zapa urgentes.'],
                ['tipo' => 'Logística', 'prioridad' => 'medio', 'estado' => 'en progreso', 'dir' => 'Serranía de Santiago de Chiquitos', 'desc' => 'Falta de comunicación por radio VHF debido a la topografía. Se requiere repetidora móvil.'],
            ];

            // Coordenadas reales distribuidas alrededor de Santa Cruz / Chiquitania
            $coordenadas = [
                [-17.806776, -63.15749],  // Santa Cruz Centro
                [-17.850000, -63.18000],  // Santa Cruz Sur
                [-17.750000, -63.10000],  // Santa Cruz Este
                [-17.780000, -63.25000],  // Santa Cruz Oeste
                [-17.830000, -63.12000],  // Santa Cruz Sureste
                [-17.720000, -63.16000],  // Santa Cruz Norte
            ];

            foreach ($solicitudesMock as $idx => $mock) {
                $volId = $voluntariosIds[array_rand($voluntariosIds)];
                $coords = $coordenadas[$idx % count($coordenadas)];

                $db->table('solicitudes_ayuda')->insert([
                    'voluntario_id' => $volId,
                    'tipo' => $mock['tipo'],
                    'prioridad' => $mock['prioridad'],
                    'estado' => $mock['estado'],
                    'direccion' => $mock['dir'],
                    'descripcion' => $mock['desc'],
                    'latitud' => $coords[0],
                    'longitud' => $coords[1],
                    'fecha' => $now->copy()->subHours(rand(1, 48)),
                    'created_at' => $now->copy()->subDays(rand(1, 5)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 4b. Evaluacion Tokens
        if (Schema::connection('seguimiento')->hasTable('evaluacion_tokens') && count($voluntariosIds) > 0) {
            $db->table('evaluacion_tokens')->delete();
            
            // 2 Activos
            for ($i = 0; $i < 2; $i++) {
                $db->table('evaluacion_tokens')->insert([
                    'id_voluntario' => $voluntariosIds[$i % count($voluntariosIds)],
                    'token' => \Illuminate\Support\Str::random(40),
                    'usado' => false,
                    'fecha_expiracion' => $now->copy()->addDays(7),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
            // 2 Usados
            for ($i = 2; $i < 4; $i++) {
                $db->table('evaluacion_tokens')->insert([
                    'id_voluntario' => $voluntariosIds[$i % count($voluntariosIds)],
                    'token' => \Illuminate\Support\Str::random(40),
                    'usado' => true,
                    'fecha_expiracion' => $now->copy()->addDays(2),
                    'created_at' => $now->copy()->subDays(5),
                    'updated_at' => $now->copy()->subDays(4)
                ]);
            }
            // 1 Expirado
            $db->table('evaluacion_tokens')->insert([
                'id_voluntario' => $voluntariosIds[4 % count($voluntariosIds)],
                'token' => \Illuminate\Support\Str::random(40),
                'usado' => false,
                'fecha_expiracion' => $now->copy()->subDays(1),
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(8)
            ]);
        }

        // 5. Chat de voluntarios (conversaciones)
        if (Schema::connection('seguimiento')->hasTable('chat_mensajes') && count($voluntariosIds) > 0) {
            $hasConversacion = Schema::connection('seguimiento')->hasColumn('chat_mensajes', 'conversacion_id');
            if (! $hasConversacion || $db->table('chat_mensajes')->whereNotNull('conversacion_id')->count() < 5) {
                if ($hasConversacion) {
                    $db->table('chat_mensajes')->delete();
                }

                $conversaciones = [
                    [
                        'vol_id' => $voluntariosIds[0] ?? null,
                        'mensajes' => [
                            ['tipo' => 'voluntario', 'texto' => 'Buenos días, confirmo asistencia a la brigada de Roboré mañana 06:00.'],
                            ['tipo' => 'coordinador', 'texto' => 'Recibido Patricia. Te asignamos al equipo Alpha con salida desde acopio central.'],
                            ['tipo' => 'voluntario', 'texto' => 'Perfecto, llevo EPP completo y botiquín personal.'],
                        ],
                    ],
                    [
                        'vol_id' => $voluntariosIds[1] ?? null,
                        'mensajes' => [
                            ['tipo' => 'voluntario', 'texto' => 'Coordinación, ¿hay transporte confirmado para San Ignacio?'],
                            ['tipo' => 'coordinador', 'texto' => 'Sí, camioneta 4x4 sale a las 05:30. Punto de encuentro: plaza principal.'],
                        ],
                    ],
                    [
                        'vol_id' => $voluntariosIds[2] ?? null,
                        'mensajes' => [
                            ['tipo' => 'voluntario', 'texto' => 'Reporto llegada al puesto de guardia en Concepción.'],
                            ['tipo' => 'coordinador', 'texto' => 'Gracias. Mantén contacto por radio canal 3.'],
                            ['tipo' => 'voluntario', 'texto' => 'Copy. Sin novedad en el sector asignado.'],
                            ['tipo' => 'coordinador', 'texto' => 'Excelente trabajo. Relevo programado para las 18:00.'],
                        ],
                    ],
                    [
                        'vol_id' => $voluntariosIds[3] ?? null,
                        'mensajes' => [
                            ['tipo' => 'voluntario', 'texto' => 'Necesitamos más agua embotellada en el campamento norte.'],
                            ['tipo' => 'coordinador', 'texto' => 'Logística confirma envío de 2 bidones en la próxima ronda.'],
                        ],
                    ],
                    [
                        'vol_id' => $voluntariosIds[4] ?? null,
                        'mensajes' => [
                            ['tipo' => 'voluntario', 'texto' => '¿A qué hora es la capacitación de hoy?'],
                            ['tipo' => 'coordinador', 'texto' => '15:00 en el aula del centro comunitario. Duración estimada 2 horas.'],
                            ['tipo' => 'voluntario', 'texto' => 'Estaré puntual. Gracias.'],
                        ],
                    ],
                ];

                $minOffset = 10;
                foreach ($conversaciones as $conv) {
                    if (! $conv['vol_id']) {
                        continue;
                    }
                    foreach ($conv['mensajes'] as $j => $msg) {
                        $row = [
                            'mensaje' => $msg['texto'],
                            'created_at' => $now->copy()->subMinutes($minOffset),
                            'updated_at' => $now,
                        ];
                        $minOffset += rand(15, 120);
                        if (Schema::connection('seguimiento')->hasColumn('chat_mensajes', 'id_usuario')) {
                            $row['id_usuario'] = $conv['vol_id'];
                        }
                        if ($hasConversacion) {
                            $row['conversacion_id'] = $conv['vol_id'];
                        }
                        if (Schema::connection('seguimiento')->hasColumn('chat_mensajes', 'remitente_tipo')) {
                            $row['remitente_tipo'] = $msg['tipo'];
                        }
                        $db->table('chat_mensajes')->insert($row);
                    }
                }
            }
        }

        $this->command?->info('Seguimiento: Datos demo de voluntarios y solicitudes ampliados significativamente.');
        $this->command?->info('Seguimiento: administradores, universidades, consultas y chat operativos.');
    }
}
