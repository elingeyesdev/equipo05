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
                'administrador' => ($i === 0),
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
        $voluntariosIds = $db->table('usuario')->pluck('id_usuario')->toArray();

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

        // 5. Chat Mensajes
        if (Schema::connection('seguimiento')->hasTable('chat_mensajes')) {
            $mensajes = [
                'Brigada Alpha lista para salir a zona de Roboré.',
                'Necesitamos más suministros de agua en el punto de acopio 2.',
                'Confirmado el traslado de personal voluntario vía aérea.',
                'El fuego está controlado en el sector norte, procedemos con guardia de cenizas.',
                'Solicito reporte de situation del equipo en San Matías.',
                'Kit de primeros auxilios entregado con éxito.',
                'Hay un nuevo foco detectado cerca de la comunidad.',
                'Iniciando jornada de capacitación para nuevos voluntarios.'
            ];
            foreach ($mensajes as $mensaje) {
                $db->table('chat_mensajes')->insert([
                    'mensaje' => $mensaje,
                    'created_at' => $now->subMinutes(rand(10, 5000)),
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Seguimiento: Datos demo de voluntarios y solicitudes ampliados significativamente.');
        $this->command?->info('Seguimiento: voluntarios, capacitaciones y actividad demo ampliados.');
    }
}
