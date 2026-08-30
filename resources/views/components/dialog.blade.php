@props(['btn', 'title'])

<button {{ $btn->attributes->class(['btn']) }} onclick="my_modal.showModal()">
    {{ $btn }}
</button>
<dialog id="my_modal" class="modal modal-scroll modal-screen">
    <div class="modal-box flex justify-center">
        <div class="md:basis-9/12 basis-full">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl">{{ $title }}</h1>
                <form method="dialog">
                    <button id="dialog-close" class="btn btn-sm btn-ghost btn-circle"><i class="icon-[mdi--times]"></i></button>
                </form>
            </div>
            <div class="divider"></div>
            {{ $slot }}
        </div>
    </div>
</dialog>