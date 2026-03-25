<?php
// Passwort-Schutz
if (!isset($_GET['pw']) || $_GET['pw'] !== 'Borkum2026') { 
    header('HTTP/1.1 403 Forbidden');
    exit; 
}

$logFile = 'aufrufe_details.log';
$totalFile = 'gesamtzahl.log';

if (!file_exists($logFile)) exit;

$total = file_exists($totalFile) ? file_get_contents($totalFile) : 0;
$logEntries = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// E-Mail Empfänger
$to = "stefan@familieschmidt.biz";
$subject = "WSG BorKum Statistik: " . date('d.m.Y');

// Header für HTML-E-Mails
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Statistik <statistik@familieschmidt.biz>" . "\r\n";

// HTML-Inhalt zusammenbauen
$message = "
<html>
<head>
<style>
    table { border-collapse: collapse; width: 100%; max-width: 400px; font-family: sans-serif; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #004a99; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>
</head>
<body>
    <h2>Statistik-Bericht WSG Borkum</h2>
    <p><strong>Gesamtaufrufe seit Start:</strong> $total</p>
    <table>
        <tr><th>Datum</th><th>Uhrzeit</th></tr>";

// Log-Einträge von neu nach alt (umgedreht)
foreach (array_reverse($logEntries) as $line) {
    $parts = explode(';', $line);
    if (count($parts) == 2) {
        $message .= "<tr><td>{$parts[0]}</td><td>{$parts[1]}</td></tr>";
    }
}

$message .= "
    </table>
</body>
</html>";

// Senden
if (mail($to, $subject, $message, $headers)) {
    echo "OK";
    // Optional: Log leeren, falls du nur "neue" Einträge pro Mail willst:
    // file_put_contents($logFile, "");
}
?>