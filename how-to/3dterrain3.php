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
 <html>
 <head>
     <title></title>

     <meta charset="utf-8">

     <!-- Ensures that IE and Edge uses the latest version and doesn't emulate an older version -->
     <meta http-equiv="x-ua-compatible" content="IE=Edge">

     <!-- Ensures the web page looks good on all screen sizes. -->
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

     <!-- Add references to the Azure Maps Map control JavaScript and CSS files. -->
     <link rel="stylesheet" href="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.css" type="text/css">
     <script src="https://atlas.microsoft.com/sdk/javascript/mapcontrol/3/atlas.min.js"></script>


     <script type="text/javascript">
         //Create an instance of the map control and set some options.
         function InitMap()
         {
             var map = new atlas.Map('myMap', {
                 center: <?php echo json_encode ( $youngest_location["coordinates"] ) ; ?>,
                 zoom: 5,
                 language: 'en-US',
                 authOptions:
                {
                    //An Azure Maps key at https://azure.com/maps. NOTE: The primary key should be used as the key.
                    authType: 'subscriptionKey',
                    subscriptionKey: 'VYw0fKIFJm_9psG1l3FjjIeB1X4SU1iR2cNDBFBJHBk'
                }
             });
         }
        //Define an HTML template for a custom popup content laypout.

        var popupTemplate = '<div class="customInfobox"><div class="name">{name}</div>{description}</div>';

        //Create a data source and add it to the map.
        var dataSource = new atlas.source.DataSource();
        map.sources.add(dataSource);

        dataSource.add(new atlas.data.Feature(new atlas.data.Point(<?php echo json_encode ( $youngest_location["coordinates"] ) ; ?>), {
        name: 'Microsoft Building 41', 
        description: '15571 NE 31st St, Redmond, WA 98052'
        }));

        //Create a layer to render point data.
        var symbolLayer = new atlas.layer.SymbolLayer(dataSource);

        //Add the polygon and line the symbol layer to the map.
        map.layers.add(symbolLayer);

        //Create a popup but leave it closed so we can update it and display it later.
        popup = new atlas.Popup({
        pixelOffset: [0, -18],
        closeButton: false
        });

        //Add a hover event to the symbol layer.
        map.events.add('mouseover', symbolLayer, function (e) {
        //Make sure that the point exists.
        if (e.shapes && e.shapes.length > 0) {
            var content, coordinate;
            var properties = e.shapes[0].getProperties();
            content = popupTemplate.replace(/{name}/g, properties.name).replace(/{description}/g, properties.description);
            coordinate = e.shapes[0].getCoordinates();

            popup.setOptions({
            //Update the content of the popup.
            content: content,

            //Update the popup's position with the symbol's coordinate.
            position: coordinate

            });
            //Open the popup.
            popup.open(map);
        }
        });

        map.events.add('mouseleave', symbolLayer, function (){
        popup.close();
        });
     </script>

     <style>
         html, body {
             margin: 0;
         }

         #myMap {
             height: 100vh;
             width: 100vw;
         }
     </style>
 </head>
 <body onload="InitMap()">
     <div id="myMap"></div>
 </body>
 </html>