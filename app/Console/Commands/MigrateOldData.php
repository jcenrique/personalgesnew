<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('app:migrate-old-data')]
#[Description('Command description')]
class MigrateOldData extends Command
{
    // El comando que ejecutarás en la terminal
    protected $signature = 'app:migrate-old-data';

    // La descripción de la tarea
    protected $description = 'Migra y transforma los datos de la antigua DB personalges a la nueva DB';

    public function handle()
    {
        $this->info('Iniciando la migración de datos...');

        // 1. Ejemplo: Migrar la tabla 'agentes', zona, residencias, cargos y roles
        // $this->migrateAgentes();

        // 2 Migracion dias adicionales

        // $this->migrateDiasAdicionales();

        // migracion disponiblidades
        // $this->migrateDisponibilidades();

        // migrar sabados

        // $this->migrateSabados();

        // migrar computos
        // $this->migrateComputos();

        // migrar reconocimientos
        // $this->migrateReconocimientos();

        // migrar cursos
        // $this->migrateCursos();

        // migrar dias pedidos por la empresa de la tabla de personalges dias_pedidos_empresa a las tabla de laravel companydays, para ello hay que buscar en la tabla de personalges los dias pedidos por la empresa y relacionarlos con los usuarios de la tabla users de laravel, para ello hay que buscar en la tabla de personalges los usuarios que tienen el mismo codigo_agente que los usuarios de la tabla users de laravel y relacionarlos con los dias pedidos por la empresa, además hay que buscar en la tabla de personalges los dias pedidos por la empresa que tienen el mismo codigo_agente que los usuarios de la tabla users de laravel y relacionarlos con los dias pedidos por la empresa, para ello hay que buscar en la tabla de personalges los usuarios que tienen el mismo codigo_agente que los usuarios de la tabla users de laravel y relacionarlos con los dias pedidos por la empresa, además hay que buscar en la tabla de personalges los dias pedidos por la empresa que tienen el mismo codigo_agente que los usuarios de la tabla users de laravel y relacionarlos con los dias pedidos por la empresa
        $this->migrateDiasPedidosEmpresa();
        // Puedes añadir aquí más métodos para otras tablas (clientes, facturas, etc.)

        $this->info('¡Migración completada con éxito!');
    }

