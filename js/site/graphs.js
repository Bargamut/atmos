var morrisObj = new Array(),
    morrisTemplate = {
        element: 'myfirstchart',    // ID of the element in which to draw the chart.
        // Chart data records -- each entry in this array corresponds to a point on the chart.
        data: [{ time: currentTime(), t1: 0.0, t2: 0.0, t3: 0.0, b: 0 }],
        xkey: 'time',               // The name of the data record attribute that contains x-values.
        ykeys: ['lightness', 'temperature', 'altitude', 'pressure', 'g_humidy'],  // A list of names of data record attributes that contain y-values.
        labels: ['Lightness', 'Temperature', 'Altitude', 'Pressure', 'Ground Humidy'],  // Labels for the ykeys -- will be displayed when you hover over the chart.
        lineColors: ["#0000ff", "#eea131", "#00ff00", "#aa0000", "#aa00ff"],
        axes: true,
        grid: true,
        fillOpacity: .5
    };

$(function() {
    var cDate = new Date();

    cDate.setHours(23);
    cDate.setMinutes(59);

    $('.date_till').datetimepicker({
        value: dateFormat(cDate) + ' ' + timeFormat(cDate),
        lang: 'ru',
        dayOfWeekStart: 1,
        format: 'Y-m-d H:i',
        formatDate: 'Y-m-d',
        formatTime: 'H:i',
        yearStart: 2000,
        yearEnd: cDate.getFullYear(),
        onChangeMonth: function(ct, $i) { this.setOptions({ value: ct.dateFormat('Y-m-d H:i') }); },
        onChangeYear: function(ct, $i) { this.setOptions({ value: ct.dateFormat('Y-m-d H:i') }); },
        onShow: function(ct, $i) {
            var dateSince = $('.date_since').val() ? $('.date_since').val().split(' ') : false;

            this.setOptions({
                minDate: dateSince[0],
                minTime: dateSince[1]
            });
        },
        onChangeDateTime: function(ct, $i) { $i.datetimepicker('hide'); }
    });

    cDate.setDate(1);
    cDate.setHours(0);
    cDate.setMinutes(0);

    $('.date_since').datetimepicker({
        value: dateFormat(cDate) + ' ' + timeFormat(cDate),
        lang: 'ru',
        dayOfWeekStart: 1,
        format: 'Y-m-d H:i',
        formatDate: 'Y-m-d',
        formatTime: 'H:i',
        yearStart: 2000,
        yearEnd: cDate.getFullYear(),
        onChangeMonth: function(ct, $i) { this.setOptions({ value: ct.dateFormat('Y-m-d H:i') }); },
        onChangeYear: function(ct, $i) { this.setOptions({ value: ct.dateFormat('Y-m-d H:i') }); },
        onShow: function(ct, $i) {
            var dateTill = $('.date_till').val() ? $('.date_till').val().split(' ') : false;

            this.setOptions({
                maxDate: dateTill[0],
                maxTime: dateTill[1]
            });
        },
        onChangeDateTime: function(ct, $i) { $i.datetimepicker('hide'); }
    });


    $('.graph').each(function(i, o) {
        getDatas($(o).attr('rel'), $(this).siblings('.date_since').val(), $(this).siblings('.date_till').val());
    });

    $(document).on('click', '.getDatas', function() {
        getDatas($(this).attr('rel'), $(this).siblings('.minDate').val(), $(this).siblings('.maxDate').val());
    });
});

function getDatas(controllerId, minDate, maxDate) {
    minDate = minDate || '0000-00-00 00:00';
    maxDate = maxDate || 'NOW()';

    var params = {
        type: 'getdat',
        cid: controllerId,
        min_date: minDate + ':00',
        max_date: maxDate == 'NOW()'? maxDate : (maxDate + ':59')
    };

    $.ajax({
        type: 'POST',
        url: '/work/do.php',
        data: params,
        timeout: 4000,
        complete: function (data) {
            var res     = $.parseJSON(data.responseText),
                result  = new Array();

            $.each(res.data, function(i, o) {
                result.push($.extend({}, { time: i }, o));
            });

            initGraph(controllerId, { element: 'area' + controllerId, data: result});
        }
    });
}

function initGraph(controllerId, settings) {
    var param = $.extend({}, morrisTemplate, settings);

    $('#area' + controllerId).html('');
    morrisObj['area' + controllerId] = new Morris.Line(param);
}