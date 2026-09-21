@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Ringkasan seluruh data dan aktivitas</p>
    </div>
@endpush

<x-app-layout title="Dashboard">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>