    private function migrateAgentes()
    {
        // Cogemos los datos de la vieja base de datos
        // Unimos 'users' con 'agentes' usando un leftJoin para no perder usuarios que no sean agentes
        $viejosUsuarios = DB::connection('old_db')->table('users')
            ->leftJoin('agentes', 'agentes.codigo_agente', '=', 'users.codigo_agente') // <-- Adapta las claves de tu relación aquí
            ->leftJoin('residencias', 'residencias.id', '=', 'agentes.residencia_id') // <-- Adapta las claves de tu relación aquí
            ->leftJoin('puestos_gestion', 'puestos_gestion.id', '=', 'residencias.puesto_id')
            ->leftJoin('cargos', 'cargos.id', '=', 'agentes.cargo_id')
            ->select(

                'users.email as usuario_email',
                'users.password as usuario_password',
                'users.firts_name as user_name',
                'users.codigo_agente as usuario_codigo_agente',
                'agentes.status as agente_activo',
                'cargos.cargo as cargo',
                'residencias.residencia as residencia',
                'puestos_gestion.puesto as puesto'

            )
            ->get();
        if ($viejosUsuarios->isEmpty()) {
            $this->warn('No se encontraron agentes en personalges.');

            return;
        }

        // --- MODO VISTA PREVIA ---
        $this->info('=== VISTA PREVIA DE LA FUSIÓN DE TABLAS (Primeros 5 registros) ===');

        $headers = ['Codigo', 'Email (De Users)', 'Nombre Completo (De Agentes u Origen)', 'Activo', 'Residencia', 'Puesto', 'Cargo'];
        $rows = [];

        foreach ($viejosUsuarios->take(15) as $viejo) {
            // Determinamos si el registro actual tenía datos de agente asociado

            // Lógica de transformación combinada

            $rows[] = [
                $viejo->usuario_codigo_agente,
                $viejo->usuario_email,
                $viejo->user_name,
                $viejo->agente_activo,
                $viejo->residencia,
                $viejo->puesto,
                $viejo->cargo,
            ];
        }

        $this->table($headers, $rows);

        if (! $this->confirm('¿La estructura combinada se ve correcta? ¿Procedemos?', false)) {
            $this->warn('Migración cancelada.');

            return;
        }

        // --- MIGRACIÓN REAL ---
        $this->info('Insertando usuarios unificados en la nueva DB...');
        $bar = $this->output->createProgressBar($viejosUsuarios->count());
        $bar->start();

        // $headers = ['Codigo', 'Email (De Users)', 'pass', 'name', 'status', 'locale', 'domain', 'creado', 'email verificado'];
        $datosNuevos = [];

        foreach ($viejosUsuarios as $viejo) {
            if ($viejo->agente_activo != 2) {
                //  Preparamos el mapeo final unificando ambos mundos
                $datosNuevos = [
                    'codigo_agente' => $viejo->usuario_codigo_agente ?? null,
                    'email' => $viejo->usuario_email,
                    'password' => $viejo->usuario_password,
                    'name' => $viejo->user_name,
                    'status' => $viejo->agente_activo ? 1 : 0,
                    'locale' => 'es',
                    'domain' => 'ets-rfv.eus',
                    'created_at' => now(),
                    'email_verified_at' => now(),
                ];
            } else {
                // No hay datos mapeados para este registro, lo saltamos
                $bar->advance();

                continue;
            }

            // Intentamos insertar, usando insertOrIgnore para evitar excepción por clave duplicada
            try {
                $inserted = DB::connection('mysql')->table('users')->insertOrIgnore($datosNuevos);
                if ($inserted === 0) {
                    $this->warn(sprintf('Registro ignorado (posible duplicado) -> email: %s, codigo_agente: %s', $viejo->usuario_email, $viejo->usuario_codigo_agente));
                } else {
                    // hay que buscar la residencia del nuevo usuario en la DB personalges y si no existe la residencia se crea una nueva residencia en la DB laravel y se asigna al nuevo usuario, además de buscar la zona en personalges con el puesto del agente y asignar esa zona al nuevo usuario en laravel
                    // con la residencia se busca la zona en personalges que equivale a la tabla puestos gestion (P.M. ATXURI => zona BIZKAIA, P.M. DONOSTIA => zona GIPUZKOA) y se asigna la zona al nuevo usuario en la DB laravel
                    $residencia = DB::connection('mysql')->table('residencias')->where('name', $viejo->residencia)->first();
                    // si el usuario no tiene residencia asignada en personalges no se hace nada y se omite el attach en la tabla residencia_user y user_zona
                    if (! $viejo->residencia) {
                        // asignar el rol de admin
                        DB::connection('mysql')->table('model_has_roles')->insert([
                            'role_id' => 2, // Asignamos el rol de 'agente' por defecto para cargos desconocidos
                            'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                            'model_type' => 'App\Models\User',
                        ]);

                        continue;
                    }

                    if (! $residencia) {

                        $nuevaResidenciaId = DB::connection('mysql')->table('residencias')->insertGetId([
                            'name' => $viejo->residencia,
                            'zona_id' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {

                        $nuevaResidenciaId = $residencia->id;
                    }
                    $zona = DB::connection('old_db')->table('puestos_gestion')->where('puesto', $viejo->puesto)->value('puesto');
                    $datosNuevos['residencia_id'] = $nuevaResidenciaId;

                    // attach en tabla residencia_user
                    DB::connection('mysql')->table('residencia_user')->insert([
                        'residencia_id' => $nuevaResidenciaId,
                        'user_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // attach en tabla user_zona
                    DB::connection('mysql')->table('user_zona')->insert([
                        'zona_id' => $zona == 'P.M. ATXURI' ? 2 : 1,
                        'user_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // el cargo de la antigua DB de personalges se asimila al rol del nuevo usuario en la DB laravel, si el cargo es 'Jefe de Equipo' se asigna el rol de 'jefe_equipo' y si el cargo es 'Agente' se asigna el rol de 'agente'
                    switch ($viejo->cargo) {
                        case 'JEFE DE SERVICIO':
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 3,
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',

                            ]);
                            break;
                        case 'TECNICO DE P.M.':
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 4,
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',
                            ]);
                            break;
                        case 'TECNICO RED':
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 6,
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',
                            ]);
                            break;
                        case 'TECNICO DE RED HABILITADO':
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 7,
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',
                            ]);
                            break;
                        case 'TECNICO DE P.M. INTEGRAL':
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 5,
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',
                            ]);
                            break;

                        default:
                            // Si el cargo no coincide con ninguno de los casos anteriores, SE ASIGNA ADMIN
                            DB::connection('mysql')->table('model_has_roles')->insert([
                                'role_id' => 2, // Asignamos el rol de 'agente' por defecto para cargos desconocidos
                                'model_id' => DB::connection('mysql')->table('users')->where('email', $viejo->usuario_email)->value('id'),
                                'model_type' => 'App\Models\User',
                            ]);
                    }
                }
            } catch (QueryException $e) {
                $this->error(sprintf('Error al insertar -> email: %s | SQL: %s', $viejo->usuario_email, $e->getMessage()));
            }

            $bar->advance();
        }

        // $this->table($headers, $datosNuevos);

        $bar->finish();
        $this->newLine(2);
    }

    // funcion para cargar los dias adicionales de la tabla dia_adicional_vacaciones a la tabla additionaldays   de cada usuario
    private function migrateDiasAdicionales()
    {
        $this->info('Migrando días adicionales de vacaciones...');

        // Obtenemos los datos de la tabla dia_adicional_vacaciones de la antigua DB
        $diasAdicionales = DB::connection('old_db')->table('dia_adicional_vacaciones')->get();

        if ($diasAdicionales->isEmpty()) {
            $this->warn('No se encontraron días adicionales de vacaciones en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($diasAdicionales->count());
        $bar->start();

        foreach ($diasAdicionales as $dia) {
            // Buscamos el usuario correspondiente en la nueva DB usando el email
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $dia->codigo_agente)->value('id');

            if ($userId) {
                // Insertamos el día adicional en la tabla additionaldays
                try {
                    $diaadcional_id = DB::connection('mysql')->table('additionaldays')->insertGetId([
                        'user_id' => $userId,
                        'year' => $dia->year, // Asegúrate de que el formato de fecha sea compatible

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // insertar el dia de disfrute de la tabla dia_adicional_vacaciones de personalges en la tabla de disfrutes de laravel, con el id del día adicional que acabamos de insertar y la fecha de disfrute
                    // la tabla disfrutes es una tabla morph que tiene un campo para el modelo y otro para el id del model
                    if ($dia->fecha_disfrute) {

                        DB::connection('mysql')->table('disfrutes')->insert([
                            'user_id' => $userId,
                            'disfrutable_id' => $diaadcional_id,
                            'disfrutable_type' => 'App\Models\Additionalday',
                            'status' => 'aprobado',
                            'fecha_disfrute' => $dia->fecha_disfrute, // Asegúrate de que el formato de fecha sea compatible

                        ]);
                    } else {
                        // si el año es inferior al año actual  se crea  un disfrute con fecha de disfrute el 31 de diciembre del año correspondiente al día adicional, para que el día adicional se pueda disfrutar aunque no tenga una fecha de disfrute asignada en personalges
                        if ($dia->year < now()->year) {
                            DB::connection('mysql')->table('disfrutes')->insert([
                                'user_id' => $userId,
                                'disfrutable_id' => $diaadcional_id,
                                'disfrutable_type' => 'App\Models\Additionalday',
                                'status' => 'aprobado',
                                // como el dia no puede estar duplicado hay que generar una fecha de disfrute única, para ello se puede usar el año del día adicional y el id del día adicional para generar una fecha de disfrute única, por ejemplo: 31 de diciembre del año del día adicional más el id del día adicional en días, para que no haya fechas de disfrute duplicadas en caso de que haya varios días adicionales sin fecha de disfrute en el mismo año
                                'fecha_disfrute' => now()->setDate(2000, 12, 31)->addDays($diaadcional_id),
                            ]);
                        }
                    }
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar día adicional -> email: %s | SQL: %s', $dia->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para email: %s. Día adicional no migrado.', $dia->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de días adicionales completada.');
    }

    // migrar las disponibilidades solicitadas de la db personalges a la tabla laravel
    private function migrateDisponibilidades()
    {
        $this->info('Migrando disponibilidades solicitadas...');

        // Obtenemos los datos de la tabla disponibilidades_solicitadas de la antigua DB
        $disponibilidades = DB::connection('old_db')->table('disponibilidad')->get();

        if ($disponibilidades->isEmpty()) {
            $this->warn('No se encontraron disponibilidades solicitadas en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($disponibilidades->count());
        $bar->start();

        foreach ($disponibilidades as $disp) {
            // Buscamos el usuario correspondiente en la nueva DB usando el codigo de agente
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $disp->codigo_agente)->value('id');

            if ($userId) {
                // Insertamos la disponibilidad solicitada en la tabla disponibilidades
                try {
                    DB::connection('mysql')->table('disponibilidades')->insert([
                        'user_id' => $userId,
                        'year' => $disp->year,
                        'fecha' => $disp->fecha, // Asegúrate de que el formato de fecha sea compatible
                        'razon' => $disp->observacion,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar disponibilidad -> email: %s | SQL: %s', $disp->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para email: %s. Disponibilidad no migrada.', $disp->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de disponibilidades completada.');
    }

    // funcion para cargar los dias adicionales de la tabla sabados_trabajados a la tabla sabados   de cada usuario
    private function migrateSabados()
    {
        $this->info('Migrando Sabados...');

        // Obtenemos los datos de la tabla dia_adicional_vacaciones de la antigua DB
        $sabados = DB::connection('old_db')->table('sabados_trabajados')->get();

        if ($sabados->isEmpty()) {
            $this->warn('No se encontraron sábados en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($sabados->count());
        $bar->start();

        foreach ($sabados as $sabado) {
            // Buscamos el usuario correspondiente en la nueva DB usando el email
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $sabado->codigo_agente)->value('id');

            if ($userId) {
                // Insertamos el día adicional en la tabla additionaldays
                try {
                    $sabado_id = DB::connection('mysql')->table('sabados')->insertGetId([
                        'user_id' => $userId,
                        'sabado_trabajado' => $sabado->sabado_trabajado, // Asegúrate de que el formato de fecha sea compatible

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // insertar el dia de disfrute de la tabla dia_adicional_vacaciones de personalges en la tabla de disfrutes de laravel, con el id del día adicional que acabamos de insertar y la fecha de disfrute
                    // la tabla disfrutes es una tabla morph que tiene un campo para el modelo y otro para el id del model
                    if ($sabado->fecha_devolucion) {

                        DB::connection('mysql')->table('disfrutes')->insert([
                            'user_id' => $userId,
                            'disfrutable_id' => $sabado_id,
                            'disfrutable_type' => 'App\Models\Sabado',
                            'status' => 'aprobado',
                            'fecha_disfrute' => $sabado->fecha_devolucion, // Asegúrate de que el formato de fecha sea compatible

                        ]);
                    }
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar sabado -> codigo_agente: %s | SQL: %s', $sabado->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para codigo_agente: %s. Registro no migrado.', $sabado->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de sábados completada.');
    }

    // funcion para cargar los computos de la tabla computos a la tabla computos   de cada usuario
    private function migrateComputos()
    {
        $this->info('Migrando Computos...');

        // Obtenemos los datos de la tabla dia_adicional_vacaciones de la antigua DB
        $computos = DB::connection('old_db')->table('computos')->get();

        if ($computos->isEmpty()) {
            $this->warn('No se encontraron computos en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($computos->count());
        $bar->start();

        foreach ($computos as $computo) {
            // Buscamos el usuario correspondiente en la nueva DB usando el email
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $computo->codigo_agente)->value('id');

            if ($userId) {
                // Insertamos el día adicional en la tabla additionaldays
                try {
                    $computo_id = DB::connection('mysql')->table('computos')->insertGetId([
                        'user_id' => $userId,
                        'year' => $computo->computo_year + 1, // Asegúrate de que el formato de fecha sea compatible
                        'disponible' => $computo->computo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // buscar los dias disfrutados de computo en la DB personalges  en la tabla computo_devoluciones relacionado con el registro actua de computo
                    $dias_disfrutados_computo = DB::connection('old_db')->table('computo_devoluciones')->where('computo_id', $computo->id)->get();
                    // insertar el dia de disfrute de la tabla dia_adicional_vacaciones de personalges en la tabla de disfrutes de laravel, con el id del día adicional que acabamos de insertar y la fecha de disfrute
                    // la tabla disfrutes es una tabla morph que tiene un campo para el modelo y otro para el id del model
                    if ($dias_disfrutados_computo) {
                        // por cada dia disfrutado en computos_devoluciones crear un registro en disfrutes
                        foreach ($dias_disfrutados_computo as $dia) {
                            DB::connection('mysql')->table('disfrutes')->insert([
                                'user_id' => $userId,
                                'disfrutable_id' => $computo_id,
                                'disfrutable_type' => 'App\Models\Computo',
                                'minutos_solicitados' => $dia->devolucion,
                                'status' => 'aprobado',
                                'fecha_disfrute' => $dia->devolucion_date, // Asegúrate de que el formato de fecha sea compatible

                            ]);
                        }
                    }
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar computo -> codigo_agente: %s | SQL: %s', $computo->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para codigo_agente: %s. Registro no migrado.', $computo->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de sábados completada.');
    }

    // funcion para cargar los reconocimientos dela old DB
    private function migrateReconocimientos()
    {
        $this->info('Migrando Reconociminetos...');

        // Obtenemos los datos de la tabla dia_adicional_vacaciones de la antigua DB
        $reconocimientos = DB::connection('old_db')->table('reconocimientos_medico')->get();

        if ($reconocimientos->isEmpty()) {
            $this->warn('No se encontraron reconocimientos en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($reconocimientos->count());
        $bar->start();

        foreach ($reconocimientos as $reconocimiento) {
            // Buscamos el usuario correspondiente en la nueva DB usando el email
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $reconocimiento->codigo_agente)->value('id');
            $lugar = DB::connection('old_db')->table('lugares_reconocimientos')->where('id', $reconocimiento->lugar_id)->value('lugar');
            if ($userId) {
                // Insertamos el día adicional en la tabla additionaldays
                try {
                    $reco_id = DB::connection('mysql')->table('reconocimientos')->insertGetId([
                        'user_id' => $userId,

                        'fecha' => $reconocimiento->fecha,
                        'lugar' => $lugar,
                        'años' => 4,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar reconocimiento -> codigo_agente: %s | SQL: %s', $reconocimiento->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para codigo_agente: %s. Registro no migrado.', $reconocimiento->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de reconocimientos completada.');
    }

    // funcion para cargar los cursos dela old DB
    private function migrateCursos()
    {
        $this->info('Migrando Cursos...');

        // Obtenemos los datos de la tabla dia_adicional_vacaciones de la antigua DB
        $cursos = DB::connection('old_db')->table('cursos')->get();

        if ($cursos->isEmpty()) {
            $this->warn('No se encontraron cursos en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($cursos->count());
        $bar->start();

        foreach ($cursos as $curso) {
            // Buscamos las acciones formativas asociadas al curso

            // Insertamos el día adicional en la tabla additionaldays
            try {
                DB::transaction(function () use ($curso) {
                    $curso_id = DB::connection('mysql')->table('courses')->insertGetId([
                        'name' => Str::upper($curso->nombre_curso),
                        'description' => $curso->descripcion,

                        'duration_hours' => 6,
                        'requires_renewal' => 0,
                        'renewal_years' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // asignar roles obligatorios al curso de la tabla pivot cursos_cargos
                    $cargos = DB::connection('old_db')->table('cargos')->whereIn('id', function ($query) use ($curso) {
                        $query->select('cargo_id')->from('cursos_cargos')->where('curso_id', $curso->id);
                    })->get();

                    foreach ($cargos as $cargo) {

                        switch ($cargo->cargo) {
                            case 'JEFE DE SERVICIO':
                                DB::connection('mysql')->table('course_role')->insert([
                                    'course_id' => $curso_id,
                                    'role_id' => 3,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                break;

                            case 'TECNICO DE P.M.':
                                DB::connection('mysql')->table('course_role')->insert([
                                    'course_id' => $curso_id,
                                    'role_id' => 4,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                break;

                            case 'TECNICO RED':
                                DB::connection('mysql')->table('course_role')->insert([
                                    'course_id' => $curso_id,
                                    'role_id' => 6,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                break;

                            case 'TECNICO DE RED HABILITADO':
                                DB::connection('mysql')->table('course_role')->insert([
                                    'course_id' => $curso_id,
                                    'role_id' => 7,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                break;

                            case 'TECNICO DE P.M. INTEGRAL':
                                DB::connection('mysql')->table('course_role')->insert([
                                    'course_id' => $curso_id,
                                    'role_id' => 5,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                break;
                        }

                    }

                    // crear las acciones formativas asociadas al curso en la tabla training_actions, relacionando el curso con el id del curso que acabamos de insertar, el nombre del formador, el tipo de acción formativa (interna o externa), el lugar, la fecha de inicio y la fecha de fin
                    $acciones_formativas = DB::connection('old_db')->table('formaciones')->where('curso_id', $curso->id)->get();

                    foreach ($acciones_formativas as $accion) {
                        $training_action_id = DB::connection('mysql')->table('training_actions')->insertGetId([
                            'course_id' => $curso_id,
                            'company_name' => Str::upper($curso->empresa),
                            'trainer_name' => $accion->formador,
                            'type' => 'interna',
                            'location' => $accion->lugar,
                            'start_date' => $accion->fecha_inicio,
                            'end_date' => $accion->fecha_fin,
                            'mode' => 'presencial',
                            'notes' => $accion->observacion,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // relacionar el curso con los usuarios que realizaron la acción formativa, para ello hay que buscar en la tabla formaciones_users de la DB personalges los usuarios que realizaron la acción formativa y relacionarlos con el curso que acabamos de insertar en la tabla course_user de la DB laravel
                        $usuarios_formacion = DB::connection('old_db')->table('agentes_formations')->where('formacione_id', $accion->id)->get();
                        foreach ($usuarios_formacion as $usuario) {
                            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $usuario->codigo_agente)->value('id');
                            if ($userId) {
                                DB::connection('mysql')->table('training_action_user')->insert([
                                    'training_action_id' => $training_action_id,
                                    'user_id' => $userId,
                                    // 'attended' =>0,

                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            } else {
                                $this->warn(sprintf('Usuario no encontrado para codigo_agente: %s. Relación curso-usuario no migrada.', $usuario->codigo_agente));
                            }
                        }

                    }

                });
            } catch (QueryException $e) {
                $this->error(sprintf('Error al insertar curso', $e->getMessage()));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de reconocimientos completada.');
    }

    // migrar los dias pedidos de la empresa de la tabla dia_pedidos_empresa a la tabla additionaldays de cada usuario
    private function migrateDiasPedidosEmpresa()
    {
        $this->info('Migrando días empresa...');

        // Obtenemos los datos de la tabla dia_pedidos_empresa de la antigua DB
        $diasEmpresa = DB::connection('old_db')->table('dias_pedidos_empresa')->get();

        if ($diasEmpresa->isEmpty()) {
            $this->warn('No se encontraron días en la antigua DB.');

            return;
        }

        $bar = $this->output->createProgressBar($diasEmpresa->count());
        $bar->start();

        foreach ($diasEmpresa as $dia) {
            // Buscamos el usuario correspondiente en la nueva DB usando el email
            $userId = DB::connection('mysql')->table('users')->where('codigo_agente', $dia->codigo_agente)->value('id');

            if ($userId) {
                // Insertamos el día adicional en la tabla additionaldays
                try {
                    $diaempresa_id = DB::connection('mysql')->table('companydays')->insertGetId([
                        'user_id' => $userId,
                        'fecha' => $dia->fecha, // Asegúrate de que el formato de fecha sea compatible
                        'razon' => $dia->motivo_peticion,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // insertar el dia de disfrute de la tabla dia_adicional_vacaciones de personalges en la tabla de disfrutes de laravel, con el id del día adicional que acabamos de insertar y la fecha de disfrute
                    // la tabla disfrutes es una tabla morph que tiene un campo para el modelo y otro para el id del model
                    if ($dia->fecha_devolucion) {

                        DB::connection('mysql')->table('disfrutes')->insert([
                            'user_id' => $userId,
                            'disfrutable_id' => $diaempresa_id,
                            'disfrutable_type' => 'App\Models\Companyday',
                            'status' => 'aprobado',
                            'fecha_disfrute' => $dia->fecha_devolucion, // Asegúrate de que el formato de fecha sea compatible

                        ]);
                    }
                } catch (QueryException $e) {
                    $this->error(sprintf('Error al insertar día empresa -> email: %s | SQL: %s', $dia->codigo_agente, $e->getMessage()));
                }
            } else {
                $this->warn(sprintf('Usuario no encontrado para email: %s. Día empresa no migrado.', $dia->codigo_agente));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migración de días empresa completada.');
    }
}
