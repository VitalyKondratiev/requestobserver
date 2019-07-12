$(document).ready(function () {
    var socket = io(socket_address);
    socket.on('result', function (data) {
        response = JSON.parse(decodeURIComponent(data));
        var postHtml = JSON.parse(response.post.POST);
        $('#requestCounter').text(parseInt($('#requestCounter').text()) + 1);
        $('#requestContainer').append(
            '<div class="panel panel-default request-entry"><div class="panel-heading">' +
            response.page +
            ' (IP:' +
            response.ip +
            '; Время: ' +
            dtf.format(new Date(response.time), 'DD.NN.Y hh:mm:ss') +
            ')</div><div class="panel-body request-data-area">' +
            postHtml +
            '</div></div>'
        );
        $('.kint-popup-trigger').remove();
    });
    socket.on('users_count', function (data) {
        $('#userCounter').text(data);
    });
    $("#requestContainer").bind("DOMSubtreeModified", function () {
        $('#requestCleanButton').attr('disabled', !parseInt($('#requestCounter').text()));
    });
    $('#requestCleanButton').on('click', function () {
        if (!confirm('Вы действительно хотите очистить список запросов?' +
            '\r\nОтменить данное действие не возможно.')) return true;
        $('#requestCounter').text(0);
        $('#requestContainer').empty();
    });
    $('#changeSecret').on('click', function () {
        return confirm('Вы действительно хотите сменить ваш секретный ключ?' +
            '\r\nЭто обновит страницу, и очистит список запросов.');
    });
    $('#copySecret').on('click', function () {
        var secretKey = document.location.search.replace('?','')
            .split('&')
            .filter(x => x.split('=')[0] == 'secret_key')[0]
            .split('=')[1];
        var $copyInput = $("<input>");
        $("body").append($copyInput);
        $copyInput.val(secretKey).select();
        document.execCommand("copy");
        $copyInput.remove();
    });
});

