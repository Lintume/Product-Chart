<?php
/*
Plugin Name: Product Chart
Description: This plugin allows you to construct the graph sales data from the database.
You can view schedule of the admin panel, or else build a schedule directly on the site using the shortcode [product_chart].
You can change the connection settings, if you want to remotely connect to the database.
Version: 0.1
Author: Sakhno Lialia
*/

if(!defined('PRODUCT_CHART_URL'))
    define('PRODUCT_CHART_URL', plugin_dir_url( __FILE__ ));

if(!defined('PRODUCT_CHART_DIR'))
    define('PRODUCT_CHART_DIR', plugin_dir_path( __FILE__ ));

add_action( 'admin_menu', 'my_plugin_menu');//Add a hook to the menu in the console

function my_plugin_menu() //menu and submenu
{
    add_menu_page( 'My Plugin Options', 'Charts', 8, PRODUCT_CHART_DIR.'view_charts.php', '', 'dashicons-groups', 8);
    add_submenu_page( PRODUCT_CHART_DIR.'view_charts.php', 'View Charts', 'View Charts', 8, PRODUCT_CHART_DIR.'view_charts.php' );
    add_submenu_page( PRODUCT_CHART_DIR.'view_charts.php', 'Settings', 'Settings', 8, PRODUCT_CHART_DIR.'settings.php' );
}
//add libraries
function wptuts_scripts_basic()
{
    wp_register_script( 'custom-script2', PRODUCT_CHART_URL.'assests/jquery-1.12.1.js', __FILE__ );
    wp_enqueue_script( 'custom-script2' );

    wp_register_script( 'custom-script1', PRODUCT_CHART_URL.'assests/bootstrap.min.js', __FILE__ );
    wp_enqueue_script( 'custom-script1' );
}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_basic' );
add_action( 'admin_enqueue_scripts', 'wptuts_scripts_basic' );

function wptuts_style_basic()
{
    wp_register_style( 'custom-style', PRODUCT_CHART_URL.'css/bootstrap.css', __FILE__ );
    wp_enqueue_style( 'custom-style' );

    wp_register_style( 'custom-style1', PRODUCT_CHART_URL.'css/normalize.min.css', __FILE__ );
    wp_enqueue_style( 'custom-style1' );
}
add_action( 'wp_enqueue_scripts', 'wptuts_style_basic' );
add_action( 'admin_enqueue_scripts', 'wptuts_style_basic' );

add_action('wp_ajax_my_action_remote', 'my_action_remote_callback');

function my_action_remote_callback()//ajax handler if choose remote DB
{
    update_option('remote host', $_POST['host'], 'no');
    update_option('remote database', $_POST['database'], 'no');
    update_option('remote user', $_POST['user'], 'no');
    update_option('remote pwd', $_POST['pwd'],'no');
    update_option('use local db', 'n', 'no');

    wp_die('true'); 
}

add_action('wp_ajax_my_action_local', 'my_action_local_callback');

function my_action_local_callback()//ajax handler if choose local DB
{
    update_option('use local db', 'y', 'no');
    wp_die('true');
}

register_deactivation_hook( PRODUCT_CHART_DIR.'Product Chart.php', 'Charts_deactivate' );

function Charts_deactivate()//action for plugin deactivate, delete options
{
    delete_option('remote host');
    delete_option('remote database');
    delete_option('remote user');
    delete_option('remote pwd');
    delete_option('use local db');
}

add_shortcode ('product_chart', 'short_code_product_chart');

function short_code_product_chart()
{
    require_once 'generateTable.php';

    return"
    <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
    <script type=\"text/javascript\">
    google.charts.load('current', {packages: ['corechart']});
    </script>
    <div id=\"container\" style=\"width: 700px; height: 600px; margin: 0 auto\"></div>
    
    <script language=\"JavaScript\">
        $(document).ready(function(){
            var jsonArrayChart ='$jsonArrayChart';
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
    ";
}
