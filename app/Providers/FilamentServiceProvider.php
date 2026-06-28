<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\Events\LocaleChanged;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\ModalComponent;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        ModalComponent::closedByClickingAway(false);

        Event::listen(function (LocaleChanged $event) {
            // persist the new locale in the user's profile, or perform any other action
            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $event->locale;
                $user->save();
            }
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'eu'])
                ->visible(outsidePanels: true)
                // also accepts a closure
                ->flags([
                    'es' => asset('storage/flags/es.svg'),
                    'eu' => asset('storage/flags/eu.svg'),

                ])
                ->circular();
        });

        Filament::serving(function () {
            if (Auth::check()) {
                app()->setLocale(Auth::user()->locale);
            }
        });
        //

        FilamentColor::register([
            'fuchsia' => Color::Fuchsia,
            'yellow' => Color::Yellow,
            'indigo' => Color::Indigo,
            'cyan' => Color::Cyan,
        ]);

        Table::configureUsing(function (Table $table): void {

            $table
                ->striped()
                ->poll(function ($livewire) {
                    $livewire->dispatch('refresh-sidebar');

                    return '10s';
                })
                ->paginationPageOptions([10, 25, 50]);
        });

    }
}
