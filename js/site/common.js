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
    $.ajax({
        type: 'GET',
        url: 'http://192.168.210.13:80/',
        data: 'searching',
        success: function(data) { console.log(data); },
        error: function(data) { console.log(data); }
    });
}