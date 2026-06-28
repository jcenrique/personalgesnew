<?php

namespace App\Filament\Resources\Zonas\RelationManagers;

use App\Filament\Resources\Zonas\ZonaResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ResidenciasRelationManager extends RelationManager
{
    use HasResizableColumn;

    protected static string $relationship = 'residencias';

    protected static ?string $relatedResource = ZonaResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Residencia');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('Residencias');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Nombre de la residencia'))
                    ->searchable()
                    ->sortable(),
            ])

            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Delete')),

                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Edit')),
            ])
            ->headerActions([
                CreateAction::make(),

            ]);
    }
}
