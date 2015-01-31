$(function() {
    $('#findController').on('click', function() { searchDevices('192.168.210.*'); });
    $(document).on('click', '.addController', function() { addControllers(); });
    $(document).on('click', '.delController img', function() { delControllers($(this)); });
    $(document).on('click', '.setController img', function() {
        makeFormSettings({
            id: $(this).attr('rel'),
            ip: $(this).parents('td').siblings().eq(0).text(),
            caption: $(this).parents('td').siblings().eq(1).text()
        });
    });
    $(document).on('click', '.commitSettings', function() { commitSettings($(this)); });
});