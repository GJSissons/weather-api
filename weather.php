
<!-- Query: https://sissons.ca/weather-api/weather.php?location=wasaga%20beach%2C%20ontario&unitGroup=metric -->

<!-- Read information from the path or query string-->
<?php

	$debug=false;

	include 'includes/db_funcs.php';
	include 'includes/funcs.php';
	
	//helper method to retrieve data from the URL path or query string
	function extractParam($pathSegments, $pathIndex, $query_params, $query_param) {
	   if (count($pathSegments)>$pathIndex) return trim(urldecode($pathSegments[$pathIndex]));
	   if ($query_params[$query_param]!=null) return trim(urldecode($query_params[$query_param]));
	   return null;
	}

	$segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
	$query_str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	parse_str($query_str, $query_params);
	//the location for the weather data (an address or partial address)
	//$location=extractParam($segments,1, $query_params, "location");
	//$location=rawurlencode($_GET['location']);

	// the unit group - us, metric or uk
	// $unitGroup=extractParam($segments,2, $query_params, "unitGroup");

	//we want weather data to aggregated to daily details.
	$aggregateHours=24;
	//your API key
	$apiKey="****";



?>
<html>
<head>
<title>Timeline Weather API PHP Sample</title>
<link rel="stylesheet" href="css/stylesheet.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<?php

		$monthnames = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

		if (isset($_GET) && ($_GET['location'] != '')) {
			$startdate=$_GET['startyear']."-".$_GET['month']."-".$_GET['day'];
			$startdate_tm=date_create($startdate);
			$enddate_tm=date_add($startdate_tm,date_interval_create_from_date_string(strval( (int)$_GET['numdays'] -1 ). " days"));
			$enddate = date_format($enddate_tm, 'Y-m-d');
			$numdays = (int) $_GET['numdays'];
			$location=$_GET['location'];
			$startyear=$_GET['startyear'];
			$month=$_GET['month'];
			$day=$_GET['day'];
			$unitGroup=$_GET['unitGroup'];
			$showapi=$_GET['showapi'];
			$showtable=$_GET['showtable'];
			$showicons=$_GET['showicons'];
		} else {
			$location="Wasaga Beach, Ontario";
			$startdate="2024-09-01";
			$enddate="2024-09-07";
			$numdays=7;
			$startyear="2015";
			$month="01";
			$day="01";
			$unitGroup="metric";
			$showapi="no";
			$showtable="no";
			$showicons="yes";
		}
	?>

	<div class="container">
	<form action="https://sissons.ca/weather-api/weather.php" id="weatherform" method="get">
		<div class="row">
			<div class="col-25">
				<label for="location">Location:</label>
			</div>
			<div class="col-75">
				<input type="text" id="location" name="location" value="<?php echo $location;?>">
			</div>
		</div>

		<div class="row">
			<div class="col-25">
				<label for="startyear">Starting:</label>
			</div>
			<div class="col-75">
				<select id="startyear" name="startyear" form="weatherform" style="width:30%;">
				<?php
				for ($i = 2015; $i <= 2030; $i++) {
					if ($i==(int)$startyear) {
						echo '<option selected="selected" value="'.(string)$i.'">'.(string)$i.'</option>';
					} else {
						echo '<option value="'.(string)$i.'">'.(string)$i.'</option>';
					}			
				}
				?>
				</select>
				
				<select id="month" name="month" form="weatherform" style="width:30%;">
					<?php
					for ($i = 1; $i <= 12; $i++) {
						if ($i==(int)$month) {
							echo '<option selected="selected" value="'.substr("000{$i}",-2).'">'.$monthnames[$i-1].'</option>';
						} else {
							echo '<option value="'.substr("000{$i}",-2).'">'.$monthnames[$i-1].'</option>';
						}			
					}			
					?>
				</select>

				<select id="day" name="day" form="weatherform" style="width:30%;">
				<?php
					for ($i = 1; $i <= 31; $i++) {
						if ($i==(int)$day) {
							echo '<option selected="selected" value="'.substr("000{$i}",-2).'">'.substr("000{$i}",-2).'</option>';
						} else {
							echo '<option value="'.substr("000{$i}",-2).'">'.substr("000{$i}",-2).'</option>';
						}
					} 
				?>
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-25">
				<label for="numdays">Number of days:</label>
			</div>
			<div class="col-75">
				<input type="text" id="numdays" name="numdays" value="<?php echo $numdays; ?>" size="2">
			</div>
		</div>

		<div class="row">
			<div class="col-25">
				<label for="UnitGroup">Units:</label>
			</div>
			<div class="col-25">
				<select id="unitGroup" name="unitGroup" form="weatherform">
					<option value="metric">Celsius</option>
					<?php
						$selected=''; 
						if ($unitGroup == 'us') $selected=' selected="selected"'; 
						echo '<option'.$selected.' value="us">Farenheit</option>';
					?>
				</select>
			</div>

			<div class="col-25">
				<label for="showapi">Show API:</label>
			</div>
			<div class="col-25">
				<select id="showapi" name="showapi" form="weatherform">
					<option value="no">No</option>
					<?php
						$selected=''; 
						if ($showapi == 'yes') $selected=' selected="selected"';
						echo '<option'.$selected.' value="yes">Yes</option>';
					?>
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-25">
				<label for="showtable">Show Table:</label>
			</div>
			<div class="col-25">
				<select id="showtable" name="showtable" form="weatherform">
					<option value="yes">Yes</option>
					<?php
						$selected=''; 
						if ($showtable == 'no') $selected=' selected="selected"';
						echo '<option'.$selected.' value="no">No</option>';
					?>
				</select>
			</div>

			<div class="col-25">
				<label for="showicons">Show Icons:</label>
			</div>
			<div class="col-25">
				<select id="showicons" name="showicons" form="weatherform">
					<option value="yes">Yes</option>
					<?php
						$selected=''; 
						if ($showicons == 'no') $selected=' selected="selected"';
						echo '<option'.$selected.' value="no">No</option>';
					?>
				</select>
			</div>
		</div>
		
		<div class="submit-row">
			<input type="submit">
		</div>

	</form>
	</div>
	<!-- Look up whether dates are in the cache ... -->

	<?php
		
		$start_time = (new DateTime())->format('Uu');

		$api_url="https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/{$location}/{$startdate}/{$enddate}?unitGroup={$unitGroup}&include=days%2Calerts&key={$apiKey}&contentType=json";

		$cached_items = check_cache($db_settings,$startdate, $numdays, $location, $unitGroup);

		if ($debug) echo '<br><p>Cached items: '.$cached_items.'</p>';

		echo '<div class="alert-box">';
		if ($cached_items == $numdays) {

			// Construct $days from the cache ....
			echo '<br><p>All dates were found in the database cache. As a result, this query was fast and free! ';
			// now, iterate through each date and retrieve and associative array with all the parameters ...
			$startdate_tm=date_create($startdate);
			$currentdate_tm  = $startdate_tm;
			$resolvedAddress=$location;
			for ($i=0;$i<$numdays; $i++) {
				$current_date = date_format($currentdate_tm, 'Y-m-d');
				if ($debug) echo '<br><p>Retrieving json data for the following date: '.$current_date.'</p>';
				$json = retrieve_cache_item($db_settings, $current_date, $location, $unitGroup);
				if ($debug) echo '<br><pre>Retrieved json is: '.$json.'</pre>';
				$day = json_decode($json);
				$days[$i] = $day;
				
				date_add($currentdate_tm,date_interval_create_from_date_string("1 days"));
			}
			if ($debug) echo '<br><pre>Value of days is:'.print_r($days,true).'</pre>';
		} else {
			// Use the API retrieve the $days array
			echo '<p>Results not found in cache, so using API. At $0.0001 / query, the estimated cost is $'.(0.0001*$numdays).'. ';
			$json_data = file_get_contents($api_url);

			$response_data = json_decode($json_data);
	
			$resolvedAddress=$response_data->resolvedAddress;
			$days=$response_data->days;

			// Write the response to cache, creating new records for each date that does not already exist

			foreach ($days as $day) {
				if ($debug) echo '<br><p>Calling cache_insert</p>';
				cache_insert($db_settings,$day->datetime,$resolvedAddress, $unitGroup, $day);
			}
	
		}

	$end_time = (new DateTime())->format('Uu');
	?>

	<?php

	$cached_items = num_cache_items($db_settings);
	echo 'Elapsed time: <b>'.(($end_time-$start_time)/1000).' ms.</b> '.$cached_items.' previous API lookups cached.</p>';
	echo '</div>'
	?>
	
	<!-- Create the HTML for the weather forecast data -->
	<?php if ($showtable == 'yes') { ?>
	<h1>Weather for <?php echo $resolvedAddress; ?></h1>
	<table>
		<tr><th>Date</th><th>Max Temp</th><th>Min Temp</th><th>Precip</th><th>Wspd</th><th>Wgust</th><th>Wdir</th><th>Cloud cover</th><th>Sunrise</th><th>Sunset</th><th>Description</th><th>icon</th></tr>
		<?php
		foreach ($days as $day) {
		?>
		<tr>
			 <td><?php echo $day->datetime; ?></td>
			 <td><center><?php echo number_format($day->tempmax,1); ?>&deg;</center></td>
			 <td><center><?php echo number_format($day->tempmin,1); ?>&deg;</center></td>
			 <td><center><?php echo number_format($day->precip,1); ?></center></td>
			 <td><center><?php echo number_format($day->windspeed,1); ?></center></td>
			 <td><center><?php echo number_format($day->windgust,1); ?></center></td>
			 <td><center><?php echo number_format($day->winddir,1); echo "&deg; (".degrees_to_direction($day->winddir); ?>)</center></td>
			 <td><center><?php echo number_format($day->cloudcover,1); ?></center></td>
			 <td><center><?php echo $day->sunrise; ?></center></td>
			 <td><center><?php echo $day->sunset; ?></center></td>
			 <td><?php echo $day->description; ?></td>
			 <td><?php echo $day->icon; ?></td>
		</tr>
		<?php } ?>
	</table>
	<?php } ?>

	<!-- show icon view -->
	<?php if ($showicons == 'yes') { ?> 
	<br>
	<h1>Weather for <?php echo $resolvedAddress; ?></h1>
	<div class="iconholder">
	<?php
		foreach ($days as $day) {
			echo '<div class="iconcell">';
			$date_dow=date("D",strtotime($day->datetime));
			$date_string=date("M j",strtotime($day->datetime));
			echo '<div class="dow_text">'.$date_dow.'</div>';
			echo '<div class="date_text">'.$date_string.'</div>';
			echo '<div class="temp_text">'.$day->tempmax.'&deg; / '.$day->tempmin.'&deg;</div>';
			echo '<center><img class="weathericon" src="images/'.$day->icon.'.png"></center>';
			echo '<div class="wind_text">'.$day->windspeed.'km/h<br>'.degrees_to_direction($day->winddir).'</div>';
			echo '</div>';
		}
	?>
	</div>
	<?php } ?>

	<div style="clear:both;"><br><h1>Shortcode view for <?php echo $resolvedAddress; ?></h1>
	
	<?php

		echo '<pre>';
		echo '[table style="font-size: 0.75em" caption="Weekly Temperature & Wind Speed" colalign="center"]';

		echo "\n";
		foreach ($days as $day) {
			echo ',';
			$date_dow=date("D",strtotime($day->datetime));
			echo $date_dow;
		}
		echo "\nHigh:";
		foreach ($days as $day) {
			echo ',';
			echo number_format($day->tempmax,1)."&deg;";	
		}
		echo "\nLow:";
		foreach ($days as $day) {
			echo ',';
			echo number_format($day->tempmin,1)."&deg;";	
		}
		echo "\nWind (km/h):";
		foreach ($days as $day) {
			echo ',';
			echo number_format($day->windspeed,1)." ".degrees_to_direction($day->winddir);	
		}

		echo "\n[/table]";
		echo '</pre></div>';
	?>


	
	<?php if ($showapi == 'yes') { ?>
	<h4>API request</h4>
	<p>
		<?php echo $api_url; ?>
	</p>

	<h4>API Response</h4>
	<p><pre>
		<?php print_r ($response_data); ?>
    </pre></p>
	<?php } ?>

	<?php if ($debug) { ?>

    <h4>Segments</h4>
	<p><pre>
		<?php print_r ($segments); ?>
    </pre></p>

    <h4>_GET</h4>
	<p><pre>
		<?php print_r ($_GET); ?>
    </pre></p>

	<h4>JSON data</h4>
	<p><pre>
		<?php print_r ($json_data); ?>
    </pre></p>

	<h4>days</h4>
	<p><pre>
		<?php print_r ($days); ?>
    </pre></p>




    <h4>Query string</h4>
	<p>
		<?php echo $query_str; ?>
	</p>

    <h4>Location</h4>
	<p>
		<?php echo $location; ?>
	</p>

	<?php } ?>
	
	
</body>
</html>
