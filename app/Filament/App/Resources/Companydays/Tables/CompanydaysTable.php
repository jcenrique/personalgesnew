<?php

namespace App\Filament\App\Resources\Companydays\Tables;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Companydays\Actions\SolicitarCompanyday;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompanydaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label(__('Fecha'))
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('razon')
                    ->label(__('Razón'))
                    ->searchable(),
                TextColumn::make('disfrute.status')
                    ->label(__('Estado'))
                    ->default(StatusSolicitudes::Disponible)
                    ->badge(),
                TextColumn::make('disfrute.fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->color('success')
                    ->weight(FontWeight::ExtraBold)
                    ->placeholder(StatusSolicitudes::Disponible->getLabel())
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([

                SolicitarCompanyday::make()
                    ->visible(
                        function ($record, $livewire) {

                            return $record->disfrute == null && ($livewire->activeTab === 'available' || $livewire->activeTab === 'all');
                        }
                    ),

            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
