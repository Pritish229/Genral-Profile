@props([
    'id' => 'editor-' . uniqid(),
    'name' => 'content',
    'value' => '',
    'menubar' => false,
])

<div>
    <label for="label">{{ $label }}</label>
    <textarea id="{{ $id }}" name="{{ $name }}">{{ $value }}</textarea>
</div>

@once
    @push('scripts')
        <script>
            // ✅ Global helper so you don’t repeat boilerplate
            window.initTiny = function (id, config = {}) {
                let baseConfig = {
                    selector: '#' + id,
                    height: 300,
                    menubar: `@json($menubar)`,
                    plugins: ['advlist','autolink','lists','link','image','fullscreen','preview' , 'wordcount', 'code'] , 
                    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | preview code ',
                    setup: function (editor) {
                        // Add default set/getContent helpers
                        editor.on('init', function () {
                            window.tiny = window.tiny || {};
                            window.tiny[id] = {
                                setContent: function (html) { editor.setContent(html); },
                                getContent: function () { return editor.getContent(); }
                            };
                        });
                    }
                };
                // Merge user config with defaults
                return tinymce.init(Object.assign(baseConfig, config));
            }
        </script>
    @endpush
@endonce
