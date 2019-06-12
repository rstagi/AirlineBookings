<?php
// TODO eventually init headers
?>
<html lang="en">
<head>
    <?php
    $title = 'Personal Page';
    require_once 'Templates/Head.html.php';
    ?>
</head>
<body>
<div id='container'>
    <?php
    $headerContent = '<h2>Personal Page</h2><h6>Here you can book some seats. You can also see your previously booked ones, if any.</h6>';
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
