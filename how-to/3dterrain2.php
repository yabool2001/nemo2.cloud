<!-- Wersja działająca opracowana na podstawie przykładu : https://learn.microsoft.com/en-us/azure/azure-maps/how-to-use-map-control -->
<!-- Źródłem danych jest plik ../rcv_uplinks.json -->

<?php

$filePath = '../rcv_uplinks.json';
$jsonContent = file_get_contents ( $filePath ) ;
if ( $jsonContent === false )
    die ( 'Błąd odczytu pliku.' ) ;
$locationData = json_decode ( $jsonContent , true ) ;
if ($locationData === null)
    die ( 'Błąd dekodowania danych JSON.' ) ;
foreach ( $locationData as $location )
    $line_string[] = $location['coordinates'] ;
$line_string = array_reverse ( $line_string ) ;
$youngest_location = reset ( $locationData ) ;
// echo json_encode ( $youngest_location["coordinates"] ) ;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <title>Elevation - Azure Maps Web SDK Samples</title>
        <link rel="stylesheet" href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" type="text/css">
        <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>
        <style>
            html,
            body {
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
            }
            #map {
                width: 100%;
                height: 100%;
            }
        </style>
    </head>

    <body>
        <div id="map"></div>
        <script>
            var map = new atlas.Map("map", {
                center: <?php echo json_encode ( $youngest_location["coordinates"] ) ; ?>,
                maxPitch: 85,
                pitch: 60,
                zoom: 12,
                style: "road_shaded_relief",
                // Get an Azure Maps key at https://azuremaps.com/.
                authOptions:
                {
                    //An Azure Maps key at https://azure.com/maps. NOTE: The primary key should be used as the key.
                    authType: 'subscriptionKey',
                    subscriptionKey: 'VYw0fKIFJm_9psG1l3FjjIeB1X4SU1iR2cNDBFBJHBk'
                }
            });

            // Create a tile source for elevation data. For more information on creating
            // elevation data & services using open data, see https://aka.ms/elevation
            var elevationSource = new atlas.source.ElevationTileSource("elevation", {
                url: "<tileSourceUrl>"
            });

            // Wait until the map resources are ready.
            map.events.add("ready", (event) => {

                // Add the elevation source to the map.
                map.sources.add(elevationSource);

                // Enable elevation on the map.
                map.enableElevation(elevationSource);
            });
        </script>
    </body>
</html>