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
$youngestLocation = reset ( $locationData ) ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Arrow along a Path - Azure Maps Web SDK Samples</title>

    <meta charset="utf-8" />
	<link rel="shortcut icon" href="/favicon.ico"/>
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="This sample shows how to add arrow icons along a line on the map. " />
    <meta name="keywords" content="Microsoft maps, map, gis, API, SDK, linestring, arrows, path, symbols, linelayer" />
    <meta name="author" content="Microsoft Azure Maps" /><meta name="version" content="1.0" />
    <meta name="screenshot" content="screenshot.jpg" />

    <!-- Add references to the Azure Maps Map control JavaScript and CSS files. -->
    <link href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" rel="stylesheet" />
    <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>

    <script>
        var map, datasource;

        function GetMap() {
            //Initialize a map instance.
            map = new atlas.Map('myMap', {
                center: [20.8080, 52.2525],
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

                //Load the custom image icon into the map resources.
                map.imageSprite.add('arrow-icon', '/res/purpleArrowRight.png').then(function () {

                    //Create a data source to add your data to.
                    datasource = new atlas.source.DataSource();
                    map.sources.add(datasource);

                    //Create a line and add it to the data source.
                    //datasource.add(new atlas.data.Feature(new atlas.data.LineString([[-122.18822, 47.63208],[-122.18204, 47.63196]])));
                    datasource.add(new atlas.data.Feature(new atlas.data.LineString(<?php echo json_encode ( $line_string ) ; ?>)));
                    
                    // const geojsonFilePath = 'rcv_uplinks.geojson';
                    // const geojsonString = fs.readFileSync(geojsonFilePath, 'utf8');
                    // const geojsonFeatures = JSON.parse(geojsonString);
                    // var lineStringCoordinates = geojsonFeatures.geometry.coordinates;
                    // console.log (lineStringCoordinates) ;
                    //var lineString = new atlas.data.LineString(lineStringCoordinates);

                    //Add a layers for rendering data.
                    map.layers.add([
                        //Add a line layer for displaying the line.
                        new atlas.layer.LineLayer(datasource, null, {
                            strokeColor: 'DarkOrchid',
                            strokeWidth: 3
                        }),

                        //Add a symbol layer for rendering the arrow along the line.
                        new atlas.layer.SymbolLayer(datasource, null, {
                            lineSpacing: 100,
                            placement: 'line',
                            iconOptions: {
                                image: 'arrow-icon',
                                allowOverlap: true,
                                anchor: 'center',
                                size: 0.8
                            }
                        })
                    ]);
                });
            });
        }
    </script>
</head>
<body onload="GetMap()">
    <div id="myMap" style="position:relative;width:100%;min-width:290px;height:600px;"></div>

    <fieldset style="width:calc(100% - 30px);min-width:290px;margin-top:10px;">
        <legend>Add Arrow along a Path</legend>
        This sample shows how to add arrow icons along a line on the map. 
        When using a symbol layer, set the "placement" option to "line", this will render the symbols along the line and rotate the icons (0 degrees = right).
    </fieldset>
</body>
</html>