$(document).ready(function() {
    $("#editableproprietario").click(function() {
        $(this).attr("contenteditable", "true").addClass("editing");
    });

    $("#editableproprietario").on("input", function() {
        $(this).css("color", "yellow");

        var newData = $(this).text().trim();
        if (newData.length === 0) {
            $("#errorMessage").text("Proprietário não pode estar vazio.").show();
            return;
        }

        $("#errorMessage").text("").hide();
    });

    var urlParams = new URLSearchParams(window.location.search);
    var ordem = urlParams.get("ordem");

    $("#editableproprietario").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $(this).removeAttr("contenteditable").removeClass("editing");

            var newData = $(this).text().trim();
            if ($("#errorMessage").text().trim() != "") {
                return;
            }

            $.ajax({
                type: "POST",
                url: "ajax/update_proprietario.php",
                data: {
                    ordem: ordem,
                    newProprietario: newData
                },
                success: function(result) {
                    console.log("Proprietário updated successfully: " + result);
                    $("#editableproprietario").css("color", "white");
                },
                error: function(xhr, status, error) {
                    console.log("Error updating Proprietário: " + error);
                    $("#errorMessage").text("Erro ao atualizar o proprietário. Tente novamente.").show();
                }
            });
        }
    });
});