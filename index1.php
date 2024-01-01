<?php
$json_path = 'rcv_uplinks.json' ;
$json_content = file_get_contents ( $json_path ) ;
if ( $json_content === false )
    die ( 'Błąd odczytu pliku.' ) ;
$locations_data = json_decode ( $json_content , true ) ;
if ( $locations_data === null )
    die ( 'Błąd dekodowania danych JSON.' ) ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HTML Marker Pulse Animation - Azure Maps Web SDK Samples</title>

    <meta charset="utf-8" />
	<link rel="shortcut icon" href="/favicon.ico"/>
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="This sample shows how to pulse animate the position of a HTML marker on the map using CSS." />
    <meta name="keywords" content="Microsoft maps, map, gis, API, SDK, animate, animation, symbol, pushpin, marker, pin" />
    <meta name="author" content="Microsoft Azure Maps" /><meta name="version" content="1.0" />
    <meta name="screenshot" content="screenshot.gif" />

    <!-- Add references to the Azure Maps Map control JavaScript and CSS files. -->
    <link href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" rel="stylesheet" />
    <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>

    <script>
        var map, marker;

        function GetMap()
        {
            // Parsowanie lokalizacji z JSON/PHP
            var astro_locations = <?php echo json_encode ( $locations_data ) ; ?> ;
            //console.log ( astro_locations ) ;

            //Initialize a map instance.
            map = new atlas.Map('myMap', {
                center: [astro_locations[0].longitude , astro_locations[0].latitude] ,
                view: 'Auto',

                //Add authentication details for connecting to Azure Maps.
                authOptions:
                {
                    //An Azure Maps key at https://azure.com/maps. NOTE: The primary key should be used as the key.
                    authType: 'subscriptionKey',
                    subscriptionKey: 'VYw0fKIFJm_9psG1l3FjjIeB1X4SU1iR2cNDBFBJHBk'
                }
            });

            //Wait until the map resources are ready.
            map.events.add('ready', function () {
                //Create a HTML marker and add it to the map.
                marker = new atlas.HtmlMarker (
                {
                    htmlContent: '<div class="pulseIcon"></div>',
                    position: [astro_locations[0].longitude , astro_locations[0].latitude]
                });

                map.markers.add(marker);
            });
        }

    </script>
    <style type="text/css">
        .pulseIcon
        {
            display: block ;
            width: 10px ;
            height: 10px ;
            border-radius: 50% ;
            background: orange ;
            border: 2px solid white ;
            cursor: pointer ;
            box-shadow: 0 0 0 rgba( 0 , 204 , 255 , 0.4 ) ;
            animation: pulse 3s infinite ;
        }

        .pulseIcon:hover
        {
            animation: none ;
        }

        @keyframes pulse
        {
            0%
            {
                box-shadow: 0 0 0 0 rgba( 0 , 204 , 255 , 0.4 ) ;
            }

            70%
            {
                box-shadow: 0 0 0 50px rgba( 0 , 204 , 255 , 0 ) ;
            }

            100%
            {
                box-shadow: 0 0 0 0 rgba( 0 , 204 , 255 , 0 ) ;
            }
        }
    </style>
</head>
<body onload='GetMap()'>
    <div id="myMap" style="position:relative;width:100%;min-width:290px;height:600px;"></div>
</body>
</html>