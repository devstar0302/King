$(document).ready(function() {

    $('#duplicate_guidance').click(function() {
        var data = {
            _token: TOKEN,
            guidance_id: GUIDANCE_ID
        }
    
        $.ajax({
            url: DUPLICATE_GUIDANCE_URL,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (result) {
                location.href = BASE_URL + '/malfunctions/guidance/'+result.guidance_id+'/edit';
            }
        });
    });

    $('#download_pdf').click(function() {

        $('#app nav').css('display', 'none');

        var width = $(document).width();
        var height = $(document).height();

        for(var i = 1; width > height, i <= 100; i++) {
            if(height * i >= width) {
                height = height * i; break;
            }
        }

        html2pdf(document.getElementById('pdf-container'), {
            margin:       1,
            filename:     'guidance_'+ (new Date().getTime()) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { dpi: 192, letterRendering: true },
            jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
            callback: function(pdf, filename) {
                pdf.save(filename);
            }
        });

        $('#app nav').css('display', 'flex');        
    });

    $('#share_pdf').click(function() {
        $('.modal.sharing').addClass('show');
    });

    $('#print_pdf').click(function() {
        if(!window_focus) return;
        
        $('#printingScorinig').remove();
        $("#printing-holder").append("<div id='printingScorinig' style='background-color: white; padding: 20px;'></div>");
        $(".guidances").clone().appendTo('#printingScorinig');

        var content = document.getElementById('printingScorinig').innerHTML;
        var mywindow = window.open('', 'Print', 'width=800, height=600');
    
        mywindow.document.innerHTML = '';
        mywindow.document.write('<html>' +
                                    '<head>' +
                                        '<title>Print</title>' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/app.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/custom.css" rel="stylesheet" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/main.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/show.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/print.css" media="all">' +
                                        (LOCALE == 'he' ? '<link rel="stylesheet"href="' + BASE_URL + '/css/malfunctions/main_rtl.css" media="all">' : '') +
                                    '</head>' +
                                    '<body >' +
                                        '<div class="container">' +
                                            '<div class="row justify-content-center">');
        mywindow.document.write(content);

        mywindow.document.write('</div></div></body></html>');
    
        mywindow.document.close();
        mywindow.focus()

        setTimeout(() => {
            mywindow.print();
            mywindow.close();
    
            $('#printingScorinig').remove();
        }, 300);
    });

    updateReportDate();
});

function sendFile() {
    var to_address = $('#to_address').val();
    var message_subject = $('#message_subject').val();
    var message_body = $('#message_body').val();
    message_body = message_body.replace(/(\n|\r)/g, '<br>');

    if(to_address == '' || message_subject == '' || message_body == '')
        return;

    $('.modal.sharing').removeClass('show');

    $('#app nav').css('display', 'none');

    var width = $(document).width();
    var height = $(document).height();

    for(var i = 1; width > height, i <= 100; i++) {
        if(height * i >= width) {
            height = height * i; break;
        }
    }

    html2pdf(document.documentElement, {
        margin:       1,
        filename:     'guidance_'+ (new Date().getTime()) + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { dpi: 192, letterRendering: true },
        jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
        callback: function(pdf, filename) {
            var formData = new FormData();
            formData.append('_token', TOKEN);
            formData.append('guidance_pdf', pdf.output('blob'));
            formData.append('to', to_address);
            formData.append('subject', message_subject);
            formData.append('body', message_body);
        
            $.ajax({
                url: SEND_PDF_URL,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
            }).done(function(data) {
                showAlert('success', Lang['Message sent successfully']);
            }).fail(function() {
                showAlert('success', Lang['Email was not sent']);
            });
        }
    });

    $('#app nav').css('display', 'flex');
}

$(".comment-editor").keyup(function(e) {
    if(e.keyCode != 13) 
        return;

    var uuid = Math.random().toString(36).substr(2, 9);
    var guidance_item = $(this).closest(".guidances-item");
    var principal_id = guidance_item.data('principal-id');
    var comment = $(this).val();
    var role = user.title.toLowerCase();

    $(guidance_item).find('.comments').append(
        `<div class="comment-item" data-comment-id="${uuid}">
            <input type="hidden" name="data[comments][${principal_id}][${uuid}][user_id]" value="${user.id}">
            <input type="hidden" name="data[comments][${principal_id}][${uuid}][user_role]" value="${user.title}">
            <input type="hidden" name="data[comments][${principal_id}][${uuid}][user_name]" value="${user.name}">
            <span class="user-name ${role}">${user.name}</span>
            <input class="comment-value" name="data[comments][${principal_id}][${uuid}][value]" value="${comment}" readonly>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">...
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" class="btn_edit" onclick="editComment('${principal_id}', '${uuid}')">${Lang['Edit']}</a></li>
                    <li><a href="javascript:void(0);" class="btn_delete" onclick="deleteComment('${principal_id}', '${uuid}')">${Lang['Delete']}</a></li>
                </ul>
            </div>
        </div>`
    );

    saveComments(principal_id);
    $(this).val('');
});

function saveComments(principal_id) {
    var form_data = $('.comments[data-principal-id='+principal_id+']').serialize();

    $.ajax({
        url: SAVE_COMMENTS_URL,
        data: form_data,
        method: 'POST',
        success: function (data) {
        }
    });
}

var save_timer = 0;
$('.comment-value').on('keyup', function(e) {
    if(e.keyCode == 13) {
        $(this).attr('readonly', '');
    }

    var principal_id = $(this).closest('.comments').data('principal-id');
    
    clearTimeout(save_timer);
    save_timer = setTimeout(function () {
        saveComments(principal_id);
    }, 500);
});

$('.comments').submit(function(e) {
    e.preventDefault();
    e.stopPropagation();    

    $('.comment-value').attr('readonly', '');
});

function editComment(principal_id, comment_id) {
    var comment_editor = $('.comments[data-principal-id='+principal_id+'] .comment-item[data-comment-id='+comment_id+'] .comment-value');

    $(comment_editor).focus();
    $(comment_editor).removeAttr('readonly');
    $(comment_editor).blur(function() {
        $(comment_editor).attr('readonly', '');
    });
}

function deleteComment(principal_id, comment_id) {
    var comment_item = $('.comments[data-principal-id='+principal_id+'] .comment-item[data-comment-id='+comment_id+']');

    if(comment_item.length) {
        $(comment_item).remove();
        saveComments(principal_id);
    }
}

function updateReportDate(selected_date = "undefined") {
    if(selected_date == 'undefined') {
        selected_date = REPORT_DATE;
    }
    date = selected_date.split('-');
    if(date.length == 3) {
        selected_date = new Date((2000 + parseInt(date[2])) + '-' + date[1] + '-' + date[0]);
    } else {
        selected_date = new Date();
    }

    var dd = selected_date.getDate();
    var mm = selected_date.getMonth() + 1; //January is 0!

    var yyyy = selected_date.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    var date = (dd + '/' + mm + '/' + yyyy);

    $('.report-date').text(date);
}

function deleteGuidance() {
    swal({
        title: Lang['Are you sure you want to delete it?'],
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: Lang['Yes'],
        cancelButtonText: Lang['Cancel(no)'],
    }).then((result) => {
        if (result.value) {
            window.location.href = DELETE_GUIDANCE_URL;
        }
    });
}
