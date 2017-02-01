var weightChart = null,
    massGChart = null,
    dateChart = null,
    date = [],
    weight = [],
    massG = [],
    measureFirst = null,
    measureSecond = null,
    nb_visit = 0;

window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(185, 206, 2)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(231,233,237)'
};

var createDataSet = function(label, colorGraphic, data) {
    return {
        label: label,
        borderColor: colorGraphic,

        pointBackgroundColor: colorGraphic,
        data: data
    };
}


var createChart = function(date, weight, massG, firstDataSet, secondDataSet) {
    // Chart
    var config = {
            type: 'line',
            data: {
                labels: date,
                datasets: [{
                    label: "Poids",
                    backgroundColor: window.chartColors.green,
                    borderColor: window.chartColors.green,
                    data: weight,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Mois'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Kg'
                        }
                    }]
                }
            }
        },
        config_imc = {
            type: 'line',
            data: {
                labels: date,
                datasets: [{
                    label: "Masse Graisseuse",
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    data: massG,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Mois'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                }
            }
        },
        color = Chart.helpers.color,
        config_radar = {
            type: 'radar',
            data: {
                labels: ["Bras", "Poitrine", "Taille", "Hanches", "Cuisses"],
                datasets: []
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                },
                elements: {
                    line: {
                        tension: 0.0,
                    }
                },
                scale: {
                    beginAtZero: true,
                }
            }
        };
    if(firstDataSet != null) {
        config_radar.data.datasets[0] = createDataSet("Première mesure", window.chartColors.blue, firstDataSet);
    }

    if(secondDataSet != null) {
        config_radar.data.datasets[1] = createDataSet("Dernière mesure", window.chartColors.green, secondDataSet);
    }

    weightChart = new Chart(document.getElementById("weightChart").getContext("2d"), config);

    massGChart = new Chart(document.getElementById("imcChart").getContext("2d"), config_imc);

    dateChart = new Chart(document.getElementById("bodyChart"), config_radar);
}

$( document ).ready(function() {
    'use strict'; // Start of use strict
    if($(".msg_nodata").length == 0) {
        var visitsArray = $.parseJSON(visits);
        $.each(visitsArray, function(i, item) {
            date.push(item.date);
            weight.push(item.weight);
            massG.push(item.fatMass);
        });
        measureFirst = [visitsArray[0].arm, visitsArray[0].chest, visitsArray[0].size, visitsArray[0].hip, visitsArray[0].thigh];

        if(visitsArray.length > 1) {
            var index = visitsArray.length - 1;
            measureSecond = [visitsArray[index].arm, visitsArray[index].chest, visitsArray[index].size, visitsArray[index].hip, visitsArray[index].thigh];
        }

        nb_visit = visitsArray.length;

        createChart(date, weight, massG, measureFirst, measureSecond);




    }

});