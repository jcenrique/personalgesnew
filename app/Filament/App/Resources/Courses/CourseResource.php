<?php

namespace App\Filament\App\Resources\Courses;

use App\Filament\App\Resources\Courses\Pages\ListCourses;
use App\Filament\App\Resources\Courses\Schemas\CourseForm;
use App\Filament\App\Resources\Courses\Tables\CoursesTable;
use App\Models\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Formación');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Curso');
    }

    public static function getPluralLabel(): string
    {
        return __('Cursos');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        $userId = Auth::id();

        $total_cursos = Course::whereHas('trainingActions.users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->count();
        //  $total_cursos = Course::count();

        return $total_cursos;
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            // 'create' => CreateCourse::route('/create'),
            // 'edit' => EditCourse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Auth::id();

        return parent::getEloquentQuery()->whereHas('trainingActions.users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }
}
