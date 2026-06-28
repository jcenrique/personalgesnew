<?php

namespace App\Filament\App\Resources\Additionaldays\Pages;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Additionaldays\AdditionaldayResource;
use App\Models\Additionalday;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListAdditionaldays extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = AdditionaldayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    // filtar par mostrar segun el satatus con Tabs
    public function getTabs(): array
    {
        return [
            'available' => Tab::make(__('Disponibles'))
                ->icon(Heroicon::Check)
                ->badgeColor(StatusSolicitudes::Disponible->getColor())
                ->badge(function () {

                    $dias = Additionalday::where('user_id', Auth::id());
                    $count = $dias
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
            // ->query(fn($query) => $query->where('status', \App\Enum\StatusSolicitudes::Disponible)),

            'requested' => Tab::make(__('Solicitados'))
                ->icon(Heroicon::PaperAirplane)
                ->badgeColor(StatusSolicitudes::Solicitado->getColor())
                ->badge(function () {

                    $dias = Additionalday::where('user_id', Auth::id());

                    $count = $dias
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

            'approved' => Tab::make(__('Aprobados'))
                ->icon(Heroicon::CheckBadge)
                ->badgeColor(StatusSolicitudes::Aprobado->getColor())
                ->badge(function () {
                    $dias = Additionalday::where('user_id', Auth::id());

                    $count = $dias
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

            'all' => Tab::make(__('Todos'))
                ->icon(Heroicon::Square3Stack3d)
                ->badgeColor('primary')
                ->badge(function () {

                    $dias = Additionalday::where('user_id', Auth::id());
                    $count = $dias->get()->count();

                    return $count > 0 ? $count : null;
                })
                ->query(fn ($query) => $query),
        ];
    }
}
