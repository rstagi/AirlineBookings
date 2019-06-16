<html lang="en">
<head>
    <title>Airline Bookings - <?= ($title ?? "") ?></title>

    <!-- Meta tags  for Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Boostrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Project CSS -->
    <link rel="stylesheet" href="styles/style.css">
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
        <div id="content" class="text-center">
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