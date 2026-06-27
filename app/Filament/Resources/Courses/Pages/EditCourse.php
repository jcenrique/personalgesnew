<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\TrainingActions\TrainingActionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            // Action::make('training_actions')
            //     ->label(__('Acciones formativas'))
            //     ->icon(Heroicon::CircleStack)
            //     ->url(TrainingActionResource::getUrl('index', [
            //         'course' => $record,
            //     ])),
            Action::make('view_attendees')
                ->label(__('Ver Asistentes'))
                ->icon('heroicon-o-eye')
                ->modalHeading(__('Asistentes confirmados'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Cerrar'))
                ->modalContent(function () use ($record) {
                    // Obtener todos los usuarios de los roles obligatorios del curso
                    $allRequiredUsers = $record->roles->pluck('users')->flatten()->unique('id');

                    // Filtrar confirmados: están inscritos en al menos una acción y renovación no vencida si aplica
                    $confirmedUsers = $allRequiredUsers->filter(function ($user) use ($record) {
                        $lastActionEndDate = $user->trainingActions()
                            ->where('course_id', $record->id)
                            ->max('end_date');

                        if (!$lastActionEndDate) {
                            return false;
                        }

                        if (!$record->requires_renewal) {
                            return true;
                        }

                        // Si requiere renovación, verificar si no ha vencido (basado en fecha de fin de la última acción)
                        return now()->diffInYears(\Carbon\Carbon::parse($lastActionEndDate)) < $record->renewal_years;
                    })->sortBy(function ($user) {
                        return $user->roles->sortBy('name')->first()->name ?? '';
                    });

                    return view('filament.resources.courses.modals.view-attendees', [
                        'attendees' => $confirmedUsers,
                        'course' => $record,
                    ]);
                }),
            Action::make('pending_attendees')
                ->label(__('Ver Pendientes'))
                ->icon('heroicon-o-clock')
                ->modalHeading(__('Asistentes pendientes'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Cerrar'))
                ->modalContent(function () use ($record) {
                    // Obtener todos los usuarios de los roles obligatorios del curso
                    $allRequiredUsers = $record->roles->pluck('users')->flatten()->unique('id');

                    // Confirmados: están inscritos en al menos una acción y renovación no vencida si aplica
                    $confirmedUsers = $allRequiredUsers->filter(function ($user) use ($record) {
                        $lastActionEndDate = $user->trainingActions()
                            ->where('course_id', $record->id)
                            ->max('end_date');

                        if (!$lastActionEndDate) {
                            return false;
                        }

                        if (!$record->requires_renewal) {
                            return true;
                        }

                        // Si requiere renovación, verificar si no ha vencido (basado en fecha de fin de la última acción)
                        return now()->diffInYears(\Carbon\Carbon::parse($lastActionEndDate)) < $record->renewal_years;
                    });

                    // Pendientes: todos los requeridos menos los confirmados
                    $pendingUsers = $allRequiredUsers->diff($confirmedUsers)->sortBy(function ($user) {
                        return $user->roles->sortBy('name')->first()->name ?? '';
                    });

                    return view('filament.resources.courses.modals.pending-attendees', [
                        'attendees' => $pendingUsers,
                        'course' => $record,
                    ]);
                }),
            Action::make('export_attendees_pdf')
                ->label(__('Asistentes a PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('courses.attendees-pdf', ['course' => $record]))
                ->openUrlInNewTab(),
            Action::make('export_pending_attendees_pdf')
                ->label(__('Pendientes a PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('courses.pending-attendees-pdf', ['course' => $record]))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
