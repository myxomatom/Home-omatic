Date.prototype.toAppFormat = function() {
      var day = this.getDate();
      if (day < 10)
            {day = "0" + day;}
      var month = (this.getMonth() + 1);
      if (month < 10)
            {month = "0" + month;}
      var year = this.getFullYear();
      var date = day +"/"+ month +"/"+ year;
      return date;
};

Date.prototype.toSQL = function() {
      var day = this.getDate();
      var month = (this.getMonth() + 1);
      var year = this.getFullYear();
      var date = day + month*100 + year*10000;
      return date;
};

$( document ).ready(function() {
      var now = Date.now();
      var tomorrow = new Date(now + 86400000);
      var yesterday = new Date(now - 86400000);

      $("[id^='refresh_']").each(function( index ) {
            var sensor_type = $(this).attr("type_capteur");
            var sensor_id = $(this).attr("sensor");
            var nom_chart = "myChart" + index;
            var chart = $('#'+nom_chart);
            var measure = $(this).attr("measure") ;
            var titre = $(this).attr("title");
            var yaxis = $(this).attr("dualYaxis");
            $('#debut_' + nom_chart).val(yesterday.toAppFormat());
            $('#fin_' + nom_chart).val(tomorrow.toAppFormat());
            myChart = new ChartManager(measure,yesterday.toSQL(),tomorrow.toSQL(),nom_chart,titre,sensor_id,sensor_type,yaxis);
            myChart.generate();
      });

      $('.date_pick input').datepicker({
            language: "fr"
      });
});


 $("[id^='refresh_']").click(function(event) 
            {
            var target = event.target.id;
            var sensor_type =  $(event.target).attr("type_capteur");
            var sensor_id = $(event.target).attr("sensor");
            var measure = $(event.target).attr("measure");
            var title = $(event.target).attr("title");
            var yaxis = $(this).attr("dualYaxis");
            var chart = target.split("_")[1];
            var debut = $('#debut_' + chart).val();
            var date = debut.split("/");
            var deb_sql = date[2] + date[1] + date[0];
            var fin = $('#fin_' + chart).val();
            date = fin.split("/");
            var fin_sql = date[2] + date[1] + date[0];
            var nom = event.target.id.toString()
            window[nom].destroy();
            window[nom] = new ChartManager(measure,deb_sql,fin_sql,chart,title,sensor_id,sensor_type,yaxis);
            window[nom].generate();
            });


