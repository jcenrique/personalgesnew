@php
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentColor;
    use Filament\Support\View\Components\ButtonComponent;
    use Guava\Calendar\Enums\Context;
@endphp

<x-filament-widgets::widget>
    <x-filament::section :after-header="$this->getCachedHeaderActionsComponent()" :footer="$this->getCachedFooterActionsComponent()">

        <style>
            .ec-event.ec-preview,
            .ec-now-indicator {
                z-index: 30;
            }

            .fc-event-time,
            .ec-time,
            .ec-event-time {
                display: none !important;
            }
        </style>

        @if ($heading = $this->getHeading())
            <x-slot name="heading">
                {{ $this->getHeading() }}
            </x-slot>
        @endif

        <div wire:poll.20s="$dispatch('refreshResources')">
            <div wire:ignore x-load x-load-src="{{ FilamentAsset::getAlpineComponentSrc('calendar', 'guava/calendar') }}"
                x-data="calendar({
                    view: @js($this->getCalendarView()),
                    locale: @js($this->getLocale()),
                    firstDay: @js($this->getFirstDay()),
                    dayMaxEvents: @js($this->getDayMaxEvents()),
                    eventContent: @js($this->getEventContentJs()),
                    eventClickEnabled: @js($this->isEventClickEnabled()),
                    eventDragEnabled: @js($this->isEventDragEnabled()),
                    eventResizeEnabled: @js($this->isEventResizeEnabled()),
                    noEventsClickEnabled: @js($this->isNoEventsClickEnabled()),
                    dateClickEnabled: @js($this->isDateClickEnabled()),
                    dateSelectEnabled: @js($this->isDateSelectEnabled()),
                    datesSetEnabled: @js($this->isDatesSetEnabled()),
                    viewDidMountEnabled: @js($this->isViewDidMountEnabled()),
                    eventAllUpdatedEnabled: @js($this->isEventAllUpdatedEnabled()),
                    hasDateClickContextMenu: @js($this->hasContextMenu(Context::DateClick)),
                    hasDateSelectContextMenu: @js($this->hasContextMenu(Context::DateSelect)),
                    hasEventClickContextMenu: @js($this->hasContextMenu(Context::EventClick)),
                    hasNoEventsClickContextMenu: @js($this->hasContextMenu(Context::NoEventsClick)),
                    resources: @js($this->getResourcesJs()),
                    resourceLabelContent: @js($this->getResourceLabelContentJs()),
                    theme: @js($this->getTheme()),
                    options: @js($this->getOptions()),
                    eventAssetUrl: @js(FilamentAsset::getAlpineComponentSrc('calendar-event', 'guava/calendar')),
                })" @class(array_merge(FilamentColor::getComponentClasses(ButtonComponent::class, 'primary'),
                        [
                            'mt-3 p-2 overflow-hidden rounded-xl ring-1 ring-gray-950/10 dark:ring-white/10',
                        ]))>
                <div data-calendar></div>
                @if ($this->hasContextMenu())
                    <x-guava-calendar::context-menu />
                @endif
            </div>
            <div class="mt-4">
                <span
                    class="inline-flex items-center rounded-md bg-fuchsia-500 px-2 py-1 text-xs font-medium text-gray-900 inset-ring inset-ring-gray-500/10">{{ __('Solicitado') }}</span>
                <span
                    class="inline-flex items-center rounded-md bg-yellow-200 px-2 py-1 text-xs font-medium text-gray-900 inset-ring inset-ring-red-600/10">{{ __('Aprobado') }}</span>

                <span
                    class="inline-flex items-center rounded-md bg-green-200 px-2 py-1 text-xs font-medium text-green-900 inset-ring inset-ring-red-600/10">{{ __('Cursos') }}</span>

                <span
                    class="inline-flex items-center rounded-md bg-indigo-200 px-2 py-1 text-xs font-medium text-indigo-900 inset-ring inset-ring-red-600/10">{{ __('Reconocimiento médico') }}</span>

            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
