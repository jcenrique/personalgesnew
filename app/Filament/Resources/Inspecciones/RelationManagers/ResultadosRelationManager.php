<?php

namespace App\Filament\Resources\Inspecciones\RelationManagers;

use App\Models\Resultadoinspeccion;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class ResultadosRelationManager extends RelationManager
{
    use HasResizableColumn;
    protected static string $relationship = 'resultados';

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             TextInput::make('elementoinspeccion_id')
    //                 ->required()
    //                 ->maxLength(255),
    //         ]);
    // }

    #[Override]
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'periodica';
    }

    public function table(Table $table): Table
    {
        return $table


            ->defaultPaginationPageOption(25)
            ->groups([
                Group::make('elemento.categoria.nombre_es')

                    ->getDescriptionFromRecordUsing(fn(Resultadoinspeccion $record): string => $record->elemento->categoria->nombre_eu)
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])

            ->defaultGroup('elemento.categoria.nombre_es')
            ->groupingSettingsHidden()


            ->recordTitleAttribute('elementoinspeccion_id')
            ->columns([

                TextColumn::make('elemento.nombre_es')
                    ->color('info')
                    ->width('40%')
                    ->description(function ($record) {

                        return $record->elemento->nombre_eu;
                    }),
                ToggleColumn::make('resultado')
                    ->width('10%')
                    ->label('Resultado')
                    ->offColor('danger')
                    ->offIcon(Heroicon::XCircle)
                    ->onColor('success')
                    ->onIcon(Heroicon::CheckCircle),
                TextInputColumn::make('observacion')
                    ->width('50%')
                    ->label('Observación'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::PlusCircle)
                    ->label(__('Asociar elemento'))
                    ->modalHeading(__('Asociar elemento'))
                    ->createAnother(false)
                    ->form([
                        Select::make('elementoinspeccion_id')
                            ->label(__('Elemento'))
                            ->options(function ($livewire) {
                                return \App\Models\Elementoinspeccion::whereDoesntHave('resultados', function ($q) use ($livewire) {
                                    $q->where('inspeccion_id', $livewire->ownerRecord->id);
                                })
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->nombre_es} ({$item->nombre_eu})"
                                        ];
                                    });
                            })

                            ->searchable()
                            ->required(),
                    ])
                    ->mutateFormDataUsing(function ($data, $livewire) {
                        $data['inspeccion_id'] = $livewire->ownerRecord->id;
                        return $data;
                    }),

            ])
            ->recordActions([
                // EditAction::make(),
                //DissociateAction::make(),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Delete')),
            ])
            ->toolbarActions([
                //BulkActionGroup::make([
                // DissociateBulkAction::make(),
                // DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
