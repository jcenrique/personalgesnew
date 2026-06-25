<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">{{ $course->name }}</h3>
        <span class="text-sm text-gray-600">Total: {{ $attendees->count() }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-900">{{ __('Nombre') }}</th>
                    <th class="px-4 py-2 text-left text-gray-900">{{ __('Email') }}</th>
                    <th class="px-4 py-2 text-left text-gray-900">{{ __('Roles') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendees as $attendee)
                    <tr class="border-b hover:bg-gray-50 text-gray-600">
                        <td class="px-4 py-2">{{ $attendee->name }}</td>
                        <td class="px-4 py-2">{{ $attendee->email }}</td>
                        <td class="px-4 py-2">{{ $attendee->roles->pluck('name')->sort()->join(', ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                            {{ __('No hay asistentes pendientes') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
