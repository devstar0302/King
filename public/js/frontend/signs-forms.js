$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//set view
$('.item.viewBlock').click(function () {
    var viewData = $(this).data('view');
    var newTitle = '';
    var newData = '';

    switch (viewData) {
        case 0:
            newTitle = 'List view';
            newData = 1;
            break;
        case 1:
            newTitle = 'Thumbnails view';
            newData = 0;
            break;
    }

    $(this).attr('title', newTitle);
    $(this).data('view', newData);
    $(this).toggleClass('list');

    $('.fileContent').fadeOut(200, function () {
        $(this).toggleClass('list').fadeIn('slow');
    })
});

//upload
$('.item.uploadBlock').click(function (e) {
    if (typeof e.buttons === 'undefined')
        return;
    $('#queue').html('');
    $('#uploadifive-file_upload input:nth-child(2)').click();
});

//sort
$('.item.sortBlock').click(function () {
    $('#sortPopup').addClass('show');
});

function sf_print() {
    if($('.print').hasClass('disabled-action')) {
        return
    }

    var mentionInputs = getSelectedInputs().mentionInputs;

    var target = mentionInputs[0];

    var type = $(target).data('type');

    var mywindow = window.open($(target).data('path'), 'Print', 'width=1200, height=600');
    mywindow.focus();

    $(mywindow).on('load', function () {
        window_printing = true;
        mywindow.print();

        if (type != 'pdf') {
            mywindow.close();
        }
    });

    var window_printing = false;
    mywindow.onfocus = function () {
        if (window_printing && type == 'pdf') {
            window_printing = false;
            mywindow.close();
        }
    }
}

function getSelectedInputs() {
    var allInputs = document.getElementsByClassName('mentioned-item');
    var mentionInputs = [];
    var mentionIds = [];

    for (var i = 0; i < allInputs.length; i++) {
        if (allInputs[i].checked) {
            mentionInputs.push(allInputs[i]);
            mentionIds.push(allInputs[i].getAttribute('data-id'));
        }
    }

    return {
        mentionInputs: mentionInputs,
        mentionIds: mentionIds
    }
}

//share
function sf_share(target) {
    if($('.share').hasClass('disabled-action')) {
        return
    }

    if (getSelectedInputs().mentionIds.length > 0) {
        $('.modal.sharing').addClass('show');
    } else {

    }
}

window.downloadFile = function (sUrl) {

    //iOS devices do not support downloading. We have to inform user about this.
    if (/(iP)/g.test(navigator.userAgent)) {
        //alert('Your device does not support files downloading. Please try again in desktop browser.');
        if (canOpenUrl === true) {
            window.open(sUrl, '_blank');
            return false;
        }
    }

    //If in Chrome or Safari - download via virtual link click
    if (window.downloadFile.isChrome || window.downloadFile.isSafari) {
        //Creating new link node.
        var link = document.createElement('a');
        link.href = sUrl;
        link.setAttribute('target', '_blank');

        if (link.download !== undefined) {
            //Set HTML5 download attribute. This will prevent file from opening if supported.
            var fileName = sUrl.substring(sUrl.lastIndexOf('/') + 1, sUrl.length);
            link.download = fileName;
        }

        //Dispatching click event.
        if (document.createEvent) {
            var e = document.createEvent('MouseEvents');
            e.initEvent('click', true, true);
            link.dispatchEvent(e);
            return true;
        }
    }

    // Force file download (whether supported by server).
    if (sUrl.indexOf('?') === -1) {
        sUrl += '?download';
    }

    window.open(sUrl, '_blank');
    return true;
}

window.downloadFile.isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
window.downloadFile.isSafari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;


function sf_download(target) {
    if($('.download').hasClass('disabled-action')) {
        return
    }

    var allInputs = document.getElementsByClassName('mentioned-item');

    for (var i = 0; i < allInputs.length; i++) {
        if (allInputs[i].checked) {
            downloadFile($(allInputs[i]).data('path'));
        }
    }
}

