

<div class="space-y-4">
    <div class="flex items-center justify-between">

        <span class="text-sm text-gray-600">{{ __('Total') . ': ' . $pendientes->count() }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-900">{{ __('Estación') }}</th>

                </tr>
            </thead>
            <tbody>
                @forelse($pendientes as $pendiente)
                    <tr class=" border-b hover:bg-gray-50 text-gray-600 odd:bg-white even:bg-slate-50">
                        <td class="px-4 py-2">{{ $pendiente->name }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                            {{ __('No hay inspecciones pendientes') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
