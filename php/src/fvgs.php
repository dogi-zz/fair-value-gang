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


function prepareFvgGroups(&$fvgGroups, $fvg)
{
  $groupForTime = null;
  foreach ($fvgGroups as &$group) {
    if ($group['timestamp'] === $fvg['timestamp']) {
      array_push($group['fvgs'], $fvg);
      return;
    }
  }
  $groupForTime = array('timestamp' => $fvg['timestamp'], 'fvgs' => array());
  array_push($groupForTime['fvgs'], $fvg);
  array_push($fvgGroups, $groupForTime);
}

function getDirection($fvg)
{
  $dir = getFromArray($fvg, 'dir');
  if ($dir === 'bull') {
    return ' ↗';
  }
  if ($dir === 'bear') {
    return ' ↘';
  }
  return '';
}

function getBadgeClass($fvg)
{
  $type = getFromArray($fvg, 'type');
  if ($type === 'ENTER') {
    return 'active';
  } else if ($type === 'NEAR') {
    return 'near';
  } else if ($type === 'DETECTED') {
    return 'pending';
  } else if ($type === 'INVALIDATED') {
    return 'invalidated';
  } else {
    return '';
  }
}

function getBadgeText($fvg)
{
  $type = getFromArray($fvg, 'type');
  if ($type === 'ENTER') {
    return 'ENTER';
  } else if ($type === 'NEAR') {
    return 'NEAR';
  } else if ($type === 'DETECTED') {
    return 'aktiv';
  } else if ($type === 'INVALIDATED') {
    return 'invalidiert';
  } else {
    return '';
  }
}


function renderGroupSummary(&$group)
{
  global $limitRatio;
  foreach ($group['fvgs'] as $fvg) {
    $symbol = simpleSymbol($fvg['symbol']);
    $direction = getDirection($fvg);
    $class = getBadgeClass($fvg);
    $size = round1(getFromArrayFloat($fvg, 'size'));
    $ratio = getFromArrayFloat($fvg, 'ratio');
    $ratioStr = round1($ratio);
    if ($class !== 'pending' && $ratio < $limitRatio) {
      $class .= ' small';
    }
    echo "<span class=\"badge summary {$class}\">{$symbol} ({$size}) R {$ratioStr}{$direction}</span>";
  }
}

function renderGroupLines(&$group)
{
  foreach ($group['fvgs'] as $fvg) {
    $symbol = simpleSymbol($fvg['symbol']);
    $direction = getDirection($fvg);
    $class = getBadgeClass($fvg);
    $size = round1(getFromArrayFloat($fvg, 'size'));
    $ratio = getFromArrayFloat($fvg, 'ratio');
    $ratioStr = round1($ratio);
    $badgeText = getBadgeText($fvg);

    echo "<tr class=\"group_line_{$group['timestamp']}\" style=\"display: none;\">";
    echo "<td></td>";
    echo "<td>" . $symbol . "</td>";
    echo "<td>" . $direction . "</td>";
    echo "<td>" . $size . "</td>";
    echo "<td>" . $ratioStr . "</td>";
    echo "<td class=\"center\"><span class=\"badge {$class}\">{$badgeText}</span></td>";
    ;
    echo "</tr>";

  }
}

function inhalt()
{
  echo '<a class="main-link" href="index.php">← zurück zur Hauptseite</a>';

  echo "<span class=\"badge summary pending\">pending</span>";
  echo "<span class=\"badge summary near\">near</span>";
  echo "<span class=\"badge summary active\">active</span>";
  echo "<span class=\"badge summary invalidated\">invalidated/small</span>";

  // array(6) {
//     ["timestamp"]=>
//     int(1750748400000)
//     ["symbol"]=>
//     string(13) "HBARUSDT.PFVG"
//     ["dir"]=>
//     string(4) "bear"
//     ["type"]=>
//     string(4) "NEAR"
//     ["size"]=>
//     float(0.6)
//     ["ratio"]=>
//     float(0.6)
//   }


  echo '<table>';
  echo '<thead><tr>';
  echo '<th style="width: 120px">Zeit</th>';
  echo '<th>Name</th>';
  echo '<th></th>';
  echo '<th>Größe</th>';
  echo '<th>Ratio</th>';
  echo '<th>Status</th>';
  echo '</tr></thead>';
  echo '<tbody>';

  $fvgs = array_values(readFvgs());
  $fvgCount = count($fvgs);


  $fvgGroups = array();
  foreach ($fvgs as $fvg) {
    prepareFvgGroups($fvgGroups, $fvg);
  }

  uasort($fvgGroups, function ($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
  });


  for ($i = 0; $i < count($fvgGroups); $i++) {
    $group = $fvgGroups[$i];

    $showScript = ''
      . "document.querySelectorAll('.group_sumary_{$group['timestamp']}').forEach(e => e.style.display = 'none');"
      . "document.querySelectorAll('.group_line_{$group['timestamp']}').forEach(e => e.style.display = '');";
    $hideScript = ''
      . "document.querySelectorAll('.group_sumary_{$group['timestamp']}').forEach(e => e.style.display = '');"
      . "document.querySelectorAll('.group_line_{$group['timestamp']}').forEach(e => e.style.display = 'none');";

    echo "<tr class=\"clickable group_sumary_{$group['timestamp']}\" onclick=\"{$showScript}\">";
    echo "<td>" . timestampToString($group['timestamp']) . "</td>";
    echo '<td colspan="5">';
    renderGroupSummary($group);
    echo "</td>";
    echo '</tr>';

    echo "<tr class=\"clickable group_line_{$group['timestamp']}\" onclick=\"{$hideScript}\" style=\"display: none;\">";
    echo "<td>" . timestampToString($group['timestamp']) . "</td>";
    echo '<td colspan="5">';
    echo "</td>";
    echo '</tr>';
    renderGroupLines($group);

  }


  echo '</tbody></table>';
}

printPage('inhalt', true);