/**
 * @file
 * Bars chart views style drawing.
 */

(function ($) {
  Drupal.behaviors.ChartViewsStyleVerticalBars = {
    attach: function (context, settings) {
      var charts = $("canvas[data-chart-type='chartjs_vertical_bars']");
      $.each(charts, function (i, chart) {
        var chartContainerId = $(chart).attr('id');
        var dataPoints = [];
        var dataColors = [];
        var dataLabels = [];
        var dataTable = $('#' + chartContainerId + '_data');
        dataTable.find('tr').each(function (i, row) {
          var cols = [];
          $(row).find('td').each(function (j, column) {
            cols.push($(column).html());
          });
          if (cols.length > 0) {
            var label = '';
            try {
              label = $(cols[1]).text();
              label = (label != '') ? label : cols[1].trim();
            }
            catch (e) {
              label = cols[1];
            }
            dataLabels.push(label.trim());
            dataPoints.push(parseFloat(cols[2]));
            dataColors.push(cols[3]);
          }
        });

        var config = {
          type: 'bar',
          data: {
            labels: dataLabels,
            datasets: [{
              data: dataPoints,
              backgroundColor: dataColors
            }]
          },
          options: {
            responsive: true,
            legend: {
              position: 'bottom',
              display: false
            },
            title: {
              display: false
            },
            animation: {
              animateScale: true,
              animateRotate: true
            }
          }
        };

        var ctx = document.getElementById(chartContainerId);
        if (ctx) {
          ctx = ctx.getContext('2d');
          window.myDoughnut = new Chart(ctx, config);
        }
      });
    }
  };
})(jQuery);
