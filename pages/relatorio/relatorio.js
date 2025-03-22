document.addEventListener("DOMContentLoaded", function() {
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'color': [] }, { 'background': [] }],
        ['link', 'image'],
        ['clean']
    ];

    const quill = new Quill('#editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow'
    });

    // Adicionar data atual automaticamente
    const dataAtual = new Date().toLocaleDateString('pt-BR');
    const dataElement = quill.root.querySelector('li');
    if (dataElement) {
        dataElement.textContent = `Data: ${dataAtual}`;
    }

    // On form submission, append Quill's HTML content to a hidden textarea
    document.querySelector('form').onsubmit = function() {
        var quillContent = document.querySelector('input[name=desc]');
        quillContent.value = quill.root.innerHTML;
    };
});