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
<div id="container">
    <?php
        $headerContent = '<h1>Airline Bookings Homepage</h1><h6>Fly cheap, fly safe</h6>';
        require_once 'Templates/Header.html.php';
    ?>
    <div id="content">
        <div class="container-fluid text-center">
            <h5>Remaining seats</h5>

            <div id="plane" class="container-fluid text-center">
                <div id="seats" class="text-center">
                    <?php
                        $row = 1;
                        echo '<table class="table"><tr>';

                        // print letters
                        $numOfCols = $model->getCols();
                        $numOfRows = $model->getRows();
                        $middleLetter = 'A' + $numOfCols/2;
                        $lastLetter = 'A' + $numOfCols-1;

                        echo '<td></td>';
                        for ($l = 'A'; $l <= $middleLetter; $l++)
                            echo '<td>'.$l.'</td>';
                        echo '<td> </td>';
                        for ( ; $l <= $numOfCols; $l++)
                            echo '<td>'.$l.'</td>';

                        // show all the seats
                        $nonFreeSeats = $model->getNonFreeSeats();

                        for ($n = 1; $n <= $numOfRows; $n++)
                        {
                            for ($l = 'A'; $l <= $lastLetter; $l++)
                            {

                                if ($l == 'A')
                                    echo '</tr><tr><td>'.($row++).'</td>';
                                else if ($l == $middleLetter+1)
                                    echo '<td> </td>';

                                $class = 'seat ' . ($nonFreeSeats[$l][$n] ?? '');

                                echo '<td><input type="checkbox" class="'.$class.'" /></td>';
                            }
                        }
                        echo '</tr></table>';
                    ?>
                </div>
            </div>
        </div>

        <!-- Login or Registration -->
        <div class="container-fluid text-center">
            <div class="col-md-6 p-3"></div>
            <div class="col-md-6 p-3"></div>
        </div>
    </div>
    <?php
        require_once 'Templates/Footer.html.php';
    ?>
</div>
</body>
</html>
