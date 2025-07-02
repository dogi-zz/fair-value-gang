<?php
require_once 'template.php';


function inhalt()
{
  $file = 'tampermonkey-bitunix.txt';
  if (isset($_GET['script']) && $_GET['script'] === 'trading-view'){
    $file = 'tampermonkey-trading-view.txt';
  }

  echo '<a class="main-link" href="index.php">← zurück zur Hauptseite</a>';

  echo '<p>Schritt 1: <a href="https://www.tampermonkey.net/">Tampermonkey Installieren</a></p>';

  echo '<p>Schritt 2: Neues Userscript anlegen</p>';

  echo '<p>Schritt 3: Inhalt hier reinkopieren</p>';

  echo '<div class="tab-links">';
  echo '<a class="main-link" href="tampermonkey.php">Bitunix</a>';
  echo '<a class="main-link" href="tampermonkey.php?script=trading-view">TradingView</a>';
  echo '</div>';
      

  echo '<textarea>'.file_get_contents($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES).'</textarea>';

  echo '';

}

printPage('inhalt', true);

?>