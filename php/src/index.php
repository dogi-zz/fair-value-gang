<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'analyse-data.php';
require_once 'secret.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $body = file_get_contents('php://input');

  $alarm = analyseAlarm($body);
  if ($alarm !== null) {
    $data = [
      'content' => $alarm,
      'username' => 'TradingView-Bot'
    ];

    $options = [
      'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
      ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($webhook_url, false, $context);

    if ($result === FALSE) {
      echo "Fehler beim Senden der Discord-Nachricht";
    } else {
      echo "Discord-Nachricht erfolgreich gesendet";
    }
  } else {
    echo "keine Discord-Nachricht nötig";
  }

} else {
  require_once 'template.php';

  function mainContent()
  {
    ?>
    <h1>Fair Value Gang</h1>

    <a class="main-link" href="fvgs.php">FVG-Liste</a>
    <a class="main-link" href="https://raw.githubusercontent.com/dogi-zz/fair-value-gang/refs/heads/main/out/_main.fvg.result">Pine Skript Anzeigen</a>
    <a class="main-link" href="tampermonkey.php">Tampermonkey</a>

    <?php
  }

  printPage('mainContent');

  // include 'print-indicator.php';
}

?>