<section class="space-y-6" x-data="{ theme: localStorage.getItem('theme') || 'system' }">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Interface Theme') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Customize the appearance of the application. Choose Light, Dark, or sync with your system.') }}
        </p>
    </header>

    <div class="flex items-center gap-4">
        <!-- Light Theme Button -->
        <button type="button"
            @click="theme = 'light'; localStorage.setItem('theme', 'light'); document.documentElement.classList.remove('dark')"
            :class="theme === 'light' 
                ? 'bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/50 dark:text-indigo-300 dark:border-indigo-700 ring-1 ring-indigo-500/50' 
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border rounded-md font-semibold text-xs tracking-widest transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Light
        </button>

        <!-- Dark Theme Button -->
        <button type="button"
            @click="theme = 'dark'; localStorage.setItem('theme', 'dark'); document.documentElement.classList.add('dark')"
            :class="theme === 'dark' 
                ? 'bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/50 dark:text-indigo-300 dark:border-indigo-700 ring-1 ring-indigo-500/50' 
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border rounded-md font-semibold text-xs tracking-widest transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            Dark
        </button>

        <!-- System Theme Button -->
        <button type="button"
            @click="theme = 'system'; localStorage.removeItem('theme'); if(window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');"
            :class="theme === 'system' 
                ? 'bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/50 dark:text-indigo-300 dark:border-indigo-700 ring-1 ring-indigo-500/50' 
                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border rounded-md font-semibold text-xs tracking-widest transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            System
        </button>
    </div>
</section>