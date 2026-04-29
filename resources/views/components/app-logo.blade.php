@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $sizeClasses }} object-contain rounded-md bg-white" />
@else
    <div class="{{ $sizeClasses }} flex items-center justify-center rounded-md bg-indigo-600 text-white font-bold">
        {{ \Illuminate\Support\Str::of($alt)->substr(0, 1)->upper() }}
    </div>
@endif
