<?php

namespace App\Enum;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum StatusSolicitudes: string implements HasLabel, HasIcon, HasColor
{

    case Disponible = 'disponible';
    case Solicitado = 'solicitado';
    case Aprobado = 'aprobado';
    case Rechazado = 'rechazado';


    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {

            self::Disponible => __('Disponible'),
            self::Solicitado => __('Solicitado'),
            self::Aprobado => __('Aprobado'),
            self::Rechazado => __('Rechazado'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {

            self::Disponible => 'success',
            self::Solicitado => 'fuchsia',
            self::Aprobado => 'yellow',
            self::Rechazado => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {

            self::Disponible => Heroicon::Check,
            self::Solicitado => Heroicon::Clock,
            self::Aprobado => Heroicon::CheckBadge,
            self::Rechazado => Heroicon::XMark,
        };
    }
}
