<!-- The sidebar -->
<nav class="sidebar bg-light">
    <a href="./">Home</a>
    <?php if(!$model->isUserLoggedIn()): ?>
    <a href="./?page=signin">Login</a>
    <a href="./?page=signin">Register</a>
    <?php else: ?>
        <a href="#about">Book</a>
        <a href="#about">Logout</a>
    <?php endif; ?>
</nav>