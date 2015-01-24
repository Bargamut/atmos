$(function() {
    $('.frmReset').live('click', function() { resetForm(); });
});

function resetForm() {
    $('#devices').find('select').val('-1');
    $('input[name="temperature"]').val('');
    $('#devices').submit();
}

/**
 * Запрос данных с датчиков контроллера
 *
 * @param ip - IP-адрес контроллера
 */
function getData(ip) {
    $.ajax({
        type: 'GET',
        url: 'http://' + ip + ':80/',
        timeout: 4000,
        success: function(data) {
            console.log(data);

            return $.parseJSON(data);
        },
        error: function(data) { console.log(data); }
    });
}

/**
 * reconnectTimeout,   // Тайм-аут переподключения
 * LIGHTNESS_MIN,      // настройки LDR
 * TEMPERATURE_MIN,    // настройки Barometer
 * HUMIDY_MIN,         // настройки для Влажности почвы
 * HUMIDY_MAX,
 * timeWait,           // интервал полива
 * timeWatering;       // длительность полива
 */
function setControllerOptions() {
    $.ajax({
        type: 'GET',
        url: 'http://' + ip + ':80/',
        data: 'mode=setting' + options,
        success: function(data) { console.log(data); },
        error: function(data) { console.log(data); }
    });
}

/**
 * Управление контроллером
 *
 * @param ip - IP-адрес контроллера
 */
function manipulation(ip) {
    $.ajax({
        type: 'GET',
        url: 'http://' + ip + ':80/',
        data: 'mode=manipulation' + options,
        success: function(data) { console.log(data); },
        error: function(data) { console.log(data); }
    });
}

/**
 * Поиск устройств в сети
 */
function searchDevices(ip) {
    for (var i = 1; i <= 254; i++) {
        $.ajax({
            type: 'GET',
            url: 'http://' + ip.replace(/\*/i, i) + ':80/',
            data: 'mode=searching',
            timeout: 4000,
            success: function (data) { console.log(data); },
            error: function (data) { console.log('err'); }
        });
    }
}