document.addEventListener("DOMContentLoaded", function() {
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'bulletedList',
                'numberedList',
                '|',
                'undo',
                'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Parágrafo', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Título 1', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Título 2', class: 'ck-heading_heading3' }
                ]
            }
        })
        .then(editor => {
            // Quando o formulário for enviado, pega o conteúdo do editor
            document.querySelector('form').addEventListener('submit', function() {
                document.querySelector('input[name=desc]').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });
});