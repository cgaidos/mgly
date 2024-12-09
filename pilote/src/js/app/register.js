console.log("register load");

function who() {
    var param = new Object();
    param.who = 'family';
    url = window.location.protocol + '//' + window.location.host + '/moowgly/become-host_start';

    $.ajax({
        type: 'POST',
        url: url,
        data: param
    })
    .done(function(data) {

        var result = jQuery.parseJSON(data);

        if(result.validation == 'success'){
            location.href=window.location.protocol + '//' + window.location.host + '/moowgly/become-host_housing?point=1';
        }else{
            alert('error');
        }

    });
}

$( ".btn-become-who" ).click(function(event) {
    event.preventDefault();
    who();
});