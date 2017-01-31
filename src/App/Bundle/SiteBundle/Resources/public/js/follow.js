'use strict';

var resetVisitForm = function () {
    $("#add_visit_weight").removeClass("has-error");
    $("#add_visit_fatMass").removeClass("has-error");
    $("#add_visit_arm").removeClass("has-error");
    $("#add_visit_thigh").removeClass("has-error");
    $("#add_visit_chest").removeClass("has-error");
    $("#add_visit_hip").removeClass("has-error");
    $("#add_visit_size").removeClass("has-error");
    $("#add_visit_date").removeClass("has-error");
}

var resetValueVisitForm = function () {
    $("#add_visit_weight").val("");
    $("#add_visit_fatMass").val("");
    $("#add_visit_arm").val("");
    $("#add_visit_thigh").val("");
    $("#add_visit_chest").val("");
    $("#add_visit_hip").val("");
    $("#add_visit_size").val("");
    $("#add_visit_date").val("");
}

$( document ).ready(function() {
    $(document).on("click", "#menu-toggle, .mask, #close, #menu-toggle-mobile", function() {
        $("#wrapper").toggleClass("toggled");
        if($("#wrapper").hasClass("toggled")) {
            $(".mask").hide();
        } else {
            $(".mask").show();
        }
    });
    $(document).on("click", ".redirectToFollow", function() {
        window.location.href = $(this).data("url");
    });

    if($(".js-datepicker").length) {
        $(".js-datepicker").datepicker({
            dateFormat: "yy-mm-dd"
        });
    }

    if($("#addvisit").length) {
        $(document).on("submit", "#addvisit", function(e) {
            e.preventDefault();
            var errors = 0;
            resetVisitForm();

            if (!$("#add_visit_weight").val()) {
                $("#add_visit_weight").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_fatMass").val()) {
                $("#add_visit_fatMass").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_arm").val()) {
                $("#add_visit_arm").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_thigh").val()) {
                $("#add_visit_thigh").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_chest").val()) {
                $("#add_visit_chest").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_hip").val()) {
                $("#add_visit_hip").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_size").val()) {
                $("#add_visit_size").addClass("has-error");
                errors++;
            }
            if (!$("#add_visit_date").val()) {
                $("#add_visit_date").addClass("has-error");
                errors++;
            }

            if (errors === 0) {
                var formData = new FormData($(this)[0]);
                $("#add_visit_save").addClass("hidden");
                var spinner = new Spinner().spin();
                $("#spinner-modal").append(spinner.el);
                $(".overlaymodal").show();

                $.ajax({
                    url: "/add-visit",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false
                }).done(function (data) {
                    $(".overlaymodal").hide();
                    $("#spinner-modal").html("");
                    $("#add_visit_save").removeClass("hidden");
                    if (data.error_code === 0) {
                        $(".msg_error").html("").hide();
                        weightChart.data.labels[nb_visit] = data.data.date;
                        weightChart.data.datasets[0].data[nb_visit] = data.data.weight;
                        weightChart.update();
                        massGChart.data.labels[nb_visit] = data.data.date;
                        massGChart.data.datasets[0].data[nb_visit] = data.data.massG;
                        massGChart.update();
                        $('.weightUser').html(data.data.weight);
                        $('.massGUser').html(data.data.massG)

                        $('#addvisit .close').click();
                        // Update chart & show message
                    } else {
                        $(".msg_error").html(data.message).show();
                    }
                });
            }

            return false;
        });
    }

    $(document).on("keyup", "#search", function() {
        // Declare variables
        var input, filter, tr, td, i;
        input = $(this);
        filter = input.val().toUpperCase();
        tr = $("#patients tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < $("#patients tr").length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

});