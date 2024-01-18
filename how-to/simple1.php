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
                 language: 'pl-PL',
                 authOptions:
                 {
                    //An Azure Maps key at https://azure.com/maps. NOTE: The primary key should be used as the key.
                    authType: 'subscriptionKey',
                    subscriptionKey: 'VYw0fKIFJm_9psG1l3FjjIeB1X4SU1iR2cNDBFBJHBk'
                }
             });
         }
     </script>

     <style>
         html, body {
             margin: 0;
         }

         #myMap {
             height: 50vh;
             width: 100vw;
         }
     </style>
 </head>
 <body onload="InitMap()">
     <div id="myMap"></div>
 </body>
 </html>