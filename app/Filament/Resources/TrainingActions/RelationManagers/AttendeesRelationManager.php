<?php

namespace App\Filament\Resources\TrainingActions\RelationManagers;

use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class AttendeesRelationManager extends RelationManager
{
    use HasResizableColumn;

    protected static string $relationship = 'attendees';

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public function getTableHeading(): string|Htmlable|null
    {
        return __('Asistentes');
    }

    public function table(Table $table): Table
    {
        return $table


            ->columns([
                TextColumn::make('name')
                    ->label(__('Nombre'))

                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('Email'))

                    ->sortable(),
                TextColumn::make('roles.user')
                    ->label(__('Rol'))
                    ->badge()
                    ->color('success')
                    ->separator(', ')
                    ->getStateUsing(function ($record) {

                        $roles_user = $record->roles;
                        return $roles_user
                            ->pluck('name')

                            ->unique()
                            ->map(fn($name) => ucwords(str_replace('_', ' ', $name)))
                            ->implode(', ');
                    }),
                TextColumn::make('pivot.certificate_path')
                    ->label(__('Certificado'))
                    ->limit(40),
            ])
            ->headerActions([
                AttachAction::make('add_attendees')
                    ->color('primary')
                    ->icon(Heroicon::UserPlus)
                    ->label(__('Vincular asistentes'))
                    ->multiple()
                    ->preloadRecordSelect(true)
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordSelectOptionsQuery(function (\Illuminate\Database\Eloquent\Builder $query) {
                        $trainingAction = $this->getOwnerRecord();
                        $course = $trainingAction->course;


                        $renewalLimit = now()->subYears($course->renewal_years);

                        $query
                            ->whereHas('roles', function ($q) use ($course) {

                                $q->whereHas('courses', fn($qq) => $qq->where('courses.id', $course->id));

                            })
                            ->whereDoesntHave('trainingActions', function ($q) use ($course, $renewalLimit) {

                                $q->where('course_id', $course->id)
                                     ->where('end_date', '>=', $renewalLimit);
                            });

                    }),






            ])
            ->recordActions([
                DetachAction::make()
                    ->hiddenLabel(true)
                    ->icon(Heroicon::UserMinus)
                    ->tooltip(__('Desvincular assitente')),
            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ]);
    }
}
