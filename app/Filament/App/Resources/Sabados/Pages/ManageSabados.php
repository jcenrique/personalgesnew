<?php

namespace App\Filament\App\Resources\Sabados\Pages;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Sabados\SabadoResource;
use App\Models\Sabado;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageSabados extends ManageRecords
{
    use HasResizableColumn;
    protected static string $resource = SabadoResource::class;



    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),

        ];
    }

    public function getTabs(): array
    {
        return [


            'available' => Tab::make()->label(__('Disponibles'))
                ->icon(Heroicon::Check)
                ->badgeColor(StatusSolicitudes::Disponible->getColor())
                ->badge(function () {
                    //obtener los dias adicionales del usuario para el año actual


                     $dias = Sabado::where ('user_id', Auth::id());
                    $count = $dias
                       ->doesntHave('disfrute')
                       ->get()
                        ->count();

                    return $count > 0 ? $count : null;
                })
                ->query(function(Builder $query){
                   $query
                       ->doesntHave('disfrute')
                       ->get();
                }),
            'requested' => Tab::make()->label(__('Solicitados'))
                ->icon(Heroicon::PaperAirplane)
                ->badgeColor(StatusSolicitudes::Solicitado->getColor())
                ->badge(function () {
                    $dias = Sabado::where ('user_id', Auth::id());

                    $count = $dias
                        ->whereHas('disfrute' , function(Builder $query){
                            //dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Solicitado);

                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                 ->query(function(Builder $query){
                   $query
                       ->whereHas('disfrute' , function(Builder $query){
                            //dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Solicitado);

                        })
                        ->get();
                }),

            'approved' => Tab::make()->label(__('Aprobados'))->icon(Heroicon::CheckBadge)
                ->badgeColor(StatusSolicitudes::Aprobado->getColor())
                ->badge(function () {
                     $dias = Sabado::where ('user_id', Auth::id());

                    $count = $dias
                        ->whereHas('disfrute' , function(Builder $query){
                            //dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);

                        })
                        ->get()->count();

                    return $count > 0 ? $count : null;
                })
                 ->query(function(Builder $query){
                   $query
                       ->whereHas('disfrute' , function(Builder $query){
                            //dd($query->where('status', StatusSolicitudes::Solicitado));
                            $query->where('status', StatusSolicitudes::Aprobado);

                        })
                        ->get();
                }),


            'all' => Tab::make()->label(__('Todos'))
                ->icon(Heroicon::Square3Stack3d)
                ->badgeColor('primary')
                ->badge(function () {
                    $count = Auth::user()->sabados()->count();

                    return $count > 0 ? $count : null;
                })
                ->modifyQueryUsing(function ($query) {
                    $query->where('user_id', Auth::id());
                }),


        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'available';
    }
}
