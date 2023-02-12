$(document).ready(function () {
    $("#dateValue").click(function () {
        $(this).attr("contenteditable", "true").addClass("editing");
    });
    $("#dateValue").on("input", function () {
        $(this).css("color", "yellow");

        var newData = $(this).text().trim();
        var dateRegex = /^\d{0,2}\/\d{0,2}\/\d{0,4}$/;
        if (!dateRegex.test(newData)) {
            $("#errorMessage").text("Invalid date format. Please enter a date in the format dd/mm/yyyy.").show();
            return;
        }

        var parts = newData.split("/");
        if (parts[0].length > 2 || parts[1].length > 2 || parts[2].length > 4) {
            $("#errorMessage").text("Invalid date format. Please enter a date in the format dd/mm/yyyy.").show();
            return;
        }

        var day = parseInt(parts[0]);
        var month = parseInt(parts[1]);
        var year = parseInt(parts[2]);
        if (day > 31 || day < 1 || month > 12 || month < 1 || year < 1) {
            $("#errorMessage").text("Invalid date. Please enter a valid day, month, and year.").show();
            return;
        }

        $("#errorMessage").text("").hide();
    });

    var urlParams = new URLSearchParams(window.location.search);
    var ordem = urlParams.get("ordem");
    $("#dateValue").keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            $(this).removeAttr("contenteditable").removeClass("editing");

            var newData = $(this).text().trim();
            if ($("#errorMessage").text().trim() != "") {
                return;
            }

            $.ajax({
                type: "POST",
                url: "ajax/update_date.php",
                data: {
                    ordem: ordem,
                    newData: newData
                },
                success: function (result) {
                    console.log("Data updated successfully: " + result);
                    $("#dateValue").css("color", "white");
                },
                error: function (xhr, status, error) {
                    console.log("Error updating data: " + error);
                    $("#errorMessage").text("Error updating data. Please try again later.").show();
                }
            });
        }
    });
});