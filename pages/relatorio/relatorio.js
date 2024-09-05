document.addEventListener("DOMContentLoaded", function() {
    const quill = new Quill('#editor', {
        theme: 'snow'
      });

    // On form submission, append Quill's HTML content to a hidden textarea
    document.querySelector('form').onsubmit = function() {
      var quillContent = document.querySelector('input[name=desc]');
      quillContent.value = quill.root.innerHTML; // Store the editor's content into hidden input
    };
  });