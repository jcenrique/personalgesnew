<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends PagesDashboard
{
    use HasPageShield;



    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;



    protected static ?int $navigationSort = 1;


    public static function getNavigationLabel(): string
    {
        return __('Inicio');
    }
    public  function getTitle(): string
    {
        return '';
    }

}
