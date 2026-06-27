<?php

namespace App\Filament\Resources\Companydays\Pages;

use App\Filament\Resources\Companydays\CompanydayResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCompanyday extends CreateRecord
{
    protected static string $resource = CompanydayResource::class;


}
