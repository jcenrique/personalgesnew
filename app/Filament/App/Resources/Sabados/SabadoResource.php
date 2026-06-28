<?php

namespace App\Filament\App\Resources\Sabados;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Sabados\Actions\AnotarSabadoAction;
use App\Filament\App\Resources\Sabados\Actions\SolicitarSabadoAction;
use App\Filament\App\Resources\Sabados\Pages\ManageSabados;
use App\Models\Disfrute;
use App\Models\Sabado;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SabadoResource extends Resource
{
    protected static ?string $model = Sabado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?string $recordTitleAttribute = 'sabado_trabajado';

    // establecer el orden en el menu
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Sábado');
    }

    public static function getPluralLabel(): string
    {
        return __('Sábados');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {

        // obtener los dias adicionales del usuario para el año actual
        $sabados_totales = Sabado::where('user_id', Auth::id())
            ->count();

        // contar los días adicionales  se han solicitado disfrutar
        $sabados_disfrutados = Disfrute::where('disfrutable_type', Sabado::class)
            ->where('user_id', Auth::id())
            ->where('status', StatusSolicitudes::Aprobado)->count();

        $sabados_disponibles = $sabados_totales - $sabados_disfrutados;

        return $sabados_disponibles;
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de sábados totales disponibles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('sabado_trabajado')
            ->poll(function ($livewire) {

                $livewire->dispatch('refresh-sidebar');

                return '10s';
            })
            ->defaultSort('sabado_trabajado', 'asc')
            ->columns([
               TextColumn::make('sabado_trabajado')

                   ->label(__('Sábado trabajado'))
                   ->date('d F Y')
                   ->color('info')
                   ->sortable(),

               TextColumn::make('disfrute.status')
                   ->label(__('Estado'))
                   ->default(StatusSolicitudes::Disponible)
                   ->badge(),

               TextColumn::make('disfrute.fecha_disfrute')
                   ->label(__('Fecha de disfrute'))
                   ->placeholder(StatusSolicitudes::Disponible->getLabel())
                   // cambiar el color  si la fecha disfrute está pasada
                   ->color(function ($record) {
                       if ($record->disfrute?->fecha_disfrute) {
                           return Carbon::parse($record->disfrute->fecha_disfrute)->isPast() ? 'gray' : 'success';
                       }

                       return null;
                   })
                  // ->color('success')
                   ->weight(FontWeight::ExtraBold)

                   // hacer visible esta columna solo cuando el estado sea "aprobado" o "disponible" o estemos en la pestaña de todas las solicitudes (porque ahí también se pueden

                   ->date('d F Y')
                   ->sortable(),

            ])
            ->filters([
                // TrashedFilter::make(),

            ])

            ->recordActions([
                SolicitarSabadoAction::make()
                   // hacer visible esta acción solo cuando el estado sea "disponible"
                   ->visible(
                       function ($record, $livewire) {

                           return $record->disfrute == null && ($livewire->activeTab === 'available' || $livewire->activeTab === 'all');
                       }
                   ),

            ])

            ->toolbarActions([
                AnotarSabadoAction::make(),

                BulkActionGroup::make([]),
            ])
            // modificar query para que obtener solo records del usuario autenticado
            ->modifyQueryUsing(function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSabados::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery();
    }
}
