<?php
http_response_code(404);
$headers[] = 'refresh:5;url=./';
?>
<html>
<head>
    <?php
    $title = '404 Page not found';
    require_once 'Templates/Head.html.php';
    ?>
</head>
<body>
<div id="container">
    <?php
    $headerContent = '<h1>Error 404</h1><h5>Ooops, something went wrong</h5>';
    require_once 'Templates/Header.html.php';
    ?>
    <!--main content-->
    <div id="content">
        <div class="container-fluid text-center">
            <h3>404 Page not found</h3>
            <p>The requested page apparently does not exist!</p>
            <p>You should be redirected to the Homepage in 5 seconds. If it does not happen, please
                <a href="./">click here</a>.</p>
        </div>
    </div>

    <?php
    require_once 'Templates/Footer.html.php';
    ?>
</body>
</html>