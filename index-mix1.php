<!--  -->
<!-- Status: Cardinal Spline & Pulse markup integrated -->
<!-- Developing: Popup integration -->
<!-- Page that comprises 3 projects: index-cso.php, how-to/popup3.php & index.php (marker pulse animation) -->
<!-- Data source: rcv_uplinks.json -->


<?php
$filePath = '/rcv_uplinks.json';
$jsonContent = file_get_contents ( $filePath ) ;
if ( $jsonContent === false )
    die ( 'Błąd odczytu pliku.' ) ;
$locationData = json_decode ( $jsonContent , true ) ;
if ($locationData === null)
    die ( 'Błąd dekodowania danych JSON.' ) ;
foreach ( $locationData as $location )
{
    $line_string[] = $location['coordinates'] ;
    $createdDate[] = $location['createdDate'] ;
}
$line_string = array_reverse ( $line_string ) ;
$youngest_location = reset ( $locationData ) ;
$no_of_locations = count ( $locationData ) ;
// echo json_encode ( $youngest_location[ "coordinates" ] ) ; // Last location
// echo json_encode ( $locationData[ $no_of_locations - 1 ][ "coordinates" ] ) ; // Oldest (first) location
// echo json_encode ( $locationData[0]["coordinates"] ) ; // Last location
// echo json_encode ( $locationData[0]["deviceGuid"] ) ;
// echo count ( $no_of_locations ) ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mix 1 project - nemo 2 cloud</title>

    <meta charset="utf-8" />
    <link rel="shortcut icon" href="/favicon.ico" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="This sample provides a set of controls to test the various features of the Cardinal Spline calculation." />
    <meta name="keywords" content="Microsoft maps, map, gis, API, SDK, spatial math, math, spline, cardinal spline, curves, lines, line layer" />
    <meta name="author" content="Microsoft Azure Maps" />
    <meta name="version" content="1.0" />
    <meta name="screenshot" content="screenshot.jpg" />

    <!-- Add references to the Azure Maps Map control JavaScript and CSS files. -->
    <link href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" rel="stylesheet" />
    <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>

    <script>
        var map, datasource, spline, tension = 0.5, nodeSize = 30, close = false;
        var pulse_marker ;
        var json_data ;

        //Sample positions.
        var positions = <?php echo json_encode ( $line_string ) ; ?>;
        var createdDate = <?php echo json_encode ( $createdDate ) ; ?>;

        function GetMap() {
            //Initialize a map instance.
            map = new atlas.Map('myMap', {
                center: <?php echo json_encode ( $locationData[0]['coordinates'] ) ; ?>,
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

                //Create a layer to render lines.
                var line_layer = new atlas.layer.LineLayer(datasource, null, {
                    strokeColor: ['get', 'color'],
                    strokeWidth: ['get', 'width']
                })
                //Create a layer for visualizing the lines on the map.
                map.layers.add( line_layer ) ;

                spline = new atlas.Shape(new atlas.data.LineString(atlas.math.getCardinalSpline(positions, tension, nodeSize, close)), null, {
                    color: 'blue',
                    width: 2
                });

                //Add the lines to the data source.
                datasource.add ( [ spline ] ) ;

                // Create a non-draggable HTML marker for each position.
                // Cancel line: htmlContent: '<div class="dot"></div>', to have standard pin marker
                for ( var i = 0 ; i < positions.length ; i++ ) {
                    if ( i != positions.length - 1 ) {
                        var marker = new atlas.HtmlMarker({
                            htmlContent: '<div class="dot"></div>',
                            draggable: false,
                            position: positions[i]
                        });
                    } else {
                        var marker = new atlas.HtmlMarker ({
                            htmlContent: '<div class="pulseIcon"></div>',
                            position: positions[i]
                        });
                    }

                    //Add the marker to the map.
                    map.markers.add ( marker ) ;
                }

            });
        }

    </script>
    <style type="text/css">
        .dot
        {
            display: block ;
            width: 10px ;
            height: 10px ;
            border-radius: 50% ;
            background: blue ;
            border: 2px solid white ;
            cursor: pointer ;
            box-shadow: 0 0 0 rgba( 0 , 204 , 255 , 0.4 ) ;
            animation: none ;
        }.pulseIcon
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
<body onload="GetMap()">
    <div id="myMap" style="position:relative;width:100%;min-width:290px;height:600px;"></div>
    <fieldset style="width:calc(100% - 30px);min-width:290px;margin-top:10px;">
        <legend>Cardinal Spline Options</legend>
        This sample provides a set of controls to test the various features of the Cardinal Spline calculation. 
        The red line shows the straight line path between the sample points, and the blue line shows the cardinal spline between the same set of points.
        Drag the markers, change the tension and node size using the sliders, or make the cardinal spline a closed shape.
    </fieldset>
</body>
</html>