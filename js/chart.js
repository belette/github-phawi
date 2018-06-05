raspURL = "http://192.168.1.70/";
recieved2 = "";
dataGraphMe = "";
 

function ActionPerso(type, max, column) {
    var dats = {type: type, max: max, column: column};
    var myData = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: dats
    }).done(function (dataGraph) {
        //console.log(dataGraph);
        //return (JSON.parse(dataGraph));
    }).always(function (alwaysData) {
        // console.log("always")
        //return(JSON.parse(alwaysData));
    }).fail(function (fail) {
        //    console.log("failll ; ");
        //return(JSON.parse(fail));
    });
    return(myData);
}

function init_val(){
     var max = $("#range").val();
    console.log(max);
    
     X = ($.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'theDate'}
    }));
    
    inTemp = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'inTemp'}
    });
    outTemp = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'outTemp'}
    });
    
    thermostat = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'thermostatTemp'}
    });
    
     humidity = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'humidity'}
    });
    
        pression = $.ajax({
        type: 'POST',
        url: './get_json.php',
        data: {type: 'getGraphData', max: max, column: 'pression'}
    });
    
    
}


$(document).ready(function(){
   init_val();
    
})
   

$("#bout").click(function() {

var inTempData = JSON.parse(inTemp.responseText.replace(/\\'/g, "'")).sort();
var ouTempData = JSON.parse(outTemp.responseText.replace(/\\'/g, "'")).sort();
var humidityData  = JSON.parse(humidity.responseText.replace(/\\'/g, "'")).sort();
var thermostatData  = JSON.parse(thermostat.responseText.replace(/\\'/g, "'")).sort();
var pressionData  = JSON.parse(pression.responseText.replace(/\\'/g, "'")).sort();

var Xdata = JSON.parse(X.responseText.replace(/\\'/g, "'")).sort();


    var ctx = document.getElementById('myChart').getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
                labels: Xdata,
                datasets: [{
                    label: "Temperature exterieure",
                    backgroundColor: 'grey',
                    borderColor: 'grey',
                    data: ouTempData,
                    fill: false,
                    borderWidth: 1,
                    pointRadius:1,
                }, 
                {
                    label: "Temperature Interieure",
                    fill: false,
                    backgroundColor: 'red',
                    borderColor: 'red',
                    data: inTempData,
                    borderWidth: 1,
                    pointRadius:1,
                },
                {
                    label: "Humidite",
                    fill: false,
                    backgroundColor: 'blue',
                    borderColor: 'blue',
                    data: humidityData,
                    borderWidth: 1,
                    pointRadius:1,
                },
                {
                    label: "thermostat",
                    fill: false,
                    backgroundColor: 'green',
                    borderColor: 'green',
                    data: thermostatData,
                    borderWidth: 1, 
                    pointRadius:1,
                }
            ] 
        },
        
        options: {
                pointRadius:0,
                responsive: true,
                title:{
                    display:true,
                    text:'Graphique quotidient'
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
                            labelString: 'Heure'
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
    });

});




// []

// 
//        var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
//        var config = {
//            type: 'line',
//            data: {
//                labels: ["January", "February", "March", "April", "May", "June", "July"],
//                datasets: [{
//                    label: "My First dataset",
//                    backgroundColor: window.chartColors.red,
//                    borderColor: window.chartColors.red,
//                    data: [
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor()
//                    ],
//                    fill: false,
//                }, {
//                    label: "My Second dataset",
//                    fill: false,
//                    backgroundColor: window.chartColors.blue,
//                    borderColor: window.chartColors.blue,
//                    data: [
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor(),
//                        randomScalingFactor()
//                    ],
//                }]
//            },
//            
//        };
//
//        window.onload = function() {
//            var ctx = document.getElementById("canvas").getContext("2d");
//            window.myLine = new Chart(ctx, config);
//        };
//
//        document.getElementById('randomizeData').addEventListener('click', function() {
//            config.data.datasets.forEach(function(dataset) {
//                dataset.data = dataset.data.map(function() {
//                    return randomScalingFactor();
//                });
//
//            });
//
//            window.myLine.update();
//        });
//
//        var colorNames = Object.keys(window.chartColors);
//        document.getElementById('addDataset').addEventListener('click', function() {
//            var colorName = colorNames[config.data.datasets.length % colorNames.length];
//            var newColor = window.chartColors[colorName];
//            var newDataset = {
//                label: 'Dataset ' + config.data.datasets.length,
//                backgroundColor: newColor,
//                borderColor: newColor,
//                data: [],
//                fill: false
//            };
//
//            for (var index = 0; index < config.data.labels.length; ++index) {
//                newDataset.data.push(randomScalingFactor());
//            }
//
//            config.data.datasets.push(newDataset);
//            window.myLine.update();
//        });
//
//        document.getElementById('addData').addEventListener('click', function() {
//            if (config.data.datasets.length > 0) {
//                var month = MONTHS[config.data.labels.length % MONTHS.length];
//                config.data.labels.push(month);
//
//                config.data.datasets.forEach(function(dataset) {
//                    dataset.data.push(randomScalingFactor());
//                });
//
//                window.myLine.update();
//            }
//        });
//
//        document.getElementById('removeDataset').addEventListener('click', function() {
//            config.data.datasets.splice(0, 1);
//            window.myLine.update();
//        });
//
//        document.getElementById('removeData').addEventListener('click', function() {
//            config.data.labels.splice(-1, 1); // remove the label first
//
//            config.data.datasets.forEach(function(dataset, datasetIndex) {
//                dataset.data.pop();
//            });
//
//            window.myLine.update();
//        });
//    