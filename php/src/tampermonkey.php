<?php
require_once 'template.php';


function inhalt()
{
  echo '<a class="main-link" href="index.php">← zurück zur Hauptseite</a>';

  echo '<p>Schritt 1: <a href="https://www.tampermonkey.net/">Tampermonkey Installieren</a></p>';

  echo '<p>Schritt 2: Neues Userscript anlegen</p>';

  echo '<p>Schritt 3: Inhalt hier reinkopieren</p>';

  echo '<textarea>'.file_get_contents('tampermonkey-bitunix.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES).'</textarea>';

  echo '';

}

printPage('inhalt', true);

?>