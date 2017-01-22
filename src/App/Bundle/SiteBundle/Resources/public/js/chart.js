$( document ).ready(function() {
    'use strict'; // Start of use strict
    if($("#weightChart").length || $("#imcChart").length || $("#bodyChart").length) {
        // Chart
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(185, 206, 2)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(231,233,237)'
        };

        window.randomScalingFactor = function () {
            return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
        }

        var config = {
                type: 'line',
                data: {
                    labels: ["10/01/2017", "17/01/2017", "20/01/2017", "22/01/2017"],
                    datasets: [{
                        label: "Poids",
                        backgroundColor: window.chartColors.green,
                        borderColor: window.chartColors.green,
                        data: [
                            70,
                            75,
                            72,
                            71
                        ],
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
                    labels: ["10/01/2017", "17/01/2017", "20/01/2017", "22/01/2017"],
                    datasets: [{
                        label: "IMC",
                        backgroundColor: window.chartColors.blue,
                        borderColor: window.chartColors.blue,
                        data: [
                            22,
                            23.1,
                            23,
                            22.8
                        ],
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
                    datasets: [{
                        label: "Dernière mesure",
                        borderColor: window.chartColors.green,
                        backgroundColor: color(window.chartColors.green).alpha(0.2).rgbString(),
                        pointBackgroundColor: window.chartColors.green,
                        data: [
                            30,
                            94,
                            90,
                            99,
                            45
                        ]
                    }, {
                        label: "Première mesure",
                        borderColor: window.chartColors.blue,
                        backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
                        pointBackgroundColor: window.chartColors.blue,
                        data: [
                            33,
                            97,
                            86,
                            90,
                            50
                        ]
                    }]
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
        new Chart(document.getElementById("weightChart").getContext("2d"), config);

        new Chart(document.getElementById("imcChart").getContext("2d"), config_imc);

        new Chart(document.getElementById("bodyChart"), config_radar);
    }

});