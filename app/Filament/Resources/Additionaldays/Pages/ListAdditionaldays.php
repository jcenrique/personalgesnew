<?php

namespace App\Filament\Resources\Additionaldays\Pages;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Additionaldays\AdditionaldayResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListAdditionaldays extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = AdditionaldayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('Anotar día adicional'))
                ->modalWidth('md')
                ->createAnother(false)
                ->icon('far-calendar-plus')
                ->color('warning'),
            Action::make('importdays')
                ->label(__('Importar días adicionales'))
                ->color('success')
                ->icon(Heroicon::ArrowUpOnSquareStack)
                ->url('additionaldays/add-days'),

        ];
    }

    public function getTabs(): array
    {
        return [

            'available' => Tab::make()->label(__('Disponibles'))
                ->icon(Heroicon::Check)
                ->badgeColor(StatusSolicitudes::Disponible->getColor())
                ->badge(function ($query, $livewire) {

                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;
                    // incluir el filtro de user_id en la consulta si no es null
                    $query = static::getResource()::getEloquentQuery();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $filterYear = $livewire->getTable()->getFilters()['year']->getState()['value'] ?? null;
                    if ($filterYear) {
                        $query->where('year', $filterYear);
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
                    // incluir el filtro de user_id en la consulta si no es null
                    $query = static::getResource()::getEloquentQuery();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $filterYear = $livewire->getTable()->getFilters()['year']->getState()['value'] ?? null;
                    if ($filterYear) {
                        $query->where('year', $filterYear);
                    }

                    $count = $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Solicitado);
                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->query(function (Builder $query) {
                    $query
                        ->whereHas('disfrute', function (Builder $query) {
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

                    $filterYear = $livewire->getTable()->getFilters()['year']->getState()['value'] ?? null;
                    if ($filterYear) {
                        $query->where('year', $filterYear);
                    }
                    $count = $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);
                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->query(function (Builder $query) {
                    $query
                        ->whereHas('disfrute', function (Builder $query) {
                            // dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);
                        })
                        ->get();
                }),

            'all' => Tab::make()->label(__('Todos'))->icon(Heroicon::Square3Stack3d)
                ->badgeColor('primary')
                ->badge(function ($livewire) {
                    $filterUserId = $livewire->getTable()->getFilters()['user_id']->getState()['value'] ?? null;
                    // incluir el filtro de user_id en la consulta si no es null
                    $query = static::getResource()::getEloquentQuery();
                    if ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    }

                    $filterYear = $livewire->getTable()->getFilters()['year']->getState()['value'] ?? null;
                    if ($filterYear) {
                        $query->where('year', $filterYear);
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
}
