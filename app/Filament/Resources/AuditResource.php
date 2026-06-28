<?php

namespace App\Filament\Resources;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Tapp\FilamentAuditing\Filament\Resources\Audits\AuditResource as BaseAuditResource;
use Tapp\FilamentAuditing\Models\Audit;
use UnitEnum;

class AuditResource extends BaseAuditResource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ServerStack;

    protected static ?int $navigationSort = 100;

    public static function getNavigationBadge(): ?string
    {
        return Audit::count();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Admin';
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Auditoría');
    }

    public static function getPluralLabel(): string
    {
        return __('Auditorías');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getModelLabel(): string
    {
        return __('Auditoría');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Auditorías');
    }
}
