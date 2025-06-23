<?php

function printPage(callable $renderContent, $wide=false)
{
  ?>
  <!DOCTYPE html>
  <html lang="de">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Fair Value Gang</title>
    <link rel="stylesheet"
          href="style.css">
  </head>

  <body>
    <div class="container<?php if ($wide){echo ' wide';} ?>">
      <?php $renderContent() ?>
      <footer>© 2025 Dogan Cinbir</footer>
    </div>
  </body>

  </html>
  <?php
}
?>