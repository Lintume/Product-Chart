<html>
<head>
    <meta charset="utf-8">
    <title>View Charts</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
    </script>
</head>
<body>
    <div id="container" style="width: 1200px; height: 800px; margin: 0 auto"></div>
    <?php
        require_once 'generateTable.php';
    ?>

    <script language="JavaScript">
        $(document).ready(function(){
            var jsonArrayChart = '<?php echo $jsonArrayChart;?>';
            var ArrayChart = JSON.parse(jsonArrayChart);

            function drawChart() {
                // Define the chart to be drawn.
                var data = google.visualization.arrayToDataTable(ArrayChart);

                var options = {
                    title: 'Product Chart',
                    isStacked:true
                };

                // Instantiate and draw the chart.
                var chart = new google.visualization.BarChart(document.getElementById('container'));
                chart.draw(data, options);
            }
            google.charts.setOnLoadCallback(drawChart);
        });
    </script>
</body>
</html>