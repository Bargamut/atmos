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
    //morrisObj = new Morris.Line(morrisTemplate);

    $('.graph').each(function(i, o) {
        getDatas($(o).attr('rel'), $(this).siblings('.minDate').val(), $(this).siblings('.maxDate').val());
    });

    $(document).on('click', '.getDatas', function() {
        getDatas($(this).attr('rel'), $(this).siblings('.minDate').val(), $(this).siblings('.maxDate').val());
    });
});

function getDatas(controllerId, minDate, maxDate) {
    minDate = minDate || '0000-00-00 00:00:00';
    maxDate = maxDate || 'NOW()';

    var params = {
        type: 'getdat',
        cid: controllerId,
        min_date: minDate,
        max_date: maxDate
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