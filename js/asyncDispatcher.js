$('form.controller').submit(function (e) {

    e.preventDefault();

    let checkCb = $(this).attr("checkBefore");
    if (checkCb != null && checkCb != undefined)
    {
        if (!eval(checkCb+"()")) return;
    }

    let args = {};
    for (let element of e.currentTarget)
        if (element.localName != 'button' && element.name != undefined && element.name != null)
            args[element.name] = element.value;

    let req_params =    "controller="+$(this).attr("controller")+
                        "&action="+$(this).attr("action")+
                        "&args="+JSON.stringify(args);

    let redirectTo = $(this).attr("redirectTo");
    let successCb = $(this).attr("success");
    let failureCb = $(this).attr("failure");
    let errorMessage = $(this).find('.errorMessage');

    $.ajax({
        contentType: 'application/x-www-form-urlencoded',
        method: 'POST',
        url: 'asyncDispatcher.php',
        data: req_params,
        success: function (payload) {
            if ( redirectTo != null && redirectTo != undefined )
                window.location.replace(redirectTo);
            $(errorMessage).hide();
            if ( successCb != null && successCb != undefined )
                successCb();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            let error = "";
            if (xhr.status === 401)
                window.location.replace("./?page=401Unauthorized");
            else if (xhr.status === 500)
                error = "Internal Server Error";
            else
                error = xhr.responseText;

            $(errorMessage).html(error).show();

            if ( failureCb != null && failureCb != undefined )
                failureCb();
        }
    });

});

$('.controller input[type="checkbox"].triggerAction').change(function (event){

});