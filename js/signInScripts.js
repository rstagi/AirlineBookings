
function checkPasswordConfirm() {
    let password = document.getElementById("register-password");
    let passwordConf = document.getElementById("register-password-confirm");

    if(password.value != passwordConf.value) {
        passwordConf.setCustomValidity("Passwords Don't Match");
        return false;
    }

    passwordConf.setCustomValidity('');
    return true;
}
