<?php
if ($model->isUserLoggedIn()):
    $headers[] = 'refresh:0;url=./?page=Homepage';
else:
?>

<div class="row mt-3">
    <div class="col">
        <h2>Sign in to take advantage of the booking system!</h2>
    </div>
</div>
<div class="row">
    <!-- Login -->
    <div id="login" class="card p-3 col-lg-5 mx-lg-auto my-3 mx-3">
        <div class="card-body">
            <h5 class="card-title">Log in</h5>
            <h7 class="card-subtitle mb-3">Do you already have an account? Log in using the following form:</h7>
            <form class="async controller" controller="SignIn" action="login" redirectTo="./?page=PersonalPage">
                <div class="errorMessage alert alert-danger" role="alert" style="display: none">
                </div>
                <div class="form group my-2">
                    <input type="email" class="form-control" id="login-email" name="email" placeholder="Email" required />
                </div>
                <div class="form group my-2">
                    <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" required />
                </div>
                <button type="submit" id="login-btn" class="btn btn-primary my-3">Log in</button>
            </form>
        </div>
    </div>
    <!-- Registration -->
    <div id="registration" class="card p-3 col-lg-5 mx-lg-auto my-3 mx-3">
        <div class="card-body">
            <h5 class="card-title">Register</h5>
            <h7 class="card-subtitle mb-3">You're not registered yet? Use the following form to become a member:</h7>
            <form class="async controller" controller="SignIn" action="register" redirectTo="./?page=PersonalPage" checkBefore="checkPasswordConfirm">
                <div class="errorMessage alert alert-danger" role="alert" style="display: none">
                </div>
                <div class="form group my-2">
                    <input type="email" class="form-control" id="register-email" name="email" placeholder="Email" required />
                </div>
                <div class="form group my-2">
                    <input type="password" class="form-control" id="register-password" name="password" placeholder="Password"
                           pattern="(?=.*[a-z])(?=.*[A-Z0-9]).+" required
                           title="Must contain at least one lower-case alphabetic character and one
                           other character that is either alphabetical uppercase or numeric" />
                </div>
                <div class="form group my-2">
                    <input type="password" class="form-control" id="register-password-confirm" placeholder="Retype Password" required />
                </div>
                <button type="submit" id="register-btn" class="btn btn-primary my-3">Register</button>
            </form>
        </div>
    </div>

</div>


<script type="text/javascript">
    let password = document.getElementById("register-password");
    let passwordConf = document.getElementById("register-password-confirm");

    function checkPasswordConfirm() {
        if(password.value != passwordConf.value) {
            passwordConf.setCustomValidity("Passwords Don't Match");
            return false;
        }

        passwordConf.setCustomValidity('');
        return true;
    }

    password.onchange = checkPasswordConfirm;
    passwordConf.onkeyup = checkPasswordConfirm;
</script>

<?php endif; ?>