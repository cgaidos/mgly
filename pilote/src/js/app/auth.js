console.log("auth load");

function registerUser($formRegisterUser) {
    var param = new Object();
    param.first_name = $formRegisterUser.find("input[name='first_name']").val();
    param.family_name = $formRegisterUser.find("input[name='last_name']").val();
    param.email = $formRegisterUser.find("input[name='email']").val();
    param.password = $formRegisterUser.find("input[name='password']").val();
    
    url = window.location.protocol + '//' + window.location.host + '/moowgly/registerUser';

    $.ajax({
        type: 'POST',
        url: url,
        data: param
    })
    .done(function(data) {

       console.log(data);
       var result = jQuery.parseJSON(data);

       if(result.validation == 'success'){
            $('<div class="alert ' + result.type + '"><strong>' + result.message + '</strong></div>').insertBefore('.info-user').delay(5000).fadeOut();
        }else{
            $('<div class="alert ' + result.type + '"><strong>' + result.message + '</strong></div>').insertBefore('.info-user').delay(5000).fadeOut();
        }

    });
}

function recoverPassword($formRecoverPassword) {
    var param = new Object();
    param.email = $formRecoverPassword.find("input[name='email']").val();
    
    url = window.location.protocol + '//' + window.location.host + '/moowgly/recoverPassword';

    $.ajax({
        type: 'POST',
        url: url,
        data: param
    })
    .done(function(data) {

       console.log(data);
       var result = jQuery.parseJSON(data);

       if(result.validation == 'success'){
            $('<div class="alert ' + result.type + '"><strong>' + result.message + '</strong></div>').insertBefore('.info-user').delay(5000).fadeOut();
        }else{
            $('<div class="alert ' + result.type + '"><strong>' + result.message + '</strong></div>').insertBefore('.info-user').delay(5000).fadeOut();
        }

    });
}

$( ".register-user" ).click(function(event) {
    event.preventDefault();
    registerUser($("#register-user-form"));
});

$( ".recover-password" ).click(function(event) {
    event.preventDefault();
    recoverPassword($("#recover-password-form"));
});
