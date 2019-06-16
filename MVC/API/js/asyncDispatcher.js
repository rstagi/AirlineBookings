/**
 * Script to manage all dispatcher calls.
 * They are triggered by form submits and checkbox changes and then delivered to the asyncDispatcher.php,
 * or to the normal dispatcher if async call is not what it's needed (action and args should be adjusted before submit)
 */


/**
 * when the form is submitted, the args are re-adjusted. If it's an async call, makeAsyncAction is called.
 * Otherwise, a temporary form is created and submitted.
 */
$('form.controller').submit(function (e) {

    e.preventDefault();

    let args = {};
    for (let element of e.currentTarget)
        if (element.localName != 'button' && element.name != undefined && element.name != null) {
            if (element.type != 'checkbox' && element.type != 'radio')
                args[element.name] = element.value;     // if it's a standard input, just take the value
            else if (element.checked) {
                if(args[element.name.slice(0, -2)] == undefined)
                    args[element.name.slice(0, -2)] = [];
                args[element.name.slice(0, -2)].push(element.value);     // if it's a radio or a checkbox, take the value only if it's checked
            }
        }


    if($(this).hasClass('async')) {

        makeAsyncAction($(this).attr("controller"), $(this).attr("action"), args,
            {
                checkCb: $(this).attr("checkBefore"),
                redirectTo: $(this).attr("redirectTo"),
                onSuccess: $(this).attr("onSuccess"),
                onFailure: $(this).attr("onFailure")
            });
    }
    else
    {
        let controller = $(this).attr("controller");
        let action = $(this).attr("action");
        let argsStr = JSON.stringify(args);
        $('#inset-form').html(' <form name="deliverAction" action="./?page='+controller+'" method="post">' +
            '<input type="text" name="action" value="'+action+'" />' +
            '<input type="text" name="args" value=\''+argsStr+'\' />' +
            '                       </form>').hide();
        document.forms['deliverAction'].submit();
    }

});


/**
 * when checkboxes change, an asynchronous call is made
 */
$('form.controller input:checkbox.asyncTrigger').change(function (event){

    let args = {
        seatId: this.value
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



/**
 * Async call to the dispatcher
 * @param controller
 * @param action
 * @param args
 * @param options
 */
function makeAsyncAction (controller, action, args, options) {

    // check-before callback
    if (options.checkCb != null && options.checkCb != undefined && !eval(options.checkCb+"()"))
        return;

    // request parameters
    let req_params =    "controller="+controller+
                        "&action="+action+
                        "&args="+JSON.stringify(args);

    $.ajax({
        contentType: 'application/x-www-form-urlencoded',
        method: 'POST',
        url: 'MVC/API/asyncDispatcher.php',
        data: req_params,
        success: function (payload) {
            if (options.redirectTo != null && options.redirectTo != undefined)
                window.location.replace(options.redirectTo);

            if (options.onSuccess != null && options.onSuccess != undefined) {
                if (options.idForCallback != null && options.idForCallback != undefined)
                    eval(options.onSuccess + '("' + options.idForCallback + '")');
                else
                    eval(options.onSuccess + '()');
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

            if (options.onFailure != null && options.onFailure != undefined) {
                if (options.idForCallback != null && options.idForCallback != undefined)
                    eval(options.onFailure + '("' + options.idForCallback + '", ' + xhr.status + ')');
                else
                    eval(options.onFailure + '(' + xhr.status + ')');
            }
        }
    });
}

