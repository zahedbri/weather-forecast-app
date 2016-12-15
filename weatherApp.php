<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/14/16
 * Time: 10:37 AM
 */

// display errors
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//----------------------------


// get lat / lon from zipcode

$zipcode_query = $_GET;

if(array_key_exists('zipcode', $zipcode_query)){

    //echo $zipcode_query['zipcode'];

    $geoCodeUrl = "http://maps.googleapis.com/maps/api/geocode/json?address=".$zipcode_query['zipcode']."&sensor=false";

    $LatLng = file_get_contents($geoCodeUrl);

    $locationData = json_decode($LatLng,true);

    $lat = $locationData['results'][0]['geometry']['location']['lat'];
    $lon = $locationData['results'][0]['geometry']['location']['lng'];

} else {

    //echo "no zipcode entered yet.. <br>";
}


// get forecast

$urlToParse;

if(isset($lat)) {

    //echo "now do this.. <br>";

    $urlToParse = "http://forecast.weather.gov/MapClick.php?lat=".$lat."&lon=".$lon."&unit=0&lg=english&FcstType=json";

} else {

    //echo "standing by for lat variable to be set..";

}

//-----------------------------

$url = $urlToParse;

// create curl resource
$ch = curl_init();

// set url
curl_setopt($ch, CURLOPT_URL, $url);

//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

// $output contains the output string
$output = curl_exec($ch);

// close curl resource to free up system resources
curl_close($ch);

$json_a = json_decode($output,true);

// <---------- Display Conditions ----------> //

$area = $json_a['location']['areaDescription'];
$tonight = $json_a['time']['startPeriodName'][0];
$forecast_text = $json_a['data']['text'][0];

$forcast_data = "
            <p>forcast for: $area<p>
		    <p>$tonight<p>            
            <p>$forecast_text<p>";

// for loop to loop through string and output results
$day = $json_a['time']['startPeriodName'];
$text = $json_a['data']['text'];

// <--------------------------notes----------------------------->
// json file -
// http://forecast.weather.gov/MapClick.php?lat=39.0434&lon=-76.0635&unit=0&lg=english&FcstType=json
// reference this - http://stackoverflow.com/questions/36356334/parsing-national-weather-service-json-with-php?noredirect=1&lq=1

?>

<!doctype html>
<html>
<head>
    <title>Example Domain</title>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
        body {
            background-color: #f0f0f2;
            margin: 0;
            padding: 0;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;

        }
        div {
            width: 600px;
            margin: 5em auto;
            padding: 50px;
            background-color: #fff;
            border-radius: 1em;
        }
        a:link, a:visited {
            color: #38488f;
            text-decoration: none;
        }
        @media (max-width: 700px) {
            body {
                background-color: #fff;
            }
            div {
                width: auto;
                margin: 0 auto;
                border-radius: 0;
                padding: 1em;
            }
        }
    </style>
</head>

<body>

<div>
    <h1>Example Domain</h1>
    <p>get weather forecast</p>

    <form action="" method="get">
        <label for="zipcode"> Enter zipcode:</label>
        <input id="zipcode" name="zipcode" pattern="[\d]{5}">
        <input type="submit" value="Submit">
    </form>


</div>

<div id="forecast">
    <h3>Forcast for: <?php echo $area; ?></h3>

    <?php

    for ($i=0; $i < count($day); $i++) {

        echo "<p>".$day[$i]."<br>".$text[$i]."</p>";
    }

    ?>

</div>

<div id="radar">


</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

</body>
</html>
