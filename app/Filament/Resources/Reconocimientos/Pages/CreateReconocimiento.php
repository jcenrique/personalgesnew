<?php

namespace App\Filament\Resources\Reconocimientos\Pages;

use App\Filament\Resources\Reconocimientos\ReconocimientoResource;
use App\Models\Reconocimiento;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateReconocimiento extends CreateRecord
{
    protected static string $resource = ReconocimientoResource::class;



}
