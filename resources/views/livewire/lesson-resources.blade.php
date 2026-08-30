<div>
    @if ($resources->count() == 0)
        <div class="text-center text-[#999] p-6 ">
            <div class="flex justify-center mb-1 ">
                <object data="/assets/svg/no_data.svg" width="60" height="60" class="me-2"> </object>
            </div>
            {{ __('base.noData') }}
        </div>
    @endif
    <ul class="py-5 space-y-1 text-gray-500 list-inside divide-y">
        @foreach ($resources as $resource)
        <?  $key = App\Enums\FileType::fromMimeType($resource->media->first()?->mime_type)->value;
            $fileType = App\Enums\FileType::names()[$key];
            
         ?>
            <li class="flex items-center justify-between py-2">
                <div class="flex">
                    <object
                        data="/assets/svg/file_types/{{ $fileType }}.svg"
                        width="23" height="23" class="me-2"> </object>
                    {{ $resource->title }}
                </div>
                <a class="link flex items-center"
                    href="{{ route('app.lesson.resources.download', $resource->id) }}">
                    <i class="text-[grey] icon-[mdi--tray-arrow-down] text-lg me-1 size-5"></i>
                </a>
            </li>
        @endforeach
    </ul>
</div>
