<!-- Include Quill Styles and Scripts -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<!-- Initialize Quill Editor -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var quill = new Quill('#editor', {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, false] }], // Header dropdown
          ['bold', 'italic', 'underline'],   // Formatting options
          [{ 'list': 'ordered' }, { 'list': 'bullet' }], // Lists
          ['link', 'image'], // Links and images
          ['clean'] // Clear formatting button
        ]
      }
    });

    // On form submission, append Quill's HTML content to a hidden textarea
    document.querySelector('form').onsubmit = function() {
      var quillContent = document.querySelector('input[name=desc]');
      quillContent.value = quill.root.innerHTML; // Store the editor's content into hidden input
    };
  });
</script>

<style>
  /* Style for the Quill editor to match dark background */
  #editor {
    background-color: #333; /* Dark background */
    color: #fff; /* Light text color */
    height: 200px;
    overflow-y: auto;
    padding: 10px;

    /* Ensure default text is NOT bold */
    font-weight: normal; /* Reset default font weight */
  }

  /* Ensure the Quill toolbar has proper contrast */
  .ql-toolbar {
    background-color: #444; /* Slightly lighter background for toolbar */
    border: none;
  }

  /* Ensure Quill container and editor-specific styles are isolated */
  .ql-container {
    border: none;
  }

  /* Fix for bold text inside the editor */
  #editor strong,
  #editor b {
    font-weight: bold !important; /* Force bold weight to work */
  }

  /* Change toolbar icons and dropdown colors to fit dark mode */
  .ql-snow .ql-stroke {
    stroke: #fff; /* White icons */
  }
  .ql-snow .ql-fill {
    fill: #fff; /* White icons */
  }

  /* Custom styling for dropdowns to be visible on dark background */
  .ql-snow .ql-picker {
    color: #fff; /* Text color for dropdown items */
  }
  .ql-snow .ql-picker-options {
    background-color: #444; /* Dark dropdown background */
    color: #fff; /* Light text color */
  }
  .ql-snow .ql-picker-label {
    color: #fff; /* Make the label visible */
  }

  /* Change hover color for dropdown items */
  .ql-snow .ql-picker-options .ql-picker-item:hover {
    background-color: #555; /* Slightly lighter on hover */
  }

  .ql-snow .ql-picker-options .ql-selected {
    background-color: #666; /* Highlight selected item */
    color: #fff; /* Ensure selected item is white */
  }
</style>

<section id="banner">
  <div class="content form">
    <img class="fit logogray" src="./assets/css/images/logo-branco-crop.png">
    <center>
      <form method="post" action="scripts/addparametro/add.php?ordem=<?php echo $_GET['ordem'] . '&motoID=' . $motoid['motoID']; ?>">
        <div>
          <div class="row">
            <div class="col-12">
              <label for="editor">Relatório</label>
              <!-- Quill editor container with dark background and light text -->
              <div id="editor"></div>

              <!-- Hidden textarea to store the content for form submission -->
              <input type="hidden" name="desc">
            </div>                 
            <div class="col-12">
              <br>
              <input id="submit" class="button primary" type="submit" value="Criar Relatório">
            </div>
          </div>
        </div>
      </form>
    </center>
  </div>
</section>
