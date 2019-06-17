<html lang="en">
<head>
    <title>Airline Bookings - <?= ($title ?? "") ?></title>

    <!-- Meta tags  for Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Boostrap JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- Project CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- check if Javascript is enabled -->
<noscript><p> You must have Javascript enabled in order to use this website. Please, enable it and then refresh this page. </p>
    <meta HTTP-EQUIV="refresh" content=0;url="./?page=noJavascript"></noscript>

<!-- page content -->
<div id="container">

    <?php
    require_once 'Templates/Layout/Header.html.php';
    ?>

    <div class="row w-100">
    <?php
    require_once 'Templates/Layout/Sidebar.html.php';
    ?>

    <div id="content-wrapper" class="w-100">
        <div id="content" class="text-center w-100">
            <?php
            require_once $template;
            ?>
        </div>

        <div class="content-footer">
            <?php
            require 'Templates/Layout/Footer.html.php';
            ?>
        </div>
    </div>
    </div>
</div>

</body>
</html>