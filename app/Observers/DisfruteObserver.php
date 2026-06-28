<?php

namespace App\Observers;

use App\Models\Disfrute;

class DisfruteObserver
{
    /**
     * Handle the Disfrute "created" event.
     */
    public function created(Disfrute $disfrute): void
    {
        $this->refreshCalendar();
    }

    /**
     * Handle the Disfrute "updated" event.
     */
    public function updated(Disfrute $disfrute): void
    {
        // disparar el evento de livewire para actualizar el calendario
        $this->refreshCalendar();

    }

    /**
     * Handle the Disfrute "deleted" event.
     */
    public function deleted(Disfrute $disfrute): void
    {
        $this->refreshCalendar();
    }

    /**
     * Handle the Disfrute "restored" event.
     */
    public function restored(Disfrute $disfrute): void
    {
        $this->refreshCalendar();
    }

    /**
     * Handle the Disfrute "force deleted" event.
     */
    public function forceDeleted(Disfrute $disfrute): void
    {
        $this->refreshCalendar();
    }

    protected function refreshCalendar()
    {
        //  dd('refreshCalendar');

    }
}
