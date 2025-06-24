<?php
require_once 'analyse-data.php';

file_put_contents('fvgs.txt', file_get_contents('fvgs_2.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
file_put_contents('alarmlog.txt', '');


$zeilen = file("alarmlog_2.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$umgekehrt = array_reverse($zeilen);
foreach ($umgekehrt as $zeile) {
  var_dump(analyseAlarm($zeile));
}

?>