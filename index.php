<?php
require_once('./config.php');
require_once('./mrtgparser.class.php');

$dev_menu_list = "";
$dev_int_list = "";
$int_data_list = "";
$all_graphs = "";
$req_dev = null;

$mrtg = new MRTGParser();
$mrtg->mrtg_path = $mrtg_path;

$dev_list = $mrtg->get_device_list();
if(isset($_REQUEST['d']) && $_REQUEST['d'] != "")
{
	$req_dev = $_REQUEST['d'];
	if(array_key_exists($req_dev, $dev_list))
	{
		$device_name = $_REQUEST['d'];
		$dev_info = $mrtg->get_device_info(true, $req_dev);
	}
	else
	{
		exit("Device not found");
	}
}
else
{
	$device_name = array_keys($dev_list);
	$device_name = $device_name[0];
	$dev_info = $mrtg->get_device_info(false, $dev_list[$device_name]['path']);
}

foreach($dev_list as $dev)
{
	// 18
	if(strlen("$dev[name] ($dev[ip])") > 21)
	{
		$dev_label = $dev['name'];
	}
	else
	{
		$dev_label = "$dev[name] ($dev[ip])";
	}
	
	if((strcasecmp($req_dev, $dev['name']) === 0 && $req_dev !== null) || strcasecmp($device_name, $dev['name']) === 0)
	{
		$dev_menu_list .= "<li class=\"active\" name=\"$dev[name]\"><a href=\"?d=$dev[name]\" title=\"$dev[name] ($dev[ip])\">$dev_label</a></li>\n";
	}
	else
	{
		$dev_menu_list .= "<li name=\"$dev[name]\"><a href=\"?d=$dev[name]\" title=\"$dev[name] ($dev[ip])\">$dev_label</a></li>\n";
	}
}

foreach($dev_info as $dev)
{
	$device_name = $dev['name'] . " - " .$dev['ip'];
	foreach($dev as $int)
	{
		if(is_array($int))
		{
			$graph_list = "<h3 class=\"interface_name\"><a href=\"".$int['url']."\">".$int['intName']."</a></h3>\n";
			$graph_list .= "<div class=\"day_graph\">\n";
			$graph_list .= "Day Graph:\n";
			$graph_list .= "<img src=\"".$int['graphs']['day']['path']."\" />\n";
			if($int['graphs']['day']['info']['updated'])
			{
				$graph_list .= "<small>Updated: ".$int['graphs']['day']['info']['datetime']."</small>\n";
			}
			else
			{
				$graph_list .= "<small class=\"update_warning\">Updated: ".$int['graphs']['day']['info']['datetime']."</small>\n";
			}
			$graph_list .= "<hr />\n";
			$graph_list .= "</div>\n";
			
			$graph_list .= "<div class=\"week_graph\">\n";
			$graph_list .= "Week Graph:\n";
			$graph_list .= "<img src=\"".$int['graphs']['week']['path']."\" />\n";
			if($int['graphs']['day']['info']['updated'])
			{
				$graph_list .= "<small>Updated:".$int['graphs']['week']['info']['datetime']."</small>\n";
			}
			else
			{
				$graph_list .= "<small class=\"update_warning\">Updated:".$int['graphs']['week']['info']['datetime']."</small>\n";
			}
			$graph_list .= "<hr />\n";
			$graph_list .= "</div>\n";
			
			$graph_list .= "<div class=\"month_graph\">\n";
			$graph_list .= "Month Graph:\n";
			$graph_list .= "<img src=\"".$int['graphs']['month']['path']."\" />\n";
			if($int['graphs']['day']['info']['updated'])
			{
				$graph_list .= "<small>Updated:".$int['graphs']['month']['info']['datetime']."</small>\n";
			}
			else
			{
				$graph_list .= "<small class=\"update_warning\">Updated:".$int['graphs']['month']['info']['datetime']."</small>\n";
			}
			$graph_list .= "<hr />\n";
			$graph_list .= "</div>\n";
			
			$graph_list .= "<div class=\"year_graph\">\n";
			$graph_list .= "Year Graph:\n";
			$graph_list .= "<img src=\"".$int['graphs']['year']['path']."\" />\n";
			if($int['graphs']['day']['info']['updated'])
			{
				$graph_list .= "<small>Updated:".$int['graphs']['year']['info']['datetime']."</small>\n";
			}
			else
			{
				$graph_list .= "<small class=\"update_warning\">Updated:".$int['graphs']['year']['info']['datetime']."</small>\n";
			}
			$graph_list .= "</div>\n";
			$all_graphs .= $graph_list;
			
			$link_name = preg_replace("/\//", "", $int['intName']);
			$dev_int_list .= "<li><a href=\"#$link_name\" data-toggle=\"tab\">$int[intName]</a></li>\n";
			$int_data_list .= "<div class=\"tab-pane\" id=\"$link_name\">\n";
			$int_data_list .= $graph_list;
			$int_data_list .= "</div>\n";
		}
	}
}

$page_title = $device_name;
require_once('_header.php');
?>

	<div class="span3">
		<div class="nav-sidebar well well-small">
			<ul class="nav nav-list">
				<li class="nav-header">View Options</li>
				<li class="active"><a href="#" id="view_all">View All</a></li>
				<li><a href="#" id="daily_view">Daily View</a></li>
				<li><a href="#" id="weekly_view">Weekly View</a></li>
				<li><a href="#" id="monthly_view">Monthly View</a></li>
				<li><a href="#" id="yearly_view">Yearly View</a></li>
			</ul>
			<ul class="nav nav-list">
				<li class="nav-header">Device List</li>
<?php
	echo $dev_menu_list;
?>
			</ul>
		</div>
	</div>
	<div class="span9">
		<p><?php echo $device_name; ?></p>
		<div class="tabbable tabs-left well well-small">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#AllInterfaces" data-toggle="tab">All</a></li>
<?php
	echo $dev_int_list;
?>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="AllInterfaces">
<?php
	echo $all_graphs;
?>
				</div>
<?php
	echo $int_data_list;
?>
			</div>
		</div>
	</div>
	
<?php require_once('_footer.php'); ?>