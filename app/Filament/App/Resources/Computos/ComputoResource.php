<?php

namespace App\Filament\App\Resources\Computos;

use App\Filament\App\Resources\Computos\Pages\ListComputos;
use App\Filament\App\Resources\Computos\Pages\ViewComputo;
use App\Filament\App\Resources\Computos\Schemas\ComputoForm;
use App\Filament\App\Resources\Computos\Tables\ComputosTable;
use App\Filament\App\Resources\Computos\Schemas\ComputoInfolist;
use App\Models\Computo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ComputoResource extends Resource

{
    protected static ?string $model = Computo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'year';

    //establecer el orden en el menu
    protected static ?int $navigationSort = 1;



    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Cómputo');
    }
    public static function getPluralLabel(): string
    {
        return __('Cómputos');
    }
    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        $modelo_id = Computo::where('user_id', Auth::id())->where('year', now()->year)->first()?->id;

        if (!$modelo_id) {
            return sprintf('%02d:%02d', 0, 0);
        }
        $computo = Computo::where('id', $modelo_id)->where('user_id', Auth::id())->where('year', now()->year)->first();
        if ($computo->disfrutes()->exists()) {
            $minutos_consumidos = $computo->disfrutes()->sum('minutos_solicitados');
        } else {
            $minutos_consumidos = 0;
        }


        $minutos = $computo->disponible - $minutos_consumidos;
        //si el computo es negativo poner el signo menos por delante en el texto de devolución de horas
        if ($minutos < 0) {
            $minutos = abs($minutos);
            $horas = intdiv($minutos, 60);
            $mins  = $minutos % 60;

            return '-' . sprintf('%02d:%02d', $horas, $mins);
        }


        $horas = intdiv($minutos, 60);

        $mins  = $minutos % 60;

        return sprintf('%02d:%02d', $horas, $mins);
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        //si el numero de minutos disponibles es negativo, el badge debe ser de color danger, si es positivo debe ser success y si es cero warning
        $modelo_id = Computo::where('user_id', Auth::id())->where('year', now()->year)->first()?->id;
        if (!$modelo_id) {
            return 'success';
        }
        $computo = Computo::find($modelo_id)->first();
        if ($computo->disfrutes()->exists()) {
            $minutos_consumidos = $computo->disfrutes()->sum('minutos_solicitados');
        } else {
            $minutos_consumidos = 0;
        }
        $minutos = $computo->disponible - $minutos_consumidos;
        if ($minutos < 0) {
            return 'danger';
        } elseif ($minutos > 0) {



            return 'success';
        } else {
            return 'warning';
        }
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Horas totales disponibles año actual');
    }

    public static function form(Schema $schema): Schema
    {
        return ComputoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComputosTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComputoInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DisfrutesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComputos::route('/'),
            //'create' => CreateComputo::route('/create'),
            // 'edit' => EditComputo::route('/{record}/edit'),
            'view' => ViewComputo::route('/{record}/view'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery();

    }
}
