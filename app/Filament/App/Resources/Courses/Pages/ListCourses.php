<?php

namespace App\Filament\App\Resources\Courses\Pages;

use App\Filament\App\Resources\Courses\CourseResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
     use HasResizableColumn;

    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
