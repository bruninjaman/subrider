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
            var foto = this.parentElement.parentElement.getElementsByClassName("rowfoto")[0].children[0].src;
            var proprietario = this.parentElement.parentElement.getElementsByClassName("rowproprietario")[0].innerHTML;
            var endereco = this.parentElement.parentElement.getElementsByClassName("rowendereco")[0].innerHTML;
            var marca = this.parentElement.parentElement.getElementsByClassName("rowmarca")[0].innerHTML;
            var placa = this.parentElement.parentElement.getElementsByClassName("rowplaca")[0].innerHTML;
            var modelo = this.parentElement.parentElement.getElementsByClassName("rowmodelo")[0].innerHTML;
            var ano = this.parentElement.parentElement.getElementsByClassName("rowano")[0].innerHTML;
            var km = this.parentElement.parentElement.getElementsByClassName("rowkm")[0].innerHTML;

            var motoid = this.parentElement.parentElement.getElementsByClassName("motoid")[0].innerHTML;

            document.getElementById("editEmployeeModal").getElementsByClassName("editimage")[0].src = foto;
            document.getElementById("editEmployeeModal").getElementsByClassName("editproprietario")[0].value = proprietario;
            document.getElementById("editEmployeeModal").getElementsByClassName("editendereco")[0].value = endereco;
            document.getElementById("editEmployeeModal").getElementsByClassName("editmarca")[0].value = marca;
            document.getElementById("editEmployeeModal").getElementsByClassName("editplaca")[0].value = placa;
            document.getElementById("editEmployeeModal").getElementsByClassName("editmodelo")[0].value = modelo;
            document.getElementById("editEmployeeModal").getElementsByClassName("editano")[0].value = ano;
            document.getElementById("editEmployeeModal").getElementsByClassName("editkm")[0].value = km;
            document.getElementById("editEmployeeModal").getElementsByClassName("editmotoid")[0].value = motoid;
        });
    };


    for (i = 0; i < elementdelete.length; i++) {
        elementdelete[i].addEventListener("click", function() {
            var motoid = this.parentElement.parentElement.getElementsByClassName("motoid")[0].innerHTML;
            document.getElementById("deleteEmployeeModal").getElementsByClassName("editmotoid3")[0].value = motoid;
        });
    };
});