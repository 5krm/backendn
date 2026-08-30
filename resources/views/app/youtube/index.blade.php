<x-layouts.main>
    <main class="h-screen grid place-items-center bg-gray-100">
        <div class="bg-white w-full max-w-xl p-6 rounded-lg border">
            <div class="flex items-center gap-1 justify-center">
                <span class="font-bold text-2xl">Youtube</span>
                <i class="icon-[mdi--youtube] text-5xl text-red-500"></i>
            </div>
            <p class="text-center">Grant access to your youtube account</p>
            @if ($mode == 'request')
                <div class="text-center mt-4">
                    <a href="{{ $authUrl }}" target="_blank"
                        class="inline-block border border-red-500 text-red-500 px-4 py-2 rounded mt-4 hover:bg-red-500 hover:text-white transition-colors">
                        Grant Access
                    </a>
                </div>
            @else
                <div>
                    <p class="text-center mt-4">
                        <span class="text-green-600">Authorized</span>
                    </p>
                </div>
            @endif
        </div>
    </main>
</x-layouts.main>
