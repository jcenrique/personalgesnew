<?php

use App\Exports\Report_Computo_NoModificados;
use App\Exports\ReportAdditionalDayImport_Export;
use App\Models\Course;
use App\Models\Inspeccion;
use App\Models\TrainingAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

//require __DIR__.'/auth.php';
// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('web')->group(function () {
    require __DIR__ . '/auth.php';

    Route::middleware('auth')->get('/training-actions/{trainingAction}/attendees-pdf', function (TrainingAction $trainingAction) {
        $trainingAction->load('course');

        $attendees = $trainingAction->attendees()
            ->get();

        $trainingAction->course->name = iconv('UTF-8', 'UTF-8//IGNORE', $trainingAction->course->name);
        $trainingAction->company_name = iconv('UTF-8', 'UTF-8//IGNORE', $trainingAction->company_name);
        $trainingAction->trainer_name = iconv('UTF-8', 'UTF-8//IGNORE', $trainingAction->trainer_name);
        $trainingAction->mode = iconv('UTF-8', 'UTF-8//IGNORE', $trainingAction->mode);

        $pdf = Pdf::loadView('filament.resources.training-actions.pdfs.attendees', [
            'trainingAction' => $trainingAction,
            'attendees' => $attendees,
        ])->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('asistentes_' . $trainingAction->id . '.pdf');
    })->name('training-actions.attendees-pdf');

    Route::middleware('auth')->get('/courses/{course}/attendees-pdf', function (Course $course) {
        $course->load('roles.users');

        $allRequiredUsers = $course->roles->pluck('users')->flatten()->unique('id');

        $attendees = $allRequiredUsers->filter(function ($user) use ($course) {
            $lastActionEndDate = $user->trainingActions()
                ->where('course_id', $course->id)
                ->max('end_date');

            if (!$lastActionEndDate) {
                return false;
            }

            if (!$course->requires_renewal) {
                return true;
            }

            return now()->diffInYears(\Carbon\Carbon::parse($lastActionEndDate)) < $course->renewal_years;
        })->sortBy(function ($user) {
            return $user->roles->sortBy('name')->first()->name ?? '';
        })->values();

        $pdf = Pdf::loadView('filament.resources.courses.pdfs.attendees', [
            'title' => __('Asistentes confirmados'),
            'course' => $course,
            'attendees' => $attendees,
        ])->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('asistentes_' . $course->id . '.pdf');
    })->name('courses.attendees-pdf');

    Route::middleware('auth')->get('/courses/{course}/pending-attendees-pdf', function (Course $course) {
        $course->load('roles.users');

        $allRequiredUsers = $course->roles->pluck('users')->flatten()->unique('id');

        $confirmedUsers = $allRequiredUsers->filter(function ($user) use ($course) {
            $lastActionEndDate = $user->trainingActions()
                ->where('course_id', $course->id)
                ->max('end_date');

            if (!$lastActionEndDate) {
                return false;
            }

            if (!$course->requires_renewal) {
                return true;
            }

            return now()->diffInYears(\Carbon\Carbon::parse($lastActionEndDate)) < $course->renewal_years;
        });

        $attendees = $allRequiredUsers->diff($confirmedUsers)
            ->sortBy(fn($user) => $user->roles->sortBy('name')->first()->name ?? '')
            ->values();

        $pdf = Pdf::loadView('filament.resources.courses.pdfs.attendees', [
            'title' => __('Asistentes pendientes'),
            'course' => $course,
            'attendees' => $attendees,
        ])->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('asistentes_pendientes_' . $course->id . '.pdf');
    })->name('courses.pending-attendees-pdf');
});



