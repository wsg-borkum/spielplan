<?php
// Erlaubt den Zugriff (CORS), falls du später doch GitHub Pages nutzen willst
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/calendar; charset=utf-8");

// Verhindert, dass Fehlermeldungen das iCal-Format zerstören
error_reporting(0);

if (isset($_GET['url'])) {
    $url = $_GET['url'];

    // Sicherheits-Check: Nur URLs von TischtennisLive erlauben
    if (strpos($url, 'tischtennislive.de') !== false) {
        
        // Wir nutzen cURL für maximale Stabilität
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        // Simulation eines echten Browsers, um Blockaden zu vermeiden
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        
        // Timeout nach 10 Sekunden, falls der Server hängt
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && !empty($response)) {
            echo $response;
        } else {
            // Falls der Server leer antwortet oder einen Fehler liefert
            http_response_code(502);
            echo "Fehler: Die Datenquelle konnte nicht erreicht werden (Status: $httpCode).";
        }
    } else {
        http_response_code(403);
        echo "Fehler: Ungültige URL-Quelle.";
    }
} else {
    echo "WSG Borkum iCal Proxy aktiv. Bitte URL-Parameter übergeben.";
}
?>
