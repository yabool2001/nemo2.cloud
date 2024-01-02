<!-- Wersja demonstracyjna i NIEdziałająca, opracowana podstawie przykładu Moving dashed line: https://github.com/Azure-Samples/AzureMapsCodeSamples/blob/main/Samples/Animations/Moving%20dashed%20line/Moving%20dashed%20line.html -->
<!-- Źródłem danych jest plik rcv_uplinks.json -->

<?php
$filePath = '/rcv_uplinks.json';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Animate marker along path - Azure Maps Web SDK Samples</title>

    <meta charset="utf-8" />
	<link rel="shortcut icon" href="/favicon.ico"/>
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="This sample shows how to easily animate a HTML marker along a path on the map." />
    <meta name="keywords" content="Microsoft maps, map, gis, API, SDK, animation, animate, animations, point, symbol, pushpin, marker, pin" />
    <meta name="author" content="Microsoft Azure Maps" /><meta name="version" content="1.0" />
    <meta name="screenshot" content="screenshot.gif" />

    <!-- Add references to the Azure Maps Map control JavaScript and CSS files. -->
    <link href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" rel="stylesheet" />
    <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>

    <!-- Add reference to the animation module. -->
    <script src="/lib/azure-maps/azure-maps-animations.min.js"></script>

    <script>
        var map, layer, animation;

        function GetMap() {
            //Initialize a map instance.
            map = new atlas.Map('myMap', {
                center: <?php echo json_encode ( $youngest_location['coordinates'] ) ; ?>,
                zoom: 5,
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
                //Create a data source and add it to the map.
                datasource = new atlas.source.DataSource();
                map.sources.add(datasource);

                //Add a line.
                datasource.add(new atlas.data.LineString ( <?php echo json_encode ( $line_string ) ; ?> ) ) ;
           
                //Add a layer for rendering line data. 
                layer = new atlas.layer.LineLayer(datasource, null, {                            
                    strokeWidth: 4
                });
                map.layers.add(layer);

                //Create a moving dashed line animation.
                animation = atlas.animations.flowingDashedLine(layer, { duration: 2000, autoPlay: true, loop: true, reverse: false });
            });
        }
    </script>
</head>
<body onload="GetMap()">
    <div id="myMap" style="position:relative;width:100%;min-width:290px;height:600px;"></div>

    <div style="position:absolute;top:0px;left:calc(50% - 100px);background-color:white;padding:5px;">Click the map to animate marker.</div>

    <fieldset style="width:calc(100% - 30px);min-width:290px;margin-top:10px;">
        <legend>Animate marker along path</legend>
        This sample shows how to easily animate a HTML marker along a path on the map.
        This sample uses the open source <a href="https://github.com/Azure-Samples/azure-maps-animations" target="_blank">Azure Maps Animation module</a>
    </fieldset>
</body>
</html>