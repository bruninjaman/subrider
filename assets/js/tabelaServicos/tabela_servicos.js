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
            var item = this.parentElement.parentElement.getElementsByClassName("item")[0].innerHTML;
            var tipo = this.parentElement.parentElement.getElementsByClassName("tipo")[0].innerHTML;

            var servicoId = this.parentElement.parentElement.getElementsByClassName("servicoId")[0].innerHTML;

            document.getElementById("editEmployeeModal").getElementsByClassName("editItem")[0].value = item;
            document.getElementById("editEmployeeModal").getElementsByClassName("editTipo")[0].value = tipo;
            document.getElementById("editEmployeeModal").getElementsByClassName("editServicoId")[0].value = servicoId;
        });
    };


    for (i = 0; i < elementdelete.length; i++) {
        elementdelete[i].addEventListener("click", function() {
            var servicoId = this.parentElement.parentElement.getElementsByClassName("servicoId")[0].innerHTML;
            document.getElementById("deleteEmployeeModal").getElementsByClassName("removeServicoId")[0].value = servicoId;
        });
    };
    $()
});