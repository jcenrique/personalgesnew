<?php

namespace App\Filament\App\Resources\Computos\RelationManagers;

use App\Filament\App\Resources\Computos\Actions\SolicitarDíaComputo;
use App\Filament\App\Resources\Computos\ComputoResource;
use App\Models\Computo;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DisfrutesRelationManager extends RelationManager
{
    use HasResizableColumn;

    protected static string $relationship = 'disfrutes';

    protected static ?string $relatedResource = ComputoResource::class;

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->dateTime('d F Y')
                    ->color('primary'),
                TextColumn::make('minutos_solicitados')
                    ->label(__('Horas solicitadas'))
                    ->color('success')
                    ->formatStateUsing(function ($record) {
                        $minutos = $record->minutos_solicitados;

                        $horas = intdiv($minutos, 60);

                        $mins = $minutos % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('status')
                    ->label(__('Estado')),
            ])
            ->modifyQueryUsing(function (Builder $query) {

                return $query
                    ->where('disfrutable_id', $this->getOwnerRecord()->id)
                    ->where('disfrutable_type', Computo::class)
                    ->reorder();
            })
            ->recordActions([])
            ->filters([])
            ->headerActions([
                SolicitarDíaComputo::make('solicitar', $this->getOwnerRecord())

                    ->visible(function () {

                        $restante = $this->getOwnerRecord()->disponible - $this->getOwnerRecord()->disfrutes()->sum('minutos_solicitados');

                        // habilitar el boton si el computo tiene mas de 3:30 horas disponibles
                        return ! ($restante < 210) && $this->getOwnerRecord()->year == date('Y'); // 3 horas y 30 minutos en minutos
                    }),

            ]);
    }
}
