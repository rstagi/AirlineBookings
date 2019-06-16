<?php
$headers[] = 'refresh:5;url=./?page=SignIn';
$headers[] = 'HTTP/1.1 401 Unauthorized';
?>

<div class="container-fluid text-center">
    <h3>401 Unauthorized</h3>
    <p>You cannot access the requested page. Please, log in and try again.</p>
    <p>You should be redirected to the Login page in 5 seconds. If it does not happen, please
        <a href="./?page=SignIn">click here</a>.</p>
</div>