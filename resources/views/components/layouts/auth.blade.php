<x-layouts.main :custom="true">
    <x-navbar />

    <div class="flex-1 flex items-center justify-center p-6 bg-slate-50">
        <div
            class="w-full max-w-5xl bg-white rounded-[32px] shadow-xl shadow-slate-200/50 overflow-hidden flex flex-col md:flex-row min-h-[640px]">
            <aside class="w-full md:w-5/12 bg-[#fef9f5] relative overflow-hidden flex flex-col justify-between p-12">
                <div class="relative z-10">
                    <h1 class="font-display text-4xl font-extrabold leading-tight text-slate-900 mb-4">
                        {{ __('auth.layout.elevate_your') }} <br />
                        <span class="text-brand"> {{ __('auth.layout.impact') }}</span>
                    </h1>
                    <p class="text-slate-500 max-w-xs leading-relaxed">
                        {{ __('auth.layout.subtitle') }}
                    </p>
                </div>

                <div class="relative z-10 mt-5">
                    <div class="flex -space-x-3 mb-4">
                        <div class="size-10 rounded-full ring-4 ring-slate-50 bg-gradient-to-br from-slate-200 to-slate-300 bg-no-repeat bg-center bg-cover"
                            style="
                            background-image: url('https://ngo.academy/uploads/371/Screenshot-2026-06-07-050642.png')
                            ">
                        </div>
                        <div class="size-10 rounded-full ring-4 ring-slate-50 bg-gradient-to-br from-brand/40 to-brand/70 bg-no-repeat bg-center bg-cover"
                            style="
                            background-image: url('https://ngo.academy/uploads/334/ACg8ocKoLMxm_Fkio3SsKAxOz7QmwLPhGC_sETsxKk7pf71GMl9xCaJU%3Ds96-c.jpeg')
                            ">
                        </div>
                        <div class="size-10 rounded-full ring-4 ring-slate-50 bg-gradient-to-br from-slate-400 to-slate-500 bg-no-repeat bg-center bg-cover"
                            style="
                            background-image: url('https://ngo.academy/uploads/69/AnaSameer-Al-Hubaishi.jpg')
                            ">
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">
                        {{ __('auth.layout.trusted_by') }}
                    </p>
                </div>

                <div class="absolute -bottom-16 -left-16 size-64 bg-brand/5 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 -right-12 size-48 bg-slate-200/40 rounded-full blur-2xl"></div>
                <div class="absolute inset-0 md:flex hidden items-center justify-center pointer-events-none ">
                    <img src="{{ asset('assets/images/auth-side.jpg') }}" alt="Online learning illustration"
                        class="w-full h-full object-contain  " width="1024" height="1536" />
                </div>
            </aside>

            <!-- Right Side: Form -->
            <div class="w-full md:w-[52%] p-8 lg:p-16 flex flex-col justify-center bg-white">
                {{ $slot }}
            </div>
        </div>
    </div>

    <x-footer />
</x-layouts.main>
