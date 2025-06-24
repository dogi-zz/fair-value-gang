<?php

require_once 'common-helpers.php';

$fvgFilePath = 'fvgs.txt';
$alarmLogPath = 'alarmlog.txt';
$alarmLogLimit = 1000;

// ----------------------------------------------------------------------------

function simpleSymbol(string $input): string
{
  $base = explode('.', $input)[0];
  $suffixes = ['USDT', 'USD'];
  foreach ($suffixes as $suffix) {
    if (str_ends_with($base, $suffix)) {
      // explizit abschneiden, nicht per substr
      return substr($base, 0, strlen($base) - strlen($suffix));
    }
  }
  return $base;
}

function replaceSymbol(string $input)
{
  $parts = explode(" ", $input, 2);
  if (count($parts) === 1) {
    return $input;
  }
  $parts[0] = simpleSymbol($parts[0]);
  return implode(" ", $parts);
}

// ----------------------------------------------------------------------------


function analyseBracket(string $symbol, string $bracket)
{
  global $fvgFilePath;
  $doSave = false;
  $result = null;

  $bracketContent = [];
  foreach (explode(",", $bracket) as $part) {
    $split = explode(":", $part, 2);
    if (count($split) === 2) {
      $bracketContent[$split[0]] = $split[1];
    }
  }
  if (array_key_exists('ratio', $bracketContent)) {
    if (strpos($bracketContent['ratio'], '1:') === 0) {
      $bracketContent['ratio'] = substr($bracketContent['ratio'], strlen('1:'));
    }
  }

  $type = getFromArray($bracketContent, 'type');
  $timestamp = getFromArrayInt($bracketContent, 'ts');

  // Minimal-Anforderung: [timestamp, type]
  if ($type === null || $timestamp === null) {
    return null;
  }

  $entry = [
    'timestamp' => $timestamp,
    'symbol' => $symbol,
    'dir' => getFromArray($bracketContent, 'dir')
  ];

  // Zusätzliche Felder je nach Typ
  if ($type === 'FVG-DETECTED') {
    $entry['type'] = 'DETECTED';
    $entry['size'] = getFromArrayFloat($bracketContent, 'size');
    if ($entry['size'] !== null) {
      $doSave = true;
      $result = false;
    }
  }

  if ($type === 'FVG-ENTER') {
    $entry['type'] = 'ENTER';
    $entry['size'] = getFromArrayFloat($bracketContent, 'size');
    $entry['ratio'] = getFromArrayFloat($bracketContent, 'ratio');
    if ($entry['size'] !== null && $entry['ratio'] !== null) {
      $doSave = true;
      $result = "$symbol FVG ENTER size:" . $entry['size'] . " ratio:" . $entry['ratio'];
    }
  }

  if ($type === 'FVG-NEAR') {
    $entry['type'] = 'NEAR';
    $entry['size'] = getFromArrayFloat($bracketContent, 'size');
    $entry['ratio'] = getFromArrayFloat($bracketContent, 'ratio');
    if ($entry['size'] !== null && $entry['ratio'] !== null) {
      $doSave = true;
      $result = "$symbol FVG near size:" . $entry['size'] . " ratio:" . $entry['ratio'];
    }
  }

  if ($type === 'FVG-INVALIDATED') {
    $entry['type'] = 'INVALIDATED';
    $doSave = true;
    $result = false;
  }

  if ($doSave) {
    // Zwei Wochen in Millisekunden
    $now = round(microtime(true) * 1000);
    $twoWeeksAgo = $now - (14 * 24 * 60 * 60 * 1000);

    $fvgLines = readLinesOrEmpty($fvgFilePath);
    $newLines = [];
    foreach ($fvgLines as $line) {
      $data = json_decode($line, true);

      // Nur behalten, wenn gültiger timestamp und nicht älter als 2 Wochen
      if (isset($data['timestamp']) && is_numeric($data['timestamp']) && $data['timestamp'] >= $twoWeeksAgo) {
        $newLines[] = json_encode($data, JSON_UNESCAPED_SLASHES);
      }
    }

    $newLines[] = json_encode($entry, JSON_UNESCAPED_SLASHES);
    writeLines($fvgFilePath, $newLines);
  }

  return $result;
}

function analyseAlarm(string $input)
{
  global $alarmLogPath, $alarmLogLimit;

  // Alarm ins Log eintargen
  $logLines = readLinesOrEmpty($alarmLogPath);
  array_unshift($logLines, $input);
  if (count($logLines) > $alarmLogLimit) {
    $logLines = array_slice($logLines, 0, $alarmLogLimit);
  }
  writeLines($alarmLogPath, $logLines);

  // Prüfen, ob Klammerinhalt existiert
  $tagStart = strpos($input, '[');
  $tagEnd = strpos($input, ']');
  $symbol = explode(" ", $input, 2)[0];

  if ($tagStart === false || $tagEnd === false || $tagEnd <= $tagStart) {
    return replaceSymbol($input); // Kein Klammerinhalt vorhanden – unverändert zurück
  }

  // Inhalt extrahieren
  $bracketContent = substr($input, $tagStart + 1, $tagEnd - $tagStart - 1);
  $analyseBracketResult = analyseBracket($symbol, $bracketContent);
  if ($analyseBracketResult === false) {
    return null;
  }
  $trimmedText = trim(substr($input, 0, $tagStart));
  if (is_string($analyseBracketResult)) {
    $trimmedText = $analyseBracketResult;
  }

  // Gekürzten Text zurückgeben
  return replaceSymbol($trimmedText);
}


// ============================================================================


function readFvgs(): array
{
  global $fvgFilePath;
  if (!file_exists($fvgFilePath)) {
    return [];
  }

  $lines = readLinesOrEmpty($fvgFilePath);
  $result = [];

  foreach ($lines as $line) {
    $data = json_decode($line, true);

    if (!is_array($data) || !isset($data['timestamp'], $data['symbol'])) {
      continue; // Ungültig oder unvollständig
    }

    $key = $data['timestamp'] . '|' . $data['symbol'];

    // Überschreibt früheres mit gleichem key
    $result[$key] = $data;
  }

  // Absteigend nach timestamp sortieren
  uasort($result, function ($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
  });

  return $result;
}

?>