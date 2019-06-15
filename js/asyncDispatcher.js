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

            if ( options.onSuccess != null && options.onSuccess != undefined ) {
                if (options.idForCallback != null && options.idForCallback != undefined)
                    eval(options.onSuccess + '("' + options.idForCallback + '")');
                else
                    eval(options.onSuccess+'()');
            }
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

            if ( options.onFailure != null && options.onFailure != undefined ) {
                if (options.idForCallback != null && options.idForCallback != undefined)
                    eval(options.onFailure+'("'+options.idForCallback+'", '+xhr.status+')');
                else
                    eval(options.onFailure+'('+xhr.status+')');
            }
        }
    });
}


$('form.controller').submit(function (e) {

    e.preventDefault();

    let args = {};
    for (let element of e.currentTarget)
        if (element.localName != 'button' && element.name != undefined && element.name != null) {
            if (element.localName != 'checkbox' && element.localName != 'radio')
                args[element.name] = element.value;     // if it's a standard input, just take the value
            else if (element.checked) {
                if (element.localName == 'radio') {
                    args[element.name] = element.value;     // if it's a radio, take the value only if it's checked
                } else if ($(element).attr("list") == true && $(element).attr("groupName") != undefined && $(element).attr("groupName") != null) {
                    if (args[$(element).attr("groupName")] == undefined) // if it's a checkbox defined as a list, then push back its name in an array
                        args[$(element).attr("groupName")] = [element.name];
                    else
                        args[$(element).attr("groupName")].push(element.name);
                }
            }
        }

    let options = {
        checkCb: $(this).attr("checkBefore"),
        redirectTo: $(this).attr("redirectTo"),
        onSuccess: $(this).attr("onSuccess"),
        onFailure: $(this).attr("onFailure"),
        errorMessage: $(this).find('.errorMessage'),
        successMessage: $(this).find('.successMessage')
    };

    makeAsyncAction($(this).attr("controller"), $(this).attr("action"), args, options);

});

$('form.controller input:checkbox.triggerAction').change(function (event){

    let args = {
        seatId: $(this).attr("name")
    };

    let options = {
        checkCb: $(this).attr("checkBefore"),
        redirectTo: $(this).attr("redirectTo"),
        onSuccess: $(this).attr("onSuccess"),
        failureCb: $(this).attr("failureCallback"),
        onFailure: $(this).attr("onFailure"),
        errorMessage: $(this).find('.errorMessage'),
        successMessage: $(this).find('.successMessage'),
        idForCallback: $(this).attr("id")
    };

    makeAsyncAction($(event.currentTarget.form).attr("controller"), $(this).attr("action"), args, options);
});