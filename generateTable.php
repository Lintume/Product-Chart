<?php
global $myWpdb;
global $wpdb;
$myWpdb = $wpdb;

if ((get_option('use local db') == 'n'))
{
    $myWpdb = new wpdb( get_option('remote user'), get_option('remote pwd'), get_option('remote database'), get_option('host') );
}

$groups = $myWpdb->get_results( "SELECT * FROM  productgroups" );//get all groups
foreach ($groups as $row)//create indexed array on pg_id
{
    $productGroupsIndexed[$row->pg_id] = (array) $row;
}
ksort($productGroupsIndexed);

foreach ($productGroupsIndexed as $index => $row)//add field 'root_parent_id' to indexed array
{
    if($row['pg_supergroupid'] == $row['pg_id'])
        $productGroupsIndexed[$index]['root_parent_id']  = $row['pg_id'];
    else //search root group parent
    {
        $parent = $productGroupsIndexed[$row['pg_supergroupid']];
        $productGroupsIndexed[$index]['root_parent_id'] = $parent['root_parent_id'];
    }
}
//join data all tables and sort
$count_prod = $myWpdb->get_results( "
            select DATE(orders.o_creationdate) AS dateShort, 
                   COUNT(orders.o_productid) AS orders_count,
                   productgroups.pg_id
            from   orders
                   join products 
                   on orders.o_productid=products.p_id
                   join productgroups 
                   on products.p_productgroupid=productgroups.pg_id
            GROUP  BY dateShort, productgroups.pg_id
            ORDER  BY dateShort ASC, productgroups.pg_id ASC ");

foreach ($count_prod as &$group)//add to objects $count_prod field root_parent_id
{
    $groupObj = $productGroupsIndexed[$group->pg_id];
    $group->root_parent_id = $groupObj['root_parent_id'];
}
foreach ($productGroupsIndexed as $row) //create indexed array groups with root_parent_id
{
    $rootGroups[] = $row['root_parent_id'];
}
$rootGroups = array_unique($rootGroups); //make array unique
$k = 0;
foreach ($rootGroups as $rootGroup)//array: root group - index
{
    $rootGroupsIndex[$rootGroup] = $k;
    $k++;
}

$dates = $myWpdb->get_results( "SELECT DISTINCT DATE(o_creationdate) AS o_creationdate FROM orders" );//get all unique dates
for ($i = 0; $i<count($dates); $i++)//create array with only dates
    $dates_num[$i] = $dates[$i]->o_creationdate;

sort($dates_num);
$datesRootGroupRow = array_fill (0, count($rootGroups), 0);

for ($i = 0; $i<count($dates_num); $i++)//fill array 0
{
    $datesRootGroup[$dates_num[$i]] = $datesRootGroupRow;
}

foreach ($count_prod as $group)//fill array, use data $count_prod like coordinates
{
    $index = $rootGroupsIndex[$group->root_parent_id];
    $count = $datesRootGroup[$group->dateShort][$index] + $group->orders_count;
    $datesRootGroup[$group->dateShort][$index] = $count;
}
//create correct array for plugin
$arrayChart[0][0] = 'DATE';
$j = 1;
foreach ($rootGroups as $rootGroup)//add names of groups
{
    $arrayChart[0][$j] = "Gr-".$rootGroup;
    $j++;
}

for ($i = 0; $i<count($dates_num); $i++)
{
    $arrayChartRow = $datesRootGroup[$dates_num[$i]];
    array_unshift($arrayChartRow, $dates_num[$i]);
    $arrayChart[$i+1] = $arrayChartRow;
}

$jsonArrayChart = json_encode($arrayChart);
?>