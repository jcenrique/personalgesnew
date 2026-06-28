<?php

namespace App\Console\Commands;

use App\Models\Reconocimiento;
use App\Models\User;
use App\Notifications\ReconocimientoPorCaducar;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Signature('app:check-reconocimientos')]
#[Description('Command description')]
class CheckReconocimientos extends Command
{
    // protected $signature = 'reconocimientos:check';
    // protected $description = 'Notifica a los administradores si hay usuarios con reconocimientos próximos a caducar';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Obtener el último reconocimiento por usuario
        $ultimos = Reconocimiento::select('reconocimientos.*')
            ->join(
                DB::raw('(SELECT user_id, MAX(fecha) AS ultima_fecha
                             FROM reconocimientos
                             GROUP BY user_id) AS t'),
                function ($join) {
                    $join->on('reconocimientos.user_id', '=', 't.user_id')
                        ->on('reconocimientos.fecha', '=', 't.ultima_fecha');
                }
            )
            ->with('user')
            ->get();

        $usuariosPorCaducar = collect();

        // 2. Calcular caducidad real
        foreach ($ultimos as $rec) {
            Log::info('--- RECONOCIMIENTO ---');
            Log::info('Usuario: '.$rec->user->name);
            Log::info('Fecha reconocimiento: '.$rec->fecha);
            Log::info('Años validez: '.$rec->años);
            $caduca = Carbon::parse($rec->fecha)->addYears($rec->años);
            Log::info('Caduca: '.$caduca);
            $mesesRestantes = now()->diffInMonths($caduca, false);
            Log::info('Meses restantes: '.$mesesRestantes);
            if ($mesesRestantes <= 6) {

                $usuariosPorCaducar->push($rec->user);
            }
        }

        // 3. Notificar a administradores
        if ($usuariosPorCaducar->isNotEmpty()) {
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new ReconocimientoPorCaducar($usuariosPorCaducar));
            }
        }

        Log::info('Cron ejecutado: '.now());

        return Command::SUCCESS;
    }
}
