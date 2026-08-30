<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        @push('meta')
            <meta name="robots" content="noindex, nofollow">
        @endpush
        <div class="max-w-7xl mx-auto">

            <x-my_courses.greeting-student :user="$user" :in-progress="$totals['in_progress']" />
            <x-my_courses.student-totals :totals="$totals" />
            @isset($lesson)
                <x-my_courses.last-lesson :lesson="$lesson" :progress="$progress" />
            @endisset
        </div>
        <div class="mb-10">
            <livewire:my-courses-tabs :enrollments="$enrollments" :certificates="$certificates" :wishlistCourses="$wishlistCourses" :ratings="$ratings"/>
        </div>
    </div>
</x-layouts.app>

<script>
    let notes = document.getElementsByClassName('note');

    for (var i = 0; i < notes.length; i++) {
        const note = notes[i].dataset.note;
        const shortNote = shorten(note);
        if (shortNote.length == note.length) {
            let readmore = document.querySelector('.readmore');
            readmore.style.display = 'none';
        }

        notes[i].querySelector('.content').innerHTML = shortNote;
    }

    function shorten(note) {
        if (!note || typeof note !== 'string') return '';
        return note.split(' ').slice(0, 15).join(' ');
    }

    function toggleReadmore(key) {
        let readmore = document.querySelector(`#readmore-${key}`);
        let note = document.querySelector(`#note-${key}`);

        let isCollapsed = readmore.getAttribute('collapsed');
        if (isCollapsed == 'true') {
            note.querySelector(`.content`).innerHTML = note.dataset.note;
            readmore.setAttribute('collapsed', false);
            readmore.innerHTML = "{{ __('base.show_less') }}";
        } else {
            note.querySelector(`.content`).innerHTML = shorten(note.dataset.note);
            readmore.setAttribute('collapsed', true);
            readmore.innerHTML = "{{ __('base.show_more') }}";
        }
    }
</script>
