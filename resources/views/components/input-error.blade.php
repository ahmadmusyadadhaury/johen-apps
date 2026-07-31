@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-400 dark:text-red-400 space-y-1']) }} role="alert">
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1">
                <svg class="w-4 h-4 flex-shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
