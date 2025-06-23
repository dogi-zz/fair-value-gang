<?php
require_once 'template.php';
require_once 'analyse-data.php';

function round1($value): string
{
  if ($value === null) {
    return '--';
    ;
  }
  return number_format($value, 1, '.', '');
}


function timestampToString($ms): string
{
  if ($ms === null) {
    return 'n.a';
    ;
  }
  $seconds = (int) floor($ms / 1000);
  return date('Y-m-d H:i', $seconds);
}

function inhalt()
{
  echo '<a href="index.php">← zurück zur Hauptseite</a>';

  echo '<table>';
  echo '<thead><tr>';
  echo '<th>Name</th>';
  echo '<th>Zeit</th>';
  echo '<th></th>';
  echo '<th>Größe</th>';
  echo '<th>Ratio</th>';
  echo '<th>Status</th>';
  echo '</tr></thead>';
  echo '<tbody>';

  foreach (readFvgs() as $fvg) {
    echo '<tr>';
    echo "<td>" . $fvg['symbol'] . "</td>";

    $timestamp = getFromArrayInt($fvg, 'timestamp');
    $size = getFromArrayFloat($fvg, 'size');
    $dir = getFromArray($fvg, 'dir');
    $ratio = getFromArrayFloat($fvg, 'ratio');
    $arrow = '';
    if ($dir==='bull') {
      $arrow = ' ↗';
    }
    if ($dir==='bear') {
      $arrow = ' ↘';
    }

    echo "<td>" . timestampToString($timestamp) . "</td>";
    echo "<td class='center'>" . $arrow . "</td>";
    echo "<td>" . round1($size) . "%</td>";
    echo "<td>" . round1($ratio) . "</td>";

    if ($fvg['type'] === 'ENTER') {
      echo '<td class="center"><span class="badge active">ENTER</span></td>';
    } else if ($fvg['type'] === 'NEAR') {
      echo '<td class="center"><span class="badge near">NEAR</span></td>';
    } else if ($fvg['type'] === 'DETECTED') {
      echo '<td class="center"><span class="badge inactive">inaktiv</span></td>';
    } else if ($fvg['type'] === 'INVALIDATED') {
      echo '<td class="center"><span class="badge invalidated">invalidiert</span></td>';
    } else {
      echo '<td></td>';
    }

    // echo "<td>" . ($i % 2 === 0 ? '<span class="badge inactive">inaktiv</span>' : '<span class="badge active">aktiv</span>') . "</td>";
    echo '</tr>';
  }

  echo '</tbody></table>';
}

printPage('inhalt', true);