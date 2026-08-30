<button
    wire:click="switchLanguage"
    type="button"
    class="flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
    title="{{ __('tutor.language.switch') }}"
>
    <span class="text-gray-700 dark:text-gray-200">
        {{ app()->getLocale() === 'en' ? 'عربي' : 'English' }}
    </span>
</button>