//delete
function sf_delete() {
    if($('.delete').hasClass('disabled-action')) {
        return
    }

    var mentionIds = getSelectedInputs().mentionIds;

    if (mentionIds.length > 0) {
        swal({
            title: Lang['Are you sure you want to delete it?'],
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: Lang['Yes'],
            cancelButtonText: Lang['Cancel(no)']
        }).then((result) => {
            if (result.value) {
                $('#loadingBlock').fadeIn(100);
                $.ajax({
                    type: "POST",
                    url: SF_DELET_ITEM_URL,
                    dataType: "html",
                    data: "_token=" + SF_TOKEN + "&ids=" + mentionIds,
                    success: function (response) {
                        window.location.reload();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        window.location.reload();
                    }
                });
            }
        });
        {

        }
    }
}

$('.close-button').click(function (e) {
    e.stopPropagation();
    $(this).closest('.modal').removeClass('show');
});

//copy share text
function sendFile() {
    var mentionIds = getSelectedInputs().mentionIds;

    var file_id = $('#fileShareId').val();

    if (mentionIds.length > 0) {
        file_id = mentionIds;
    }

    var to_address = $('#to_address').val();
    var message_subject = $('#message_subject').val();
    var message_body = $('#message_body').val();
    message_body = message_body.replace(/(\n|\r)/g, '<br>');

    if (to_address == '' || message_subject == '' || message_body == '')
        return;

    $('.modal.sharing').removeClass('show');

    var data = {
        _token: SF_TOKEN,
        fileId: file_id,
        mail: to_address,
        subjest: message_subject,
        message: message_body
    };

    $.ajax({
        url: SF_SEND_EMAIL_URL,
        method: "POST",
        data: data,
    }).done(function (data) {
        showAlert('success', Lang['Message sent successfully']);
    }).fail(function () {
        showAlert('success', Lang['Email was not sent']);
    });
};

$(document).mouseup(function (e) {
    var container = $(".modal");
    if (container.has(e.target).length === 0) {
        container.removeClass('show');
    }
});

$(function () {
    $('#file_upload').uploadifive({
        'auto': false,
        'queueSizeLimit': 5,
        'formData': {
            'timestamp': SF_TIMESTAMP,
            '_token': SF_TOKEN
        },
        'queueID': 'queue',
        'uploadScript': SF_UPLOAD_ITEM_URL,
        'onSelect': function () {
            setTimeout(() => {
                $('#file_upload').uploadifive('upload');
            }, 100);
        },
        'onUploadComplete': function (file, data) {
            $.ajax({
                type: "POST",
                url: SF_UPDATE_CONTENT_URL,
                dataType: "html",
                data: 'url=' + SF_CURRENT_URL + "&_token=" + SF_TOKEN,
                success: function (data) {
                    $('.fileContent').html(data);
                    setCheckboxClickListener();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    window.location.reload();
                }
            });
        }
    });
});

$('#sortingSelect').change(function () {
    var sorting = $(this).val();

    $('#sortPopup').removeClass('show');
    $('.item.sortBlock').find('span').text(sortTextArray[sorting]);

    var data = {
        _token: SF_TOKEN,
        sorting: sorting
    };

    $.ajax({
        type: "POST",
        url: SF_RESORTING_URL,
        dataType: "html",
        data: data,
        success: function (data) {
            $('.fileContent').html(data);
            setCheckboxClickListener();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // window.location.reload();
        }
    });
});

$(document).ready(function () {
    $('#search').keyup(function () {
        var filter = $(this).val();
        $(".fileContent .item").each(function () {
            if ($(this).find('.content').find('.name').text().search(new RegExp(filter, "i")) < 0) {
                $(this).fadeOut();
            } else {
                $(this).show();
            }
        });
    });
});

setCheckboxClickListener();
function setCheckboxClickListener() {
    $(".item-check").each(function (index) {
        $(this).on("change", function () {

            let inputs = getSelectedInputs().mentionIds;

            inputs.length ? $(".share").removeClass('disabled-action') : $(".share").addClass('disabled-action');
            inputs.length ? $(".delete").removeClass('disabled-action') : $(".delete").addClass('disabled-action');
            inputs.length ? $(".download").removeClass('disabled-action') : $(".download").addClass('disabled-action');
            inputs.length === 1 ? $(".print").removeClass('disabled-action') : $(".print").addClass('disabled-action');
        });
    });
}
