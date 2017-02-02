'use strict';

var formatTime = function(unixTimestamp) {
    var dt = new Date(unixTimestamp * 1000);

    var hours = dt.getHours();
    var minutes = dt.getMinutes();
    var seconds = dt.getSeconds();

    // the above dt.get...() functions return a single digit
    // so I prepend the zero here when needed
    if (hours < 10)
        hours = '0' + hours;

    if (minutes < 10)
        minutes = '0' + minutes;

    if (seconds < 10)
        seconds = '0' + seconds;

    return hours + ":" + minutes;
}

var addLineIntable = function(data) {
    return "<tr><td>" + data.data.date + "</td><td>" + data.data.arm + "</td><td>" + data.data.thigh + "</td><td>" + data.data.chest + "</td><td>" + data.data.size + "</td><td>" + data.data.hip + "</td></tr>";
}

var addLineInNewAgenda = function(data, nbLine) {
    var labelActivity = 'activité';
    if(nbLine > 1) {
        labelActivity = labelActivity + 's';
    }
    var dataString = "<tr id='day_" + data.data.dayInt + "' class='day_" + data.data.dayInt + " last-activity' data-starttime='" + data.data.startTime + "' data-endtime='" + data.data.endTime + "' >" +
        "<td class='agenda-date' class='active' rowspan='1'> " +
            "<div class='dayofweek'>" + data.data.dayString + "</div>" +
            "<div class='shortdate text-muted activity'>" + nbLine +  " " + labelActivity + "</div>" +
        "</td>" +
        "<td class='agenda-time' style='background-color:" + data.data.color + "'>" +
        formatTime(data.data.startTime) + '-' + formatTime(data.data.endTime) +
        "</td>" +
        "<td class='agenda-events' style='background-color:" + data.data.color + "'>" +
            "<div class='agenda-event'>" +
            data.data.activity +
        "</div></td>";

        if($("#admin").length) {
            dataString = dataString + "<td class='action-delete'><button type='button' class='btn btn-remove' data-id='" + data.data.id + "' data-toggle='modal' data-target='#removeModal'>Supprimer</button></td>";
        }
    dataString = dataString + "</tr>";

    return dataString;
}

var addLineInNextAgenda = function(data, last) {
    var classAdd = '';
    if(last) {
        classAdd = 'last-activity';
    }
    var dataString = "<tr class='day_" + data.data.dayInt +  " " + classAdd + " ' data-starttime='" + data.data.startTime + "' data-endtime='" + data.data.endTime + "' >" +
        "<td class='agenda-time' style='background-color:" + data.data.color + "'>" +
        formatTime(data.data.startTime) + '-' + formatTime(data.data.endTime) +
        "</td>" +
        "<td class='agenda-events' style='background-color:" + data.data.color + "'>" +
        "<div class='agenda-event'>" +
        data.data.activity +
        "</div></td>";
    if($("#admin").length) {
        dataString = dataString + "<td class='action-delete'><button type='button' class='btn btn-remove' data-id='" + data.data.id + "' data-toggle='modal' data-target='#removeModal'>Supprimer</button></td>";
    }

    dataString = dataString + "</tr>";

    return dataString;
}

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

var resetTrainingForm = function () {
    $("#add_training_activity").removeClass("has-error");
    $("#add_training_color").removeClass("has-error");
}

var resetValueTrainingForm = function () {
    $("#add_training_activity").val("");
    $("#add_training_color").val("");
}

