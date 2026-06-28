<?php

namespace App\Filament\Resources\TrainingActions\Pages;

use App\Filament\Resources\TrainingActions\TrainingActionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditTrainingAction extends EditRecord
{
    protected static string $resource = TrainingActionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                TextInput::make('company_name')
                    ->label(__('Empresa'))

                    ->required()
                    ->maxLength(255),
                TextInput::make('trainer_name')
                    ->label(__('Formador'))

                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('Tipo de acción'))
                    ->options([
                        'interna' => __('Interna'),
                        'externa' => __('Externa'),
                    ])
                    ->required(),
                Select::make('mode')
                    ->label(__('Modalidad'))
                    ->options([
                        'presencial' => __('Presencial'),
                        'online' => __('On Line'),
                    ])
                    ->required(),
                TextInput::make('location')
                    ->label(__('Lugar'))

                    ->required()
                    ->maxLength(255),

                DatePicker::make('start_date')
                    ->label(__('Fecha de inicio'))
                    ->native(false)
                    ->displayformat('d F Y')   // lo que ve el usuario
                    ->format('Y-m-d')
                    ->locale('es')
                    ->closeOnDateSelection()
                    ->after('2020-01-01')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {

                        $set('end_date', $state);
                    })
                    ->required(),
                DatePicker::make('end_date')
                    ->afterOrEqual('start_date')
                    ->native(false)
                    ->locale('es')
                    ->closeOnDateSelection()

                    ->displayformat('d F Y')   // lo que ve el usuario
                    ->format('Y-m-d')
                    ->label(__('Fecha de finalización'))
                    ->required(),
                Textarea::make('notes')
                    ->label(__('Notas'))
                    ->columnSpanFull(),
            ]);
    }

    public function getRecord(): Model
    {
        $record = parent::getRecord();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Action::make('view_attendees')
                ->label(__('Asistentes'))
                ->icon(Heroicon::Users)

                ->modalHeading(__('Asistentes'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Cerrar'))
                ->modalContent(function () use ($record) {
                    $attendees = $record->attendees()
                        ->get();

                    return view('filament.resources.training-actions.modals.view-attendees', [
                        'attendees' => $attendees,
                        'trainingAction' => $record,
                    ]);
                }),
            Action::make('export_attendees_pdf')
                ->label(__('Asistentes a PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('training-actions.attendees-pdf', ['trainingAction' => $record]))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
