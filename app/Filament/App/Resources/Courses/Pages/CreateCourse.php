<?php

namespace App\Filament\App\Resources\Courses\Pages;

use App\Filament\App\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}
