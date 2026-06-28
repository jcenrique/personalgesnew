<?php

namespace App\Filament\Resources\Sabados\Pages;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Sabados\SabadoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageSabados extends ManageRecords
{
    use HasResizableColumn;

    protected static string $resource = SabadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('Anotar sábado trabajado'))
                ->modalWidth('md')
                ->createAnother(false)
                ->icon('far-calendar-plus')
                ->color('warning'),
        ];
    }

    public function getTabs(): array
    {
        return [

            'available' => Tab::make()->label(__('Disponibles'))
                ->icon(Heroicon::Check)
                ->badgeColor(StatusSolicitudes::Disponible->getColor())
                ->badge(function ($livewire) {
                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;

                    $query = static::getResource()::getEloquentQuery();

                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $count = $query
                        ->doesntHave('disfrute')
                        ->get()
                        ->count();

                    return $count > 0 ? $count : null;
                })
                ->query(function (Builder $query) {
                    $query
                        ->doesntHave('disfrute')
                        ->get();
                }),
            'requested' => Tab::make()->label(__('Solicitados'))->icon(Heroicon::Clock)
                ->badgeColor(StatusSolicitudes::Solicitado->getColor())
                ->badge(function ($livewire) {
                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;

                    $query = static::getResource()::getEloquentQuery(); // Companyday::query();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $count = $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Solicitado);
                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(function (Builder $query) {

                    $query->whereHas('disfrute', function (Builder $query) {
                        // dd($query->where('status', StatusSolicitudes::Solicitado));
                        $query->where('status', StatusSolicitudes::Solicitado);
                    })
                        ->get();
                }),
            'approved' => Tab::make()->label(__('Aprobados'))->icon(Heroicon::CheckBadge)
                ->badgeColor(StatusSolicitudes::Aprobado->getColor())
                ->badge(function ($livewire) {
                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;
                    // incluir el filtro de user_id en la consulta si no es null
                    $query = static::getResource()::getEloquentQuery();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $count = $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);
                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(function (Builder $query) {
                    $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);
                        })
                        ->get();
                }),

            'all' => Tab::make()->label(__('Todos'))
                ->icon(Heroicon::Square3Stack3d)
                ->badgeColor('primary')
                ->badge(function ($livewire) {
                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;
                    // incluir el filtro de user_id en la consulta si no es null
                    $query = static::getResource()::getEloquentQuery();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }
                    $count = $query->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(function ($query) {}),

        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'available';
    }

    //     protected function getTableQuery(): Builder
    // {

    //     $query = parent::getTableQuery();
    //     $user = Auth::user();

    //     // Si  es super_admin ni admin, filtrar por zonas
    //     if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
    //         // Obtener zonas del usuario autenticado directamente
    //         $zonaIds = $user->zonas()
    //             ->pluck('zonas.id')
    //             ->toArray();

    //         // Filtrar por usuarios que pertenecen a esas zonas a través de las residencias
    //         $query = $query->whereHas('user.residencias', function (Builder $q) use ($zonaIds) {
    //             $q->whereIn('zona_id', $zonaIds);
    //         });
    //     }

    //     return $query;
    // }
}
