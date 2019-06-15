function makeAsyncAction (controller, action, args, options) {

    if (options.checkCb != null && checkCb != undefined && !eval(checkCb+"()"))
        return;

    let req_params =    "controller="+controller+
                        "&action="+action+
                        "&args="+JSON.stringify(args);

    $.ajax({
        contentType: 'application/x-www-form-urlencoded',
        method: 'POST',
        url: 'asyncDispatcher.php',
        data: req_params,
        success: function (payload) {
            if ( options.redirectTo != null && options.redirectTo != undefined )
                window.location.replace(options.redirectTo);

            if (options.errorMessage != undefined && options.errorMessage != null)
                $(options.errorMessage).hide();


            if (options.successMessage != undefined && options.successMessage != null)
                $(options.successMessage).html(payload).show();

            if ( options.successCb != null && options.successCb != undefined )
                options.successCb();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            let error = "";
            if (xhr.status === 401)
                window.location.replace("./?page=401Unauthorized");
            else if (xhr.status === 500)
                error = "Internal Server Error";
            else
                error = xhr.responseText;

            if (options.successMessage != undefined && options.successMessage != null)
                $(options.successMessage).hide();

            if (options.errorMessage != undefined && options.errorMessage != null)
                $(options.errorMessage).html(error).show();

            if ( options.failureCb != null && options.failureCb != undefined )
                options.failureCb(xhr.status);
        }
    });
}


$('form.controller').submit(function (e) {

    e.preventDefault();

    let args = {};
    for (let element of e.currentTarget)
        if (element.localName != 'button' && element.name != undefined && element.name != null)
            args[element.name] = element.value;

    let options = {
        checkCb: $(this).attr("checkBefore"),
        redirectTo: $(this).attr("redirectTo"),
        successCb: $(this).attr("success"),
        failureCb: $(this).attr("failure"),
        errorMessage: $(this).find('.errorMessage'),
        successMessage: $(this).find('.successMessage')
    };

    makeAsyncAction($(this).attr("controller"), $(this).attr("action"), args, options);

});

$('form.controller input:checkbox.triggerAction').change(function (event){

    console.log(event);
    
    let options = {
        checkCb: $(this).attr("checkBefore"),
        redirectTo: $(this).attr("redirectTo"),
        successCb: $(this).attr("success"),
        failureCb: $(this).attr("failure"),
        errorMessage: $(this).find('.errorMessage'),
        successMessage: $(this).find('.successMessage')
    };

    makeAsyncAction($(event.currentTarget.form).attr("controller"), $(this).attr("action"), [], options);
});