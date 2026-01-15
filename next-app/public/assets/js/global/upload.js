function doUpload(input, preview) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.hide();
            preview.attr('src', e.target.result);
            preview.fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$('input[type="file"]').change(function() {
    var preview = $(this).parent().find('img');
    doUpload(this, preview);
});  
