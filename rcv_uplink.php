<?php

// 29.12.2023 00:48 uzupełniłem kod o dodawanie do pozycji w json tablicy złożonej z otrzymanych longitude i latitude, np. "coordinates":[21.042707,52.3931105]
// Wszystko jest w jednym opisanym bloku 
// Poprzedni kod znajduje się w pliku rcv_uplink backup.php


$filename = 'rcv_uplinks.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = file_get_contents('php://input');

    // Sprawdzanie, czy odebrany JSON jest poprawny
    $newObject = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Błąd dekodowania odebranego JSON";
        exit;
    }

    // UWAGA!!! NOWY KOD JEST TUTAJ:
    // Utworzenie nowego elementu coordinates składającego się z long i lat
    if ( isset ( $newObject['longitude'] ) && isset ( $newObject['latitude'] ) )
    {
        $newObject['coordinates'] = array ( (float) $newObject['longitude'] , (float) $newObject['latitude'] ) ;
    }
    // Skrócenie czasu poprzez usunięcie milisekund
    if ( isset ( $newObject['createdDate'] ) )
    {
        $newObject['createdDate'] = date ( 'Y-m-d H:i:s' , strtotime ( $newObject['createdDate'] ) ) ;
    }
    if ( isset ( $newObject['receivedDate'] ) )
    {
        $newObject['receivedDate'] = date ( 'Y-m-d H:i:s' , strtotime ( $newObject['receivedDate'] ) ) ;
    }

    // Odczytanie i dekodowanie istniejącej zawartości pliku
    if (file_exists($filename) && filesize($filename) > 0) {
        $fileContents = file_get_contents($filename);
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

    // Zapisanie zmodyfikowanej tablicy do pliku
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

    http_response_code(200);
} else {
    http_response_code(405);
    echo "Metoda nie dozwolona";
}
?>