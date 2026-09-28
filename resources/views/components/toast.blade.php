<template x-for="(item, index) in $store.toast.items" :key="item.id">
    <div
        x-data="{
            show: true,
            remove() { $store.toast.remove(item.id) }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-4 scale-95"
        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0 scale-100"
        x-transition:leave-end="opacity-0 translate-x-4 scale-95"
        @click="remove"
        class="relative flex w-full min-w-0 items-start gap-3 p-4 rounded-2xl shadow-xl border cursor-pointer overflow-hidden max-w-full"
        :class="{
            'bg-emerald-50 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800': item.type === 'success',
            'bg-red-50 border-red-200 dark:bg-red-950/40 dark:border-red-800': item.type === 'error',
            'bg-amber-50 border-amber-200 dark:bg-amber-950/40 dark:border-amber-800': item.type === 'warning',
            'bg-blue-50 border-blue-200 dark:bg-blue-950/40 dark:border-blue-800': item.type === 'info',
        }"
    >
        <div
            class="flex h-8 w-8 items-center justify-center rounded-xl shrink-0"
            :class="{
                'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400': item.type === 'success',
                'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400': item.type === 'error',
                'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': item.type === 'warning',
                'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': item.type === 'info',
            }"
        >
            <template x-if="item.type === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
            <template x-if="item.type === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </template>
            <template x-if="item.type === 'warning'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </template>
            <template x-if="item.type === 'info'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            </template>
        </div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p
                class="text-sm font-semibold"
                :class="{
                    'text-emerald-800 dark:text-emerald-300': item.type === 'success',
                    'text-red-800 dark:text-red-300': item.type === 'error',
                    'text-amber-800 dark:text-amber-300': item.type === 'warning',
                    'text-blue-800 dark:text-blue-300': item.type === 'info',
                }"
                x-text="item.message"
            ></p>
        </div>
        <button @click.stop="remove" class="shrink-0 p-0.5 rounded-lg opacity-40 hover:opacity-100 transition-opacity"
            :class="{ 'text-emerald-600 dark:text-emerald-400': item.type === 'success', 'text-red-600 dark:text-red-400': item.type === 'error', 'text-amber-600 dark:text-amber-400': item.type === 'warning', 'text-blue-600 dark:text-blue-400': item.type === 'info' }">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div
            class="absolute bottom-0 left-0 h-0.5 rounded-full"
            :class="{
                'bg-emerald-400 dark:bg-emerald-600': item.type === 'success',
                'bg-red-400 dark:bg-red-600': item.type === 'error',
                'bg-amber-400 dark:bg-amber-600': item.type === 'warning',
                'bg-blue-400 dark:bg-blue-600': item.type === 'info',
            }"
            x-init="
                $el.style.width = '100%';
                $el.style.transition = 'width ' + (item.duration || 4000) + 'ms linear';
                requestAnimationFrame(() => { $el.style.width = '0%' });
            "
        ></div>
    </div>
</template>
