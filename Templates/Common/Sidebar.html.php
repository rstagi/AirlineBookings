<!-- The sidebar -->
<nav class="sidebar bg-light">
    <a href="./">Home</a>
    <?php
    if(!\Utils\AirlineBookingsUtils::isNonEmpty($model) || !$model->isUserLoggedIn()): ?>
    <a href="./?page=SignIn">Login</a>
    <a href="./?page=SignIn">Register</a>
    <?php else: ?>
    <a href="./?page=PersonalPage">Book</a>
    <a href="./?page=Homepage&action=logout">Logout</a>
    <?php endif;?>
</nav>