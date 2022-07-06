$(document).ready(function() {
    // Activate tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Select/Deselect checkboxes
    var checkbox = $('table tbody input[type="checkbox"]');
    $("#selectAll").click(function() {
        if (this.checked) {
            checkbox.each(function() {
                this.checked = true;
            });
        } else {
            checkbox.each(function() {
                this.checked = false;
            });
        }
    });
    checkbox.click(function() {
        if (!this.checked) {
            $("#selectAll").prop("checked", false);
        }
    });

    var elementedit = document.getElementsByClassName("edit");
    var elementdelete = document.getElementsByClassName("delete");

    for (i = 0; i < elementedit.length; i++) {
        elementedit[i].addEventListener("click", function() {
            var foto = this.parentElement.parentElement.getElementsByClassName("foto")[0].children[0].src;
            var grupo = this.parentElement.parentElement.getElementsByClassName("grupo")[0].innerHTML;
            var item = this.parentElement.parentElement.getElementsByClassName("item")[0].innerHTML;
            var parte = this.parentElement.parentElement.getElementsByClassName("parte")[0].innerHTML;

            var pecaId = this.parentElement.parentElement.getElementsByClassName("pecaId")[0].innerHTML;

            document.getElementById("editEmployeeModal").getElementsByClassName("editFoto")[0].src = foto;
            document.getElementById("editEmployeeModal").getElementsByClassName("editGrupo")[0].value = grupo;
            document.getElementById("editEmployeeModal").getElementsByClassName("editItem")[0].value = item;
            document.getElementById("editEmployeeModal").getElementsByClassName("editParte")[0].value = parte;
            document.getElementById("editEmployeeModal").getElementsByClassName("editPecaId")[0].value = pecaId;
        });
    };


    for (i = 0; i < elementdelete.length; i++) {
        elementdelete[i].addEventListener("click", function() {
            var pecaId = this.parentElement.parentElement.getElementsByClassName("pecaId")[0].innerHTML;
            document.getElementById("deleteEmployeeModal").getElementsByClassName("removePecaId")[0].value = pecaId;
        });
    };
});