$(function() {
    $(document).on('click', '.cancel', function() { $.unblockUI(); });
});

/**
 * Поиск устройств в сети
 */
function searchDevices(ip) {
    var controllers = new Array(), j = 0;

    $.blockUI({
        message: '<img src="/img/default/loading.gif" /> Поиск контроллеров в сети...',
        css: {
            cursor: 'default',
            minHeight: '70px',
            padding: '0px 0px 15px'
        },
        onBlock: function() {
            for (var n = 1; n <= 254; n++) {
                $.ajax({
                    type: 'GET',
                    url: 'http://' + ip.replace(/\*/i, n) + ':80/',
                    data: 'mode=searching',
                    timeout: 4000,
                    complete: function (data) {
                        if (typeof(data.responseText) != 'undefined') {
                            var have    = true,
                                p       = $.parseJSON(data.responseText);

                            $('td:eq(1)', '.controllersList tbody tr').each(function(i, o) {
                                if (p.device_ip == $(o).text()) { have = false; }
                            });

                            if (have && typeof(p.device_ip) != 'undefined') { controllers.push(p); }
                        }

                        j++;

                        if (j == 254) {
                            if (controllers.length > 0) { makeForm4AddControllers(controllers); }
                            else {
                                $('.blockMsg').html('Новых контроллеров не найдено!');
                                setTimeout('$.unblockUI()', 3000);
                            }
                        }
                    },
                    error: function (data) { }
                });
            }
        }
    });
}

function makeForm4AddControllers(controllers) {
    var resHtml = '<h1>Найденные контроллеры</h1>' +
        '<table class="controllersList addControllersForm">' +
            '<thead><tr><th>IP</th><th>Название</th><th>Описание</th></tr></thead>' +
            '<tbody>';

    $.each(controllers, function(i, o) {
        resHtml += '<tr><td>' + o.device_ip + '</td><td><input class="caption' + o.device_ip.replace(/\./g, '') + '" type="text" value="" /></td><td><textarea class="description' + o.device_ip.replace(/\./g, '') + '"></textarea></td></tr>';
    });

    resHtml += '</tbody></table>' +
        '<input class="addController" type="button" value="Добавить" />' +
        '<input class="cancel" type="button" value="Отмена" />';

    $('.blockMsg').html(resHtml);
}

function addControllers() {
    var c = {};

    $('td:first', '.addControllersForm tbody tr').each(function(i, o) {
        var ip = $(o).text();

        c[ip] = {
            caption: $('.caption' + ip.replace(/\./g, '')).val(),
            description: $('.description' + ip.replace(/\./g, '')).val()
        }
    });

    $.ajax({
        type: 'POST',
        url: '/work/do.php',
        data: 'type=add&data=' + encodeURIComponent(JSON.stringify(c)),
        complete: function (data) {
            var res = $.parseJSON(data.responseText);

            if (res.status == 'ok') {
                $('.blockMsg').html('<h1>Подключение завершено!</h1>');
                setTimeout('$.unblockUI()', 2000);
            }

            if (res.status == 'error') {
                var t = '';

                $.each(res.data, function(i, o) {
                    t += '<div><b>' + i + '</b>: ' + o.caption + '</div>';
                });

                $('.blockMsg').html('<h1>Ошибка подключения</h1>' + t + '<input class="cancel" type="button" value="Закрыть" />');
            }
        }
    });
}

function delControllers(obj) {
    $.blockUI({
        message: '<img src="/img/default/loading.gif" /> Удаление данных контроллера из базы...',
        css: {
            cursor: 'default',
            minHeight: '70px',
            padding: '0px 0px 15px'
        },
        onBlock: function () {
            $.ajax({
                type: 'POST',
                url: '/work/do.php',
                data: 'type=del&cid=' + obj.attr('rel'),
                complete: function (data) {
                    var res = $.parseJSON(data.responseText);

                    if (res.status == 'ok') {
                        $('.blockMsg').html('<h1>Удаление завершено!</h1>');
                        setTimeout('$.unblockUI()', 2000);
                        if (obj.parents('tr').siblings() > 1) {
                            obj.parents('tr').remove();
                        } else {
                            obj.parents('tr').html('<td colspan="5">Контроллеры не установлены</td>');
                        }
                    }

                    if (res.status == 'error') {
                        $('.blockMsg').html('<h1>Ошибка удаления</h1><input class="cancel" type="button" value="Закрыть" />');
                    }
                }
            });
        }
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
 * Запрос данных с датчиков контроллера
 *
 * @param ip - IP-адрес контроллера
 */
function getData(ip) {
    $.ajax({
        type: 'GET',
        url: 'http://' + ip + ':80/',
        timeout: 4000,
        complete: function(data) {
            console.log(data);

            return $.parseJSON(data);
        },
        error: function(data) { console.log(data); }
    });
}