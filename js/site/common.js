$(function() {
    $('.frmReset').live('click', function() { resetForm(); });
});

function resetForm() {
    $('#devices').find('select').val('-1');
    $('input[name="temperature"]').val('');
    $('#devices').submit();
}

function getData() {
    $.ajax({
        type: 'GET',
        url: 'http://192.168.210.13:80/',
        success: function(data) { console.log(data); },
        error: function(data) { console.log(data); }
    });
}

function setData() {
    $.ajax({
        type: 'GET',
        url: 'http://192.168.210.13:80/',
        data: 'a=b&c=d&e=123',
        success: function(data) { console.log(data); },
        error: function(data) { console.log(data); }
    });
}

function searchDevices() {
    for (var i = 1; i <= 254; i++) {
        $.ajax({
            type: 'GET',
            url: 'http://192.168.210.' + i + ':80/',
            data: 'mode=searching&testing=1&man=watering&man=lighting&man=warming',
            timeout: 4000,
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log('err');
            }
        });
    }
}