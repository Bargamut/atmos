$(function() {
    $('#findController').on('click', function() { searchDevices('192.168.210.*'); });
    $(document).on('click', '.addController', function() { addControllers(); });
    $(document).on('click', '.delController img', function() { delControllers($(this)); });
});