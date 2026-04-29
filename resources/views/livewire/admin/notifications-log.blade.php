<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Notifications Log') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div class="grid sm:grid-cols-3 gap-3 mb-4">
                    <select wire:model.live="status" class="border-gray-300 rounded">
                        <option value="">{{ __('All statuses') }}</option>
                        <option value="queued">{{ __('Queued') }}</option>
                        <option value="sent">{{ __('Sent') }}</option>
                        <option value="delivered">{{ __('Delivered') }}</option>
                        <option value="failed">{{ __('Failed') }}</option>
                    </select>
                    <select wire:model.live="template_key" class="border-gray-300 rounded">
                        <option value="">{{ __('All templates') }}</option>
                        @foreach ($templates as $t)
                            <option value="{{ $t->key }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Time') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('To') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('User') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Template') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Status') }}</th>
                                <th class="px-3 py-2 text-left text-xs uppercase">{{ __('Error') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($logs as $l)
                                <tr>
                                    <td class="px-3 py-2 text-xs">{{ $l->created_at->format('d M H:i') }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $l->recipient }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $l->user->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $l->template_key }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        <span class="px-2 py-1 rounded
                                            @if($l->status->value === 'sent') bg-blue-100 text-blue-700
                                            @elseif($l->status->value === 'delivered') bg-green-100 text-green-700
                                            @elseif($l->status->value === 'failed') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                            {{ $l->status->value }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-red-700 max-w-xs truncate" title="{{ $l->error }}">{{ $l->error }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">{{ __('No notifications yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</div>