Route::middleware('auth')->get('/inspecciones/{inspeccion}/export-pdf', function (Inspeccion $inspeccion) {

    $inspeccion->load([
        'user1',
        'user2',
        'estacion',
        'resultados.elemento.categoria',
    ]);
    $logo = base64_encode(file_get_contents(public_path('storage/images/logo_ets.jpg')));
    //fecha en euskera y castellano
    $meses_eu = [
        1 => 'Urtarrila',
        2 => 'Otsaila',
        3 => 'Martxoa',
        4 => 'Apirila',
        5 => 'Maiatza',
        6 => 'Ekaina',
        7 => 'Uztaila',
        8 => 'Abuztua',
        9 => 'Iraila',
        10 => 'Urria',
        11 => 'Azaroa',
        12 => 'Abendua',
    ];

    $fecha = $inspeccion->fecha_hora;
    $fecha_eu = $fecha->translatedFormat('Y') . ' ' . $meses_eu[$fecha->translatedFormat('n')] . ' ' . $fecha->translatedFormat('d');

    $meses_es = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    $fecha_es = $fecha->translatedFormat('d') . ' de ' . $meses_es[$fecha->translatedFormat('n')] . ' de ' . $fecha->translatedFormat('Y');

    $roles = $inspeccion->user2->roles
        ->pluck('name')
        ->map(fn($r) => ucwords(str_replace('_', ' ', $r)))
        ->implode(', ');

    $pdf = Pdf::loadView('filament.resources.inspecciones.pdfs.inspeccion', [
        'inspeccion' => $inspeccion,
        'formato_id' => 'FN1-0001/4',
        'logo' => $logo,
        'roles' =>  $roles,
        'fecha_eu' => $fecha_eu,
        'fecha_es' => $fecha_es,
    ])->setOptions([
        'defaultFont' => 'Arial',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

    return $pdf->download('inspeccion_' . $inspeccion->estacion->name . '.pdf');
})->name('inspecciones.export-pdf');


Route::middleware('auth')->get('/inspecciones/{inspeccion}/export-especial-pdf', function (Inspeccion $inspeccion) {

    $inspeccion->load([
        'user1',
        'user2',
        'estacion',
        'resultados.elemento.categoria',
    ]);
    $logo = base64_encode(file_get_contents(public_path('storage/images/logo_ets.jpg')));
    //fecha en euskera y castellano
    $meses_eu = [
        1 => 'Urtarrila',
        2 => 'Otsaila',
        3 => 'Martxoa',
        4 => 'Apirila',
        5 => 'Maiatza',
        6 => 'Ekaina',
        7 => 'Uztaila',
        8 => 'Abuztua',
        9 => 'Iraila',
        10 => 'Urria',
        11 => 'Azaroa',
        12 => 'Abendua',
    ];

    $fecha = $inspeccion->fecha_hora;
    $fecha_eu = $fecha->translatedFormat('Y') . ' ' . $meses_eu[$fecha->translatedFormat('n')] . ' ' . $fecha->translatedFormat('d');

    $meses_es = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    $fecha_es = $fecha->translatedFormat('d') . ' de ' . $meses_es[$fecha->translatedFormat('n')] . ' de ' . $fecha->translatedFormat('Y');

    $roles = $inspeccion->user2->roles
        ->pluck('name')
        ->map(fn($r) => ucwords(str_replace('_', ' ', $r)))
        ->implode(', ');

    $pdf = Pdf::loadView('filament.resources.inspecciones.pdfs.inspeccion-especial', [
        'inspeccion' => $inspeccion,
        'formato_id' => 'FN1-0002/1',
        'logo' => $logo,
        'roles' =>  $roles,
        'fecha_eu' => $fecha_eu,
        'fecha_es' => $fecha_es,

    ])->setOptions([
        'defaultFont' => 'Arial',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

    return $pdf->download('inspeccion_especial' . $inspeccion->estacion->name . '.pdf');
})->name('inspecciones.export-especial-pdf');

Route::get('/export-no-eliminados/{id}/{year}/{rows}', function ($id, $year, $rows) {

    $report = cache()->get("report_$id", collect());

    return \Maatwebsite\Excel\Facades\Excel::download(
        new ReportAdditionalDayImport_Export($report, $year, $rows) , 'dias_no_eliminados.xlsx');

});

Route::get('/export-computo-no-modificados/{id}/{year}/{rows}', function ($id, $year, $rows) {

    $report = cache()->get("report_$id", collect());

    return \Maatwebsite\Excel\Facades\Excel::download(
        new Report_Computo_NoModificados($report, $year, $rows) , 'computos_no_modificados.xlsx');

});


Route::get('/add', function () {
    return 'User ';
});
//
