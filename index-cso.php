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
    <title>Cardinal Spline Options - Azure Maps Web SDK Samples</title>

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
        var map, datasource, spline, straightLine, tension = 0.5, nodeSize = 30, close = false;

        //Sample positions.
        var positions = <?php echo json_encode ( $line_string ) ; ?>;

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

                //Create a layer for visualizing the lines on the map.
                map.layers.add(
                    new atlas.layer.LineLayer(datasource, null, {
                        strokeColor: ['get', 'color'],

                        strokeWidth: ['get', 'width']
                    })
                );

                //Create to line shapes for easy updating. A red straightline will be created to show the straight line path between the points.
                straightLine = new atlas.Shape(new atlas.data.LineString(positions), null, {
                    color: 'red',
                    width: 0
                });

                spline = new atlas.Shape(new atlas.data.LineString(atlas.math.getCardinalSpline(positions, tension, nodeSize, close)), null, {
                    color: 'blue',
                    width: 2
                });

                //Add the lines to the data source.
                datasource.add([straightLine, spline]);

                //Create a draggable HTML marker for each position.
                for (var i = 0; i < positions.length; i++) {
                    var marker = new atlas.HtmlMarker({
                        draggable: true,
                        position: positions[i]
                    });

                    //Store the position index in the marker properties so that we can easily update the cardinal spline as the marker is dragged.
                    marker.properties = {
                        index: i
                    };

                    //Add the marker to the map.
                    map.markers.add(marker);

                    //Create a drag event for the marker.
                    map.events.add('drag', marker, markerDragged);
                }
            });
        }

        function markerDragged(e) {
            //Update the position the marker represents.
            positions[e.target.properties.index] = e.target.getOptions().position;

            //Calculate the cardinal spline coordinates and update the spline.
            spline.setCoordinates(atlas.math.getCardinalSpline(positions, tension, nodeSize, close));

            //Update the lines with the new positions.
            straightLine.setCoordinates(positions);
        }

        function calculateSpline() {
            //Get the spline options.
            tension = parseFloat(document.getElementById("tensionSlider").value);
            nodeSize = parseInt(document.getElementById("nodeSizeSlider").value);
            close = document.getElementById("closeChbx").checked;

            //Calculate positions for the new cardinal spline options and update the spline coordinates.
            spline.setCoordinates(atlas.math.getCardinalSpline(positions, tension, nodeSize, close));
        }
    </script>
</head>
<body onload="GetMap()">
    <div id="myMap" style="position:relative;width:100%;min-width:290px;height:600px;"></div>

    <!-- Odkomentuj poniższy kod, żeby regulować linie ale środkowa regulacja nie działa. Dlatego lepiej nie odblokowywać. -->
    <!-- <div style="position:absolute;top:10px; left:10px;border-radius:10px;background-color:white;">
        <table>
            <tr>
                <td>Tension:</td>
                <td>
                    <form oninput="tension.value=tensionSlider.value">
                        <input type="range" id="tensionSlider" value="0.5" min="-2" max="2" step="0.1" oninput="calculateSpline()" onchange="calculateSpline()" />
                        <output name="tension" for="tensionSlider">0.5</output>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Node Size:</td>
                <td>
                    <form oninput="nodeSize.value=nodeSizeSlider.value">
                        <input type="range" id="nodeSizeSlider" value="30" min="2" max="100" step="1" oninput="calculateSpline()" onchange="calculateSpline()" />
                        <output name="nodeSize" for="nodeSizeSlider">30</output>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Close:</td>
                <td>
                    <input id="closeChbx" type="checkbox" onchange="calculateSpline()" />
                </td>
            </tr>
        </table>
    </div> -->

    <fieldset style="width:calc(100% - 30px);min-width:290px;margin-top:10px;">
        <legend>Cardinal Spline Options</legend>
        This sample provides a set of controls to test the various features of the Cardinal Spline calculation. 
        The red line shows the straight line path between the sample points, and the blue line shows the cardinal spline between the same set of points.
        Drag the markers, change the tension and node size using the sliders, or make the cardinal spline a closed shape.
    </fieldset>
</body>
</html>