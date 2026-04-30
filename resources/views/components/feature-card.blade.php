@props(['icon' => '✨', 'title' => '', 'body' => ''])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
    <div class="text-3xl mb-3">{{ $icon }}</div>
    <h3 class="font-semibold text-gray-800 mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-600 leading-relaxed">{{ $body }}</p>
</div>
