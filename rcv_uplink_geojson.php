<?php
// 01.01.2024 22:41 uzupełniłem kod o dodawanie danych w formacie GeoJSON do pliku rcv_uplinks.geojson
// Uwaga kod nadpisuje produkcyjny rcv_uplinks.json
//
// Produkcyjny kod znajduje się w pliku rcv_uplink.php
?>

<?php
$filenameJson = 'rcv_uplinks.json' ;
$filenameGeoJson = 'rcv_uplinks.geojson' ;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = file_get_contents('php://input');

    // Sprawdzanie, czy odebrany JSON jest poprawny
    $newObject = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Błąd dekodowania odebranego JSON";
        exit;
    }

    // Dodaj "coordinates" do nowego obiektu, jeśli longitude i latitude są dostępne
    if (isset($newObject['longitude']) && isset($newObject['latitude'])) {
        $newObject['coordinates'] = array((float)$newObject['longitude'], (float)$newObject['latitude']);
    }

    // Odczytanie i dekodowanie istniejącej zawartości pliku JSON
    if (file_exists($filenameJson) && filesize($filenameJson) > 0) {
        $fileContents = file_get_contents($filenameJson);
        $data = json_decode($fileContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // W przypadku błędu w pliku, zastąp zawartość nową tablicą
            $data = array();
        }
    } else {
        // Jeśli plik nie istnieje lub jest pusty, inicjujemy nową pustą tablicę
        $data = array();
    }

    // Dodanie nowego obiektu do tablicy
    $data[] = $newObject;

    // Sortowanie tablicy według daty w polu 'receivedDate'
    usort($data, function ($a, $b) {
        return strtotime($b['receivedDate']) - strtotime($a['receivedDate']);
    });

    // Zachowanie tylko 50 najnowszych obiektów
    $data = array_slice($data, 0, 50);

    // Zapisanie zmodyfikowanej tablicy do pliku JSON
    file_put_contents($filenameJson, json_encode($data, JSON_PRETTY_PRINT));

    // Generowanie pliku GeoJSON
    generateGeoJsonFile($filenameJson, $filenameGeoJson);

    http_response_code(200);
} else {
    http_response_code(405);
    echo "Metoda nie dozwolona";
}

// Funkcja do generowania pliku GeoJSON
function generateGeoJsonFile($inputJsonFile, $outputGeoJsonFile) {
    $data = json_decode(file_get_contents($inputJsonFile), true);

    // Ogranicz do ostatnich 50 obiektów
    $data = array_slice($data, 0, 50);

    $geoJson = array(
        'type' => 'FeatureCollection',
        'metadata' => array(
            'generated' => strtotime('now') * 1000, // milliseconds since epoch
            'title' => 'nemo 2 space assets locations',
            'api' => '0.0.1',
            'count' => count($data)
        ),
        'features' => array()
    );

    foreach ($data as $item) {
        if (isset($item['coordinates'])) {
            $feature = array(
                'type' => 'Feature',
                'properties' => $item,
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => $item['coordinates']
                )
            );
            unset($feature['properties']['coordinates']); // Remove redundant coordinates property
            $geoJson['features'][] = $feature;
        }
    }

    // Zapisz plik GeoJSON
    file_put_contents($outputGeoJsonFile, json_encode($geoJson, JSON_PRETTY_PRINT));
}
?>
