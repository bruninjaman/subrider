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
          [{ 'font': [] }], // Font family
          [{ 'size': ['small', false, 'large', 'huge'] }], // Font size dropdown
          ['bold', 'italic', 'underline'],   // Formatting options
          [{ 'color': [] }, { 'background': [] }], // Color and background color
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
  /* Style for the modernized form */
  body {
    background-color: #1d1f27; /* Dark background */
    font-family: 'Roboto', sans-serif; /* Modern font */
    color: #e0e0e0; /* Light text */
  }

  /* Style for Quill editor */
  #editor {
    background-color: #333; /* Dark background */
    color: #fff; /* Light text color */
    height: 200px;
    overflow-y: auto;
    padding: 15px;
    font-size: 14px;
    border-radius: 8px; /* Rounded edges for modern feel */
    border: 1px solid #444;
    box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.2); /* Inner shadow */
  }

  /* Styling for toolbar */
  .ql-toolbar {
    background-color: #444; /* Dark toolbar */
    border: 1px solid #555;
    border-radius: 8px 8px 0 0; /* Rounded top */
  }

  /* Button style */
  .button {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  }

  .button:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
  }

  /* Input label style */
  label {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
    display: block;
    color: #e0e0e0; /* Light color */
  }

  /* Form layout adjustments */
  .form {
    margin-top: 20px;
  }

  .row {
    display: flex;
    flex-direction: column;
  }

  .col-12 {
    margin-bottom: 20px;
  }

  .logogray {
    margin-bottom: 20px;
    max-width: 150px;
  }

  /* Adjust Quill icons for dark mode */
  .ql-snow .ql-stroke {
    stroke: #fff;
  }

  .ql-snow .ql-fill {
    fill: #fff;
  }

  .ql-snow .ql-picker-options {
    background-color: #444;
    color: #fff;
  }

  .ql-snow .ql-picker-label {
    color: #fff;
  }

  .ql-snow .ql-picker-options .ql-selected {
    background-color: #666;
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
