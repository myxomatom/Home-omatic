/*
 *Script de déclaration de l'objet ChartManager
 *Permet d'effectuer la requete ajax qui recupère les données dans la BDD
 *Puis mise en forme des données dans un graphique gèré par l'outil de création de graphique Chart_js (http://www.chartjs.org/)
 ******************************************************************************************************************************
 *dataType correspond au données récupèrées dans la BDD
 *debut correspond à la date et à l'heure de début au format AAAAMMJJ[HHMM]
 *chartID correspond au graphique dans lequel on veut entrer les données
 *titre correspond au titre du graphique
 ******************************************************************************************************************************
 */

function ChartManager (dataType, debut, fin, chartID, titre, sensor_id, type, yaxis){
    this.yaxis = (yaxis === "true");
    dataType = dataType.split(" ");
    this.chartColors =["rgba(61, 100, 179, 1)","rgba(229, 11, 11, 1)","rgba(36, 204, 36, 1)","rgba(0, 0, 0, 1)"]

    if (dataType.length == 1)
    {
        this.requete = "Ajax/get_data.php?debut=" + debut + "&fin=" + fin + "&donnee=" + dataType[0] + "&capteur=" + sensor_id + "&type=" + type;    
    }
    else
    {
        this.requete =[];
        for (i=0;i<dataType.length;i++) {
            this.requete[i] = "Ajax/get_data.php?debut=" + debut + "&fin=" + fin + "&donnee=" + dataType[i] + "&capteur=" + sensor_id + "&type=" + type;
        }

    }
    this.ctx = document.getElementById(chartID);
    this.titre = titre;
    this.option = {
                    scales: {
                        xAxes: [{
                            type: 'time'
                        }],
                        yAxes: [{
                            id : 'A',
                            position : 'left',
                            display: true,
                            ticks: {
                                suggestedMin: 0,//  minimum will be 0, unless there is a lower value.
                                suggestedMax: 1                             
                                    }
                            },
                            {
                            id : 'B',
                            position : 'right',
                            display: this.yaxis,
                            ticks: {
                                suggestedMin: 0,//  minimum will be 0, unless there is a lower value.
                                suggestedMax: 1 
                                 
                                }
                            }]
                    }
                };
    
    
    ChartManager.prototype.generate = function()
    {
               
        var num = chartID.toString();
        var ctx = this.ctx;
        var option = this.option
        var chartColors = this.chartColors;
        var requete = this.requete;
        var data = {datasets: []};
        var datasets = {datasets: []};

        if (dataType.length == 1){
            $.getJSON(this.requete , function(data){}).done(function(result){
            data.datasets.push({
                        label: dataType,
                        yAxisID : 'A',
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: result
                    });
            var config = {
                type: 'line',
                xValueType: "dateTime"           
            };
            config['data'] = data;
            config['options'] = option;
            window['refresh_'+num] = new Chart(ctx, config);
        });

        }
        else{
            var get_dataset = function(request,color,titre){
               return $.getJSON(request , function(data){}).done(function(result){
                set_datasets(result,color,titre);})
            }

            var get_graph_conf = function(ID_capteur){              
                return  $.getJSON("Ajax/get_graph_conf.php?capteur=" + ID_capteur + "&type=wattmeter1ch" , function(data){}).done(function(result){
                    chartColors = result;
                });
            }

            var set_datasets = function(result, color, titre){
                yaxisID = 'A'
                if (yaxis==='true' && color==chartColors[1]) yaxisID ='B';
                datasets.datasets.push({
                    label: titre,
                    yAxisID : yaxisID,
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: color,
                    borderColor: color,
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: color,
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: color,
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: result
                });
            };


            

            requetes =[];
            $.when(get_graph_conf(sensor_id)).then(function(){

            for (i=0; i<requete.length; i++)
            {
                requetes.push(get_dataset(requete[i],chartColors[i],dataType[i]));
            }
            
            $.when.apply(null,requetes).then(function(){     
                var config = {
                    type: 'line',
                    xValueType: "dateTime"           
                };
                config['data'] = datasets;
                config['options'] = option;
                window['refresh_'+num] = new Chart(ctx, config);
            });
        });
        }
    }   
}