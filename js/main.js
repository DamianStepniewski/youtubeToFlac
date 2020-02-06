$(document).ready(function() {
    var socket;
    if (window.WebSocket) {
        socket = new WebSocket('wss://snikk.pl/ws_snikk');

        socket.onopen = function(e) {
            $('.btn').addClass('btn-active').removeClass('btn-inactive');
            $('#msg-container').html('');
        };

        socket.onmessage = function(e) {
            var json = JSON.parse(e.data);

            if (json.app === "YT") {
                if (json.action === "transcodingProgress") {
                    if (json.progress > 0) {
                        $('#progress-container').slideDown(250);
                    }
                    $('#progress-inner').width($('#progress-bar').width() * json.progress/100);
                    $('#msg-container').html(json.estimatedTime);
                } else
                if (json.action === 'result') {
                    if (json.status === "ERROR") {
                        $('#msg-container').html('');
                        $('#error-container').html(json.message);
                        setTimeout(function() {
                            $('#error-container').html('');
                        }, 5000);
                    } else {
                        $('#progress-inner').width($('#progress-bar').width());
                        $('#msg-container').html('');
                        window.location = 'inc/handler.php?action=download&filename='+json.filename;
                    }

                    $('.btn').removeClass('btn-inactive').addClass('btn-active');
                    $('#link').attr('disabled', false);
                    $('#progress-container').slideUp(250);
                }
            }
        };

        socket.onerror = function(e) {
            $('#error-container').html('An error has occured (WebSocket connection failed).');
        };

        socket.onclose = function(e) {
            $('.btn').removeClass('btn-active').addClass('btn-inactive');
            $('#error-container').html('An error has occured (WebSocket has been closed unexpectedly).');
        }
    } else {
        $('#error-container').html('Your browser doesn\'t support WebSockets, which is required for this website to work.');
    }

    $('input').focus(function(){
        $(this).parents('.form-group').addClass('focused');
    });

    $('input').blur(function(){
        var inputValue = $(this).val();
        if ( inputValue == "" ) {
            $(this).removeClass('filled');
            $(this).parents('.form-group').removeClass('focused');
        } else {
            $(this).addClass('filled');
        }
    })

    $('.btn').click(function() {
        if ($('#link').val().length > 0 && $(this).hasClass('btn-active')) {
            $('#msg-container').html('Preparing video, please wait...');
            $('.btn').removeClass('btn-active').addClass('btn-inactive');
            $('#progress-inner').width(0);
            $('#link').attr('disabled', true);

            socket.send(JSON.stringify({
                app: 'YT',
                action: 'transcode',
                url: $('#link').val(),
                output: $(this).data('type')
            }));
        }
    });
});