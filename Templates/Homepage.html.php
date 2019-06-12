<?php
    // TODO eventually init headers
?>
<html lang="en">
<head>
<?php
    $title = 'Homepage';
    require_once 'Templates/Head.html.php';
?>
</head>
<body>
<div id='container'>
    <?php
        $headerContent = '<h1>Airline Bookings Homepage</h1><h6>Fly cheap, fly safe</h6>';
        require_once 'Templates/Header.html.php';
    ?>
    <div id='content'>
        <div class='container-fluid'>
            <h5>Remaining seats:</h5>

            <img src='Images/plane.png' class='plane' />
        </div>
    </div>
    <?php
        require_once 'Templates/Footer.html.php';
    ?>
</div>
</body>
</html>
