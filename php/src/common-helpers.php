<?php

function readLinesOrEmpty(string $filepath): array
{
  return file_exists($filepath)
    ? file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    : [];
}
function writeLines(string $filepath, array $lines)
{
  file_put_contents($filepath, implode(PHP_EOL, $lines));
}

function getFromArray(array $array, $key)
{
  if (array_key_exists($key, $array)) {
    return $array[$key];
  }
  return null;
}

function getFromArrayInt(array $array, $key)
{
  $val = getFromArray($array, $key);
  if (is_numeric($val)){
    return (int) $val;
  }
  return null;
}

function getFromArrayFloat(array $array, $key)
{
  $val = getFromArray($array, $key);
  if (is_numeric($val)){
    return (float) $val;
  }
  return null;
}

?>