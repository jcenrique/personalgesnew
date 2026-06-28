<?php

namespace App\Filament\App\Resources\Inspecciones\Pages;

use App\Filament\App\Resources\Inspecciones\InspeccionesResource;
use App\Models\Elementoinspeccion;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateInspeccion extends CreateRecord
{
    protected static string $resource = InspeccionesResource::class;

    #[Override]
    public function getCreateAnotherFormAction(): Action
    {
        $action = parent::getCreateAnotherFormAction();
        $action->hidden(true);

        return $action;
    }

    public function afterCreate(): void
    {
        // ❗ Si es inspección especial → NO crear elementos
        if ($this->record->type === 'especial') {
            // elimian los resultados que se hayan creado anteiromente
            $this->record->resultados()->delete();

            return;
        }

        $elementos = Elementoinspeccion::query()
            ->join('categoriaelementos', 'elementos_inspecciones.categoriaelemento_id', '=', 'categoriaelementos.id')
            ->where('categoriaelementos.active', 1)
            ->where('elementos_inspecciones.active', 1)
            ->orderBy('categoriaelementos.sort')
            ->select('elementos_inspecciones.*')
            ->get();

        foreach ($elementos as $elem) {
            $this->record->resultados()->create([
                'isnpeccion_id' => $this->record->id,
                'elementoinspeccion_id' => $elem->id,
                'resultado' => 0,
                'observacion' => null,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        //
        if (
            $data['type'] === 'especial'
            && (! empty($data['user_id_1'])
                || ! empty($data['user_id_2']))
        ) {
            return $data;
        } elseif ($data['type'] === 'especial') {

            $this->form->addError('user_id_1', 'Debe indicar JS o TR/TRH');
            $this->form->addError('user_id_2', 'Debe indicar JS o TR/TRH');

            $this->halt(); //

        }

        if (
            $data['type'] === 'periodica'
            && (! empty($data['user_id_1'])
                && ! empty($data['user_id_2']))
        ) {

            return $data;
        } elseif ($data['type'] === 'periodica') {

            $this->form->addError('user_id_1', 'Debe indicar JS o TR/TRH');
            $this->form->addError('user_id_2', 'Debe indicar JS o TR/TRH');

            $this->halt(); //

        }

        return $data;
    }
}
