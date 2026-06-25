<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\AuditResource;
use App\Models\Role;
use Asmit\ResizedColumn\ResizedColumnPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Tapp\FilamentAuditing\FilamentAuditingPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('SofTren')
            ->brandLogo(asset('storage/images/miLogo.png'))
            ->brandLogoHeight('3rem')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandName('SofTren')
            ->emailVerification()
            ->login(Login::class)
            ->globalSearch(false)
            ->font('Antic')
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('9rem')
            //  ->collapsibleNavigationGroups()

            ->databaseNotifications()
            ->userMenuItems([
                Action::make('App')

                    ->url(fn(): string => '/')
                    ->icon(Heroicon::Home),
                // ...
            ])

            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Amber,
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                //Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([


                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->resources([
                AuditResource::class, // tu recurso sobrescrito
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    // string|Closure|null
                    ->navigationIcon('fas-user-shield')        // string|Closure|null
                    ->activeNavigationIcon('fas-user-shield')   // string|Closure|null
                    ->navigationGroup('Admin')
                    // string|Closure|null
                    ->navigationSort(10)
                    //si no existe la tabla roles, devuelve 0 para evitar errores
                    ->navigationBadge(function () {
                        //si no existe la tabla roles, devuelve 0 para evitar errores
                        if (Role::count()) {
                            return strval(Role::count());
                        } else {
                            return "0";
                        }
                    })                      // string|Closure|null
                    ->navigationBadgeColor('success')

                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

                ResizedColumnPlugin::make(),


            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
