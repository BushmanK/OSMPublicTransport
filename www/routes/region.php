<?php
include_once ('../include/config.php');

$r_id = $_GET['id'];

$sql_region = pg_query("
SELECT
	id,
	name
FROM regions
WHERE
	id='".$r_id."'
	");

$sql_city_town = pg_query("
SELECT DISTINCT
	places.id,
	places.type,
	places.name
FROM places, transport_location, transport_routes
WHERE
	places.region_id='".$r_id."' and
	places.type in ('city','town') and
	places.id in (transport_location.place_id) and
	transport_location.route_id=transport_routes.id and
	transport_routes.tags->'route' in (".PUBLIC_TRANSPORT.") and
	transport_routes.tags->'ref'<>''
--GROUP BY places.id
ORDER BY type, name
	");

$sql_village = pg_query("
SELECT DISTINCT
	places.id,
	places.type,
	places.name
FROM places, transport_location, transport_routes
WHERE
	places.region_id='".$r_id."' and
	places.type='village' and
	places.id in (transport_location.place_id) and
	transport_location.route_id=transport_routes.id and
	transport_routes.tags->'route' in (".PUBLIC_TRANSPORT.") and
	transport_routes.tags->'ref'<>''
--GROUP BY places.id
ORDER BY type, name
	");

$region_name = pg_fetch_assoc($sql_region)['name'];
$output = "<div class='content_body'><h2 align=center>".$region_name."</h2>";

$city_town_count=pg_num_rows($sql_city_town);
$village_count=pg_num_rows($sql_village);

if ($city_town_count+$village_count>0) {
	if ($city_town_count>0) {
		$i=0;
		$output .=
			"<h3 align=left>Города:</h3>".
			"<p align=justify>";
		while ($row = pg_fetch_assoc($sql_city_town)){
			$i++;
			$output .= "<a href='routes_list?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
			if ($i<$city_town_count)
			{
				$output .= ", ";
			}
		}
		$output .= "</p>";
	}
	if ($village_count>0) {
		$i=0;
		$output .=
			"<h3 align=left>Посёлки городского типа:</h3>".
			"<p align=justify>";
		while ($row = pg_fetch_assoc($sql_village)){
			$i++;
			$output=$output. "<a href='routes_list?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
			if ($i<$village_count)
			{
				$output .= ", ";
			}
		}
		$output .= "</p>";
	}

} else
{
	$output .= "<p>К сожалению здесь пока ничего нет :(</p>";
}

$output .= "</div";

pg_close($dbconn);

$page_title=$region_name.' - маршруты общественного транспорта OpenStreetMap';
$page = 'routes';
include(TEMPLATE_PATH);
?>
