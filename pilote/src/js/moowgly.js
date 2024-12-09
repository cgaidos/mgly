function insert() {
    var param = new Object();
    param.email = 'vdurand@mail.com';
    url = window.location.protocol + '//' + window.location.host + '/moowgly/';

    $.ajax({
        type: 'POST',
        url: url,
        data: param
    })
    .done(function(data) {
//        alert('done');
    });
}


function update() {
    var param = new Object();
    param.id = $('#entete').data('id');
    param.email = 'gmarcel@courrier.com';
    url = window.location.protocol + '//' + window.location.host + '/moowgly/' + param.id;
//console.log(param);
//return;
    $.ajax({
        type: 'PUT',
        url: url,
        data: param
    })
    .done(function(data) {
//        alert('done');
    });
}

function del() {
    var param = new Object();
    param.id = $('#entete').data('id');
    url = window.location.protocol + '//' + window.location.host + '/moowgly/' + param.id;

    $.ajax({
        type: 'DELETE',
        url: url,
        data: param
    })
    .done(function(data) {
//        alert('done');
    });
}
