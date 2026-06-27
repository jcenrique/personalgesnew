<?php

namespace App\Filament\Tables\Columns;

use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Tables\Columns\Column;

class BienMalColumn extends Column implements HasEmbeddedView
{
    public function toEmbeddedHtml(): string
    {

        ob_start(); ?>

        <div class="flex items-center justify-center gap-2 mr-4">
            <?php
            if (is_string($this->getState())) {
                $parts = explode(' / ', $this->getState());
                if (count($parts) === 2) {
                    $bien = trim($parts[0]);
                    $mal = trim($parts[1]);
                } else {
                    $bien = $this->getState();
                    $mal = '';
                }
            } else {
                $bien = '';
                $mal = '';
            }
            ?>

                <div class="bienmal-green">
                    <?= e($bien) ?>
                </div>
                <div>
                    <?php
                    if ($bien !== '' && $mal !== '') {
                        echo '/';
                    } else {
                        echo __('No procede');
                    }
                    ?>
                </div>
                <div class="bienmal-red">
                    <?= e($mal) ?>
                </div>

        </div>

    <?php return ob_get_clean();
    }
}
