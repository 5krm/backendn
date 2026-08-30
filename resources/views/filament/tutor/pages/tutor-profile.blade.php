<x-filament-panels::page>
    <div class="space-y-8" dir="rtl">
        <!-- Modern Header Section -->
        <div class="relative rounded-3xl overflow-hidden bg-white dark:bg-gray-900 shadow-lg border border-gray-200 dark:border-gray-800">
             <!-- Cover Background -->
            <div class="h-40 bg-gradient-to-l from-primary/80 to-secondary/80 relative overflow-hidden">
                <div class="absolute inset-0 bg-grid-white/10 mask-image-gradient-to-b"></div>
                <!-- Abstract Shapes -->
                <div class="absolute -left-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -right-10 bottom-0 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            </div>
            
            <div class="px-8 pb-8">
                <div class="relative flex flex-col md:flex-row items-end -mt-16 mb-6 gap-6">
                    <!-- Avatar -->
                    <div class="relative group">
                        <div class="h-32 w-32 rounded-2xl border-[6px] border-white dark:border-gray-900 bg-white dark:bg-gray-800 shadow-xl overflow-hidden flex items-center justify-center transform transition group-hover:scale-105">
                             @if($tutor->profile_image)
                                <img src="{{ $tutor->profile_image }}" alt="{{ $tutor->user->name }}" class="h-full w-full object-cover">
                             @else
                                <span class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-bl from-primary to-secondary">
                                    {{ strtoupper(substr($tutor->user->name, 0, 1)) }}
                                </span>
                             @endif
                        </div>
                         <!-- Active Status Indicator -->
                        <div class="absolute bottom-2 left-2 h-6 w-6 {{ $tutor->is_active ? 'bg-green-500' : 'bg-red-500' }} border-4 border-white dark:border-gray-900 rounded-full shadow-sm" title="{{ $tutor->is_active ? 'نشط' : 'غير نشط' }}"></div>
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="flex-1 pb-2 text-center md:text-right">
                         <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $tutor->user->name }}</h2>
                         <div class="flex items-center justify-center md:justify-start gap-2 mt-1 text-gray-500 dark:text-gray-400">
                            <span class="icon-[heroicons--academic-cap] w-4 h-4"></span>
                            <span class="text-sm font-medium">{{ $tutor->localized_specialization }}</span>
                         </div>
                    </div>

                    <!-- Stats Badges -->
                     <div class="flex flex-wrap justify-center md:justify-end gap-3 pb-3">
                        @if($tutor->experience_years)
                             <div class="badge badge-lg bg-primary/10 text-primary border-primary/20 gap-2 px-4 py-3 h-auto">
                                <span class="icon-[heroicons--briefcase] w-5 h-5"></span>
                                {{ $tutor->experience_years }} سنوات خبرة
                             </div>
                        @endif
                        <span class="badge badge-lg {{ $tutor->is_active ? 'bg-green-50 text-green-600 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-600 border-red-200' }} gap-2 px-4 py-3 h-auto">
                            <span class="icon-[heroicons--check-badge] w-5 h-5"></span>
                            {{ $tutor->is_active ? 'حساب نشط' : 'حساب غير نشط' }}
                        </span>
                     </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content (Bio) -->
            <div class="lg:col-span-2 space-y-8">
                @if($tutor->user?->localized_bio)
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="icon-[heroicons--user] w-5 h-5 text-primary"></span>
                            {{ app()->getLocale() === 'en' ? 'About the Instructor' : 'نبذة عن المدرس' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tutor->user->localized_bio }}</p>
                    </div>
                </div>
                @endif

                <!-- Contact Info -->
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="icon-[heroicons--phone] w-5 h-5 text-primary"></span>
                            معلومات الاتصال
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                            <div class="p-3 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <span class="icon-[heroicons--envelope] w-6 h-6"></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">البريد الإلكتروني</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $tutor->user->email }}</p>
                            </div>
                        </div>

                        @if($tutor->user?->phone)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50">
                            <div class="p-3 rounded-xl bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                <span class="icon-[heroicons--device-phone-mobile] w-6 h-6"></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">رقم الهاتف</p>
                                <p class="text-gray-900 dark:text-white font-medium" dir="ltr">{{ $tutor->user->phone }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar (Social Media & Other Info) -->
            <div class="space-y-8">
                @php
                    $socialLinks = $tutor->user?->socialLinks ?? collect();
                @endphp
                 @if($socialLinks->isNotEmpty())
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="icon-[heroicons--share] w-5 h-5 text-primary"></span>
                            وسائل التواصل الاجتماعي
                        </h3>
                    </div>
                    <div class="p-6 flex flex-col gap-3">
                        @foreach($socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline gap-2 w-full justify-start normal-case">
                             <x-social-platform-icon :platform="$link->platform ?? ($link->getAttributes()['platform'] ?? null)" class="w-5 h-5" />
                            {{ $link->platformLabel() }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
