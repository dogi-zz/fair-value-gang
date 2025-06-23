<?php


$url = 'https://raw.githubusercontent.com/dogi-zz/fair-value-gang/main/_result_';
  $content = file_get_contents($url);

  if ($content === false) {
    echo "Fehler beim Abrufen der Datei.";
    exit;
  }

  header("Content-Type: text/plain");
  echo ($content);

?>