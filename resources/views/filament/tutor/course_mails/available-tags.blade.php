<div class="flex flex-wrap gap-2">
    @foreach($tags as $tag)
        <button
            type="button"
            style="padding: 0.25rem 0.75rem; font-size: 0.875rem; border-radius: 9999px; background-color: #f3f4f6; transition: background-color 0.2s;margin:3px auto;"
            x-on:click="
            const editorCtn = document.querySelector('[x-data^=\'richEditorFormComponent\']:has(#course-content-editor)');                
                if (editorCtn) {
                    // Access the Alpine data object for this element
                    const alpineData = Alpine.$data(editorCtn);                    
                    // Retrieve the TipTap editor instance via Filament's helper
                    const editor = alpineData?.$getEditor();
                    
                    if (editor) {
                        let result = editor.chain().focus().insertContent('{{ $tag }}').run();                    
                        editor.view.dispatch(editor.view.state.tr);
                    } else {
                        console.error('TipTap instance not ready yet.');
                    }
                } else {
                    console.error('Could not find richEditorFormComponent element in DOM.');
                }
            "
            class="px-3 py-1 text-sm rounded-full bg-gray-100 hover:bg-gray-200 transition"
        >
            {{ $tag }}
        </button>
    @endforeach
</div>