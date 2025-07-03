<?php 

$db_settings = array("servername" => "localhost",
                     "db_name" => "storyt22_weather",
                     "table_name" => "weather_history",
                     "username" => "storyt22_wp802",
                     "password" => "0wpSU]g)58"
                );

function num_cache_items($db_settings) {

    $query = "SELECT count(*) FROM `".$db_settings["table_name"]."` WHERE 1";

    //Run the query and fetch the result
    try {
        $conn = new PDO("mysql:host=".$db_settings["servername"].";dbname=".$db_settings["db_name"], $db_settings["username"], $db_settings["password"]);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $conn->query($query);
        $count = $result->fetchColumn();
        //echo '<br><pre>'.$query.'</pre>';
        //echo '<br><p>There are '.$count.' matching records</p>'; 

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        echo $query . "<br>" . $e->getMessage();
    }

    $conn = null;

    return $count;
    
}

function check_cache($db_settings,$startdate, $numdays, $location, $unitgroup) {

    //Construct the SQL query
    $location = strtolower(substr($location,0,8));
    $startdate_tm=date_create($startdate);
    $currentdate_tm  = $startdate_tm;

    $query = "SELECT count(*) FROM `".$db_settings["table_name"]."` WHERE unitgroup='".$unitgroup."' and location='".$location."' and datekey in (";
    for ($i=0; $i<$numdays; $i++) {

        //echo '<br><p>Adding '.strval($i).' to '.date_format($startdate_tm, 'Y-m-d');;
        $date_array[$i]=date_format($currentdate_tm, 'Y-m-d');
        if ($i == ($numdays-1)) {
            $query.="'".$date_array[$i]."');";
        } else {
            $query.="'".$date_array[$i]."',";
        }
        date_add($currentdate_tm,date_interval_create_from_date_string("1 days")); 
    }

    //Run the query and fetch the result
    try {
        $conn = new PDO("mysql:host=".$db_settings["servername"].";dbname=".$db_settings["db_name"], $db_settings["username"], $db_settings["password"]);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $conn->query($query);
        $count = $result->fetchColumn();
        //echo '<br><pre>'.$query.'</pre>';
        //echo '<br><p>There are '.$count.' matching records</p>'; 

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        echo $query . "<br>" . $e->getMessage();
    }

    $conn = null;

    return $count;

}


function cache_insert($db_settings,$date,$location, $unitgroup, $json) {
    // Conditionally insert a record if it does not already exist

    $json_encoded = json_encode($json);

    // Create connection
    try {
        $conn = new PDO("mysql:host=".$db_settings["servername"].";dbname=".$db_settings["db_name"], $db_settings["username"], $db_settings["password"]);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO `".$db_settings["table_name"]."` (datekey, location, unitgroup, json, time_created)
        VALUES ('".$date."','".strtolower(substr($location,0,8))."','".$unitgroup."','".$json_encoded."', NOW())";

        //echo '<br><pre>'.$sql.'</pre>';

        $conn->exec($sql);

    } catch(PDOException $e) {
        //echo "Connection failed: " . $e->getMessage();
        //echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;
}


function retrieve_cache_item($db_settings,$current_date, $location, $unitgroup) {

    $location = strtolower(substr($location,0,8));

    $query = "SELECT json FROM `".$db_settings["table_name"]."` WHERE unitgroup='".$unitgroup."' and location='".$location."' and datekey='".$current_date."';";
    //echo '<br><p>Query from cache is:'.$query.'</p>';

    //Run the query and fetch the result
    try {
        $conn = new PDO("mysql:host=".$db_settings["servername"].";dbname=".$db_settings["db_name"], $db_settings["username"], $db_settings["password"]);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_NUM);
        //echo '<br><pre>Result is'.$result[0].'</pre>';

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        echo $query . "<br>" . $e->getMessage();
    }

    $conn = null;

    return $result[0];
}
?>

