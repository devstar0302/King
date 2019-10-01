function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

function showAlert(cls, msg) {
    $('#alert').addClass(cls);
    $('#alert').addClass('show');
    $('#alert .message').text(msg);

    setTimeout(function() {
        $('#alert').removeClass(cls);
        $('#alert').removeClass('show');
        $('#alert .message').text('');
    }, 2000);
}

function getFormatDate(date) {
    return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
}

var window_focus = true;
window.onblur = function() { window_focus = false; }
window.onfocus = function() { window_focus = true; }

function getHtmlDiff(originalHtml, newHtml) {
    var result = htmldiff(originalHtml, newHtml);
    for (var i = 0; i < result.length; i++) {
        var before_length = result.length;

        result = result.replace("<ins>", "<span class='inserted'>");
        result = result.replace("</ins>", "</span>");
        result = result.replace("<del>", "<span class='deleted'>");
        result = result.replace("</del>", "</span>");

        if(before_length == result.length) break;
    }
    return result;
}

function clearTrialMark(canvas, fill_color, container_height) {
    var context = canvas.getContext('2d');
    context.fillStyle = fill_color;
    if(canvas.height >= container_height) {
        context.fillRect(0, container_height - 20, 100, 20);
    } else {
        context.fillRect(0, canvas.height - 20, 100, 20);
    }
}

function date_diff_indays(date1, date2) {
    dt1 = new Date(date1);
    dt2 = new Date(date2);

    return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
}