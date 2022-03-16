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
            var proprietario = this.parentElement.parentElement.getElementsByClassName("prop")[0].innerHTML;
            var endereco = this.parentElement.parentElement.getElementsByClassName("ender")[0].innerHTML;
            var marca = this.parentElement.parentElement.getElementsByClassName("marca")[0].innerHTML;
            var placa = this.parentElement.parentElement.getElementsByClassName("placa")[0].innerHTML;
            var modelo = this.parentElement.parentElement.getElementsByClassName("model")[0].innerHTML;
            var ano = this.parentElement.parentElement.getElementsByClassName("ano")[0].innerHTML;
            var km = this.parentElement.parentElement.getElementsByClassName("km")[0].innerHTML;

            var motoid = this.parentElement.parentElement.getElementsByClassName("motoId")[0].innerHTML;

            document.getElementById("editEmployeeModal").getElementsByClassName("editFoto")[0].src = foto;
            document.getElementById("editEmployeeModal").getElementsByClassName("editProp")[0].value = proprietario;
            document.getElementById("editEmployeeModal").getElementsByClassName("editEnder")[0].value = endereco;
            document.getElementById("editEmployeeModal").getElementsByClassName("editMarca")[0].value = marca;
            document.getElementById("editEmployeeModal").getElementsByClassName("editPlaca")[0].value = placa;
            document.getElementById("editEmployeeModal").getElementsByClassName("editModel")[0].value = modelo;
            document.getElementById("editEmployeeModal").getElementsByClassName("editAno")[0].value = ano;
            document.getElementById("editEmployeeModal").getElementsByClassName("editKm")[0].value = km;
            document.getElementById("editEmployeeModal").getElementsByClassName("editMotoId")[0].value = motoid;
        });
    };


    for (i = 0; i < elementdelete.length; i++) {
        elementdelete[i].addEventListener("click", function() {
            var motoid = this.parentElement.parentElement.getElementsByClassName("motoId")[0].innerHTML;
            document.getElementById("deleteEmployeeModal").getElementsByClassName("removeMotoId")[0].value = motoid;
        });
    };
});