$( document ).ready(function() {

    if($("#add_visit_date").length) {
        $("#add_visit_date").datepicker({
            format: "dd-mm-yyyy",
            startDate: new Date()
        });
    }

    if($("#add_patient_birthday").length) {
        $("#add_patient_birthday").datepicker({
            format: "dd-mm-yyyy",
            endDate: new Date()
        });
    }

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
                        var dataSet = [data.data.arm, data.data.chest, data.data.size, data.data.hip, data.data.thigh];
                        $(".msg_error").html("").hide();
                        if($(".msg_nodata").length == 0) {
                            weightChart.data.labels[nb_visit] = data.data.date;
                            weightChart.data.datasets[0].data[nb_visit] = data.data.weight;
                            weightChart.update();
                            massGChart.data.labels[nb_visit] = data.data.date;
                            massGChart.data.datasets[0].data[nb_visit] = data.data.massG;
                            massGChart.update();
                            dateChart.data.datasets[1] = createDataSet("Dernière mesure", window.chartColors.green, dataSet);
                            dateChart.update();
                        } else {
                            $(".msg_nodata").remove();
                            createChart([data.data.date], [data.data.weight], [data.data.massG], dataSet, null);
                        }
                        nb_visit++;
                        $("#visits").parent(".table-responsive").removeClass("hide");
                        $("#visits").children("tbody").append(addLineIntable(data));
                        resetValueVisitForm();
                        $('.weightUser').html(data.data.weight);
                        $('.massGUser').html(data.data.massG);
                        $("#color-fat").removeClass($("#color-fat").attr("class")).addClass("numberCircle " + data.data.colorFatMass);

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

    if($("#addtraining").length) {
        $(document).on("submit", "#addtraining", function(e) {
            e.preventDefault();
            var errors = 0;
            resetTrainingForm();
            if (!$("#add_training_activity").val()) {
                $("#add_training_activity").addClass("has-error");
                errors++;
            }
            if (!$("#add_training_color").val()) {
                $("#add_training_color").addClass("has-error");
                errors++;
            }

            if (errors === 0) {
                var formData = new FormData($(this)[0]);
                $("#add_training_save").addClass("hidden");
                var spinner = new Spinner().spin();
                $("#spinner-modal").append(spinner.el);
                $(".overlaymodal").show();

                $.ajax({
                    url: "/add-training",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false
                }).done(function (data) {
                    $(".overlaymodal").hide();
                    $("#spinner-modal").html("");
                    $("#add_training_save").removeClass("hidden");
                    if (data.error_code === 0) {
                        resetValueTrainingForm();
                        var objectBefore = null;
                        // No data
                        if($("#trainings").parent(".table-responsive").hasClass("hide")) {
                            $(".msg_nodata").remove();
                            $("#trainings").parent(".table-responsive").removeClass("hide");
                            $("#trainings").children("tbody").append(addLineInNewAgenda(data, 1));
                        } else if($("#day_" + data.data.dayInt).length === 0) { // No data in this day
                            if( data.data.dayInt == 6) { // Monday or sunday
                                $("#trainings").children("tbody").append(addLineInNewAgenda(data, 1));
                            }else if(data.data.dayInt == 0) {
                                $("#trainings").children("tbody").prepend(addLineInNewAgenda(data, 1));
                            } else {
                                    var i = data.data.dayInt - 1;
                                while(objectBefore == null) {
                                    if($("#day_" + i).length) {
                                        objectBefore = "day_" + i;
                                    }
                                    i--;
                                }
                                $(addLineInNewAgenda(data, 1)).insertAfter( "." + objectBefore + '.last-activity' );
                            }

                        } else if ($("#day_" + data.data.dayInt).length > 0) {
                            var iSelect = 0;
                            $(".day_" + data.data.dayInt).each(function (i, elt) {
                                if (parseInt(data.data.startTime) >= parseInt($(elt).data('endtime'))) {
                                    objectBefore = elt;
                                    iSelect = i;
                                }
                            });
                            if (($(".day_" + data.data.dayInt).length - 1) == iSelect && objectBefore != null) {
                                $(".day_" + data.data.dayInt + '.last-activity').removeClass('last-activity');
                                $(addLineInNextAgenda(data, true)).insertAfter(objectBefore);
                            } else if (objectBefore == null) {
                                var beforeElement = $("#day_" + data.data.dayInt);
                                $(addLineInNewAgenda(data, $(".day_" + data.data.dayInt).length)).insertBefore("#day_" + data.data.dayInt);
                                beforeElement.children('.agenda-date').remove();
                                beforeElement.attr("id", "");
                            } else {
                                $(addLineInNextAgenda(data, false)).insertAfter( objectBefore );
                            }

                            $("#day_" + data.data.dayInt).children(".agenda-date").attr("rowspan", $(".day_" + data.data.dayInt).length);
                            $("#day_" + data.data.dayInt).children(".agenda-date").children('.activity').html($(".day_" + data.data.dayInt).length + " activités");
                        }
                        $('#addtraining .close').click();
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


    $(document).on("click", ".btn-remove", function() {
       $(".valid-delete").data("idactivity", $(this).data("id"));
    });

    $(document).on("click", ".valid-delete", function() {
        var trainingId = $(this).data("idactivity"),
            userContentId = $(this).data("userid");
        $.ajax({
            url: "/remove-training",
            method: "POST",
            data: {userId: userContentId, activityId: trainingId}
        }).done(function (data) {
            location.reload();
        });
    });

});