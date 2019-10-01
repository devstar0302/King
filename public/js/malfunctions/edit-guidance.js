var original_data = new Array();
var NOTHINIG = 0, REDIRECT_TO_FORMLIST_PAGE = 1;
$(document).ready(function () {
    
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        window.prettyPrint && prettyPrint();

        $('#datepicker').datepicker({
            format: 'dd-mm-yy',
            language: LOCALE
        }).on('changeDate', function(e) {
            $("input[name='data[date]']").val(this.value)
            updateReportDate(this.value);
            saveGuidanceForm();
        });

        $('#time').datetimepicker({
            format: 'HH:mm',
        })

        today();
        currentTime();
        saveGuidanceForm();

        $('.guidance-principal.hidden, .guidance-list.hidden, .guidance-summary.hidden').removeClass('hidden');
        if ($('.guidance-principal-item .summernote-textarea').length > 0) {
            $('#guidance-principal-items').sortable({
                handle: ".guidance-sort",
                update: saveGuidanceForm
            })
        }
    });

    $('#site').on('change', function () {
        $("input[name='data[subsite]']").val('');
        if($(this).val() == 'site') {
            $('#subsite').attr('disabled',true);
            $("input[name='data[site]']").val('');
            $("input[name='data[company_representative]']").val('');
            saveGuidanceForm();
            updateSendToAdminButton();
        }
        else {
            $("input[name='data[site]']").val($("option:selected", this).text());

            $.ajax({
                url: FILTER_SITE_URL,
                type: 'post',
                data: {id: $(this).val()},
                success: function (data) {
                    $('#subsite').attr('disabled',false);
                    $('#subsite').html('');
                    $('#subsite').prepend('<option  value="subsite">' + Lang['Sub-site'] + '</option>');
                    for (var i = 0; i < data.subsites.length; i++) {
                        $('#subsite').append("<option value='" + data.subsites[i].id + "'>" + data.subsites[i].title + "</option>");
                    }
                    $("input[name='data[company_representative]']").val(data.repres[0] ? data.repres[0].representative : "");
                    saveGuidanceForm();
                },
                complete: function() {
                    updateSendToAdminButton();
                }
            });
        }
    });

    $('#subsite').on('change', function () {
        $("input[name='data[subsite]']").val($("option:selected", this).text());

        $.ajax({
            url: FILTER_SUBSITE_URL,
            type: 'post',
            data: {id: $(this).val()},
            async: false,
            success: function (data) {
                if(data.repres){
                    $("input[name='data[company_representative]']").val(data.repres);
                }
                saveGuidanceForm();
            },
            complete: function() {
                updateSendToAdminButton();
            }
        });
    });

    $(document).on('click', '.note-btn-group.btn-group.note-color .note-color:last-child .dropdown-menu .note-palette:first-child button.note-color-btn', function () {
        $('.note-btn-group.btn-group.note-color .note-color:first-child .dropdown-menu .note-palette:first-child button.note-color-btn[data-value="' + $(this).data('value') + '"').click();
    })

/*
    toolbar: [
    ['style', ['fontname', 'fontsize', 'color', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
    ['Paragraph style', ['style', 'ol', 'ul', 'paragraph', 'height']],
    ['Insert', ['link', 'video', 'table', 'hr']],
    ['Misc', ['fullscreen', 'codeview', 'undo', 'redo', 'help']],
    ['popovers', ['lfm']]
    ],
    */

    $('.summernote-textarea').summernote({
        toolbar: [
            ['para', ['ol', 'numListStyles', 'ul', 'bullListStyles', 'listStyles', 'paragraph']],
            ['style', ['bold', 'underline', 'italic']],
            ['color', ['color', 'color']],
            ['Misc', [ 'undo', 'redo']],
        ],
        callbacks: {
            onChange: delay(function (e, contents) {
                var newHtml = $(this).summernote('code');
                if(user.title.toLowerCase() == 'admin') {
                    newHtml = getHtmlDiff(getOriginalHtml($(this).attr("uuid")), newHtml);
                    if(newHtml.indexOf("<span class='inserted'>") != -1 || newHtml.indexOf("<span class='deleted'>") != -1) {
                        $('input[name="data[status][admin_changed_date]"]').val("changed");
                    }
                }
                $(this).find('*').remove();
                var children = $(newHtml).find('*');
                for(var i = 0; i < children.length; i ++) {
                    var element = children.eq(i);
                    if( !element[0].classList.contains || !element[0].classList.contains
                        || element.text().replace(/(\s|\r|\n)*/g,"") != "") {
                        $(this).append(element);
                    }
                }
                saveGuidanceForm();
            }, 500),
            onInit: function(){
                $("button.note-btn.btn.btn-light.btn-sm.list-styles").removeClass("dropdown-toggle")
            }
        }
    });

    $.each($('.summernote-textarea.disabled'), function (index, element) {
        $(element).summernote('disable');
    });

    $(document).on('click', '.guidance-principal-photo-remove, .guidance-uploads-item-remove', function () {
        $(this).parent().remove();
        saveGuidanceForm();
    })

    $('body').on('change', '.guidance-upload-image', function () {
        var that = this;
        var file_data = $(this).prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);

        $(this).closest(".guidance-photos").prepend("<div class='guidance-principal-photo-item'><span class='uploadProgressBar'></span></div>")
        $.ajax({
            url: UPLOAD_FILE_URL,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (name) {
                var is_image = isImage(name);
                var path = BASE_URL + (is_image ? "/images/" : "/uploads/");
                var principal_id = $(that).closest('.guidance-principal-item').data('id');

                $(that).closest(".guidance-photos").prepend(
                    '<div class="guidance-principal-photo-item">' +
                        '<a href="' + path + name + '" target="_blank">' +
                            (is_image ? '<img src="' + path + name + '" class="guidance-principal-photo-item" alt="' + name + '"/>' : (name.length > 20 ? name.substr(0, 20) : name)) +
                        '</a>' +
                        '<i class="guidance-principal-photo-remove"></i>' +
                        '<input type="hidden" name="data[photo][' + principal_id + '][]" value="' + name + '"/>' +
                    '</div>'
                );

                saveGuidanceForm();
            },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener( "progress", uploadProgressHandler, false );
                xhr.addEventListener("load", loadHandler, false);
                return xhr;
            }
        });
    });

    $('.contractor_representative').on('change', function () {
        saveGuidanceForm();
    })

    $(document).on('change', '.warn', function () {
        $(this).removeClass('warn');
    })

    updateReportDate();
});

function isImage(name = ""){
    name = name.split(".");
    var extention = name[name.length - 1];//name.last()
    return ($.inArray(extention.toLowerCase(), ["jpg", "jpeg", "bmp", "gif", "png"]) !== -1);
}

function saveGuidanceForm(after_saved = NOTHINIG) {
    var form_data = $('#guidance-form').serialize();
    $.ajax({
        url: UPDATE_URL,
        data: form_data,
        method: 'POST',
        success: function (data) {
            switch(after_saved) {
            case REDIRECT_TO_FORMLIST_PAGE:
                location.href = FORM_LIST_URL;
                break;
            case NOTHINIG:
                break;
            }
        }
    });
}

function uploadProgressHandler(event)
{
    // $("#loaded_n_total").html("Uploaded "+event.loaded+" bytes of "+event.total);
    var percent = (event.loaded / event.total) * 100;
    var progress = Math.round(percent);
    $(".uploadProgressBar").css("width", progress + "%");
}

function loadHandler(event)
{
    $(".uploadProgressBar").parent().remove();
}

function updateTime(target){
    $("input[name='data[time_]']").val($(target).val())
    saveGuidanceForm();
}

function uploadFiles(target){
    var that = this,
        files = $(this).prop("file").files,
        form_data = new FormData(),
        is_image = false;
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        form_data.append('uploads[]', file, file.name);
    }

    $.ajax({
        url: UPLOAD_FILES_URL,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function (data) {
            $.each(JSON.parse(data), function(key, value){
                is_image = isImage(value);
                $("#guidance-uploaded-documents").prepend(
                    //guidance-principal-photo-item" style="background-image: url('${data}');"
                    `<div class="guidance-uploads-item">` +
                        `<a href="${BASE_URL}/uploads/${value}" target="_blank" class="guidance-uploads-text">` + (is_image ? ('<img class="guidance-principal-photo-item" src="' + (BASE_URL + "/uploads/" + value) + '" alt="'+ value + '"/>') : (value.length > 20 ? value.substr(0, 20) : value)) + `</a>` +
                        `<i class="guidance-uploads-item-remove"></i>
                        <input type="hidden" name="data[guidance-uploads][]" value="${value}"/>
                     </div>`
                 );

            })
            saveGuidanceForm();
        }
    });
}

function getTodayDate(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return (dd + '/' + mm + '/' + yyyy);
}
function currentTime() {
    if (!$("#time").val()) {
        $('#time').val(moment().format("HH:mm"));
        $("input[name='data[time_]']").val($('#time').val())
    }
}
function today() {
    var d = new Date(),
        curr_date = d.getDate(),
        curr_month = d.getMonth() + 1,
        curr_year = d.getFullYear().toString().substr(-2);
    if(!$('#datepicker').val()){
        $('#datepicker').val(curr_date + "-" + curr_month + "-" + curr_year);
        $("input[name='data[date]']").val( $('#datepicker').val())
    }
}

$(document).ready(function() {

    $('#add-principal-guidance').on('click', function() {
        var uuid = Math.random().toString(36).substr(2, 9);
        $('#guidance-principal-items').append(
        '<div class="guidance-principal-item" data-id="'+ uuid +'">' + 
            '<div class="guidance-sort"></div>' +
            '<textarea name="data[guidance_principal_detail]['+ uuid +'][value]" class="summernote-textarea"></textarea>' +
            '<input type="hidden" name="data[guidance_principal_detail]['+ uuid +'][id]" value="'+ uuid +'" />' + 
            '<div class="guidance-photos">' +
                '<div class="upload-photo-wrapper">' +
                '<label class="guidance-add-photo" for="add-image-'+ uuid +'">' +
                        '<i class="fa fa-upload"></i>' + 
                        '<input id="add-image-'+ uuid +'" class="guidance-upload-image" type="file">' +
                    '</label>' + 
                '</div>' +
            '</div>' +
            '<div class="cb"></div>' +
        '</div>'
        );

        $('.guidance-principal-item[data-id='+ uuid +'] .summernote-textarea').summernote({
            toolbar: [
                ['para', ['ol', 'numListStyles', 'ul', 'bullListStyles', 'listStyles', 'paragraph']],
                ['style', ['bold', 'underline', 'italic']],
                ['color', ['color', 'color']],
                ['Misc', [ 'undo', 'redo']],
            ],
            callbacks: {
                onChange: delay(function (e, contents) {
                    var newHtml = $(this).summernote('code');
                    if(user.title.toLowerCase() == 'admin') {
                        newHtml = getHtmlDiff(getOriginalHtml($(this).attr("uuid")), newHtml);
                        if(newHtml.indexOf("<span class='inserted'>") != -1 || newHtml.indexOf("<span class='deleted'>") != -1) {
                            $('input[name="data[status][admin_changed_date]"]').val("changed");
                        }
                    }
                    $(this).find('*').remove();
                    var children = $(newHtml).find('*');
                    for(var i = 0; i < children.length; i ++) {
                        var element = children.eq(i);
                        if( !element[0].classList.contains || !element[0].classList.contains
                            || element.text().replace(/(\s|\r|\n)*/g,"") != "") {
                            $(this).append(element);
                        }
                    }
                    saveGuidanceForm();
                }, 500),
                onInit: function(){
                    $("button.note-btn.btn.btn-light.btn-sm.list-styles").removeClass("dropdown-toggle")
                }
            }
        });

        updateOriginalData();

        $('#guidance-principal-items').sortable({
            handle: ".guidance-sort",
            update: saveGuidanceForm
        })
    });

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

        html2pdf(document.documentElement, {
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
        var mywindow = window.open('', 'Print', 'width=1200, height=600');
    
        mywindow.document.innerHTML = '';
        mywindow.document.write('<html>' +
                                    '<head>' +
                                        '<title>Print</title>' +
                                        '<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/multiselect.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/app.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/custom.css" rel="stylesheet" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/main.css" media="all">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/edit.css" media="all">' +
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
        }, 500);
    });

    $(document).on('change', '.warn', function () {
        $(this).removeClass('warn');
    });

    $('#send_to_admin').click(function() {
        var is_error = false;
        if($('#site').val() == 'site') {
            is_error = true;
            $('#site').addClass('warn');
        }

        if($('#subsite option').length >= 2 && $('#subsite').val() == 'subsite') {
            is_error = true;
            $('#subsite').addClass('warn');
        }
    
        if(is_error) {
            $('#guidance-error-msg').html(Lang['SomeFieldsAreMissiing']);
            return;
        }
    
        $('#guidance-error-msg').html('');

        swal({
            title: Lang['Are you sure?(send to admin)'],
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: Lang['Yes'],
            cancelButtonText: Lang['No'],
        }).then((result) => {
            if (result.value) {
                $("input[name='data[status][stage]']").val('pending');
                saveGuidanceForm(REDIRECT_TO_FORMLIST_PAGE);
            }
        });
    });

    updateOriginalData();
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

    var scoring_pdf = null, other_pdf = null;
    html2pdf(document.documentElement, {
        margin:       1,
        filename:     'statistics_scoring'+ (new Date().getTime()) + '.pdf',
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

function sendToAdmin() {
    $("input[name='data[status][stage]']").val('pending');

    saveGuidanceForm(REDIRECT_TO_FORMLIST_PAGE);   
}

function getOriginalHtml(uuid) {
    var org_data = '';
    $.each(original_data, function( index, value ) {
        if(value.uuid == uuid) {
            org_data = value.data;
        }
    });

    return org_data;
}

function updateOriginalData() {
    $.each($('.summernote-textarea'), function(index, element) {
        var uuid = $(element).attr("uuid");
        if(typeof uuid === "undefined" || uuid === false) {
            uuid = Math.random().toString(36).substr(2, 9);
            $(element).attr("uuid", uuid);    
            original_data.push({"uuid": uuid, data: $(element).summernote('code')});
        }
    });
}

function updateReportDate(selected_date = "undefined") {
    if(selected_date == 'undefined') {
        selected_date = $('#datepicker').val();
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

function uploadFromSignsForms() {
    $('.subview-container').addClass('show');
    $('.signs_forms .fileContent .item-check').prop('checked', false);
}

$('.subview-container').on('click', function() {
    $('.subview-container').removeClass('show');
});

$('.subview-container').on('keyup', function(e) {
    if(e.keycode == 27) {
        $('.subview-container').removeClass('show');
    }
});

$('.signs_forms').on('click', function(e) {
    e.stopPropagation();
});

$('.modal').on('click', function(e) {
    e.stopPropagation();
});

function uploadCheckedFiles() {
    $('.subview-container').removeClass('show');

    var file_ids = [];
    $.each($("input.item-check:checked"), function(){            
        file_ids.push($(this).data('id'));                
    });

    if(file_ids.length == 0)
        return;

    var data = {
        _token: SF_TOKEN,
        ids: file_ids
    };

    $.ajax({
        url: SF_UPLOAD_FILES_URL,
        dataType: 'text',
        data: data,
        type: 'post',
        success: function (data) {
            $.each(JSON.parse(data), function(key, value){
                is_image = isImage(value);
                $("#guidance-uploaded-documents").prepend(
                    //guidance-principal-photo-item" style="background-image: url('${data}');"
                    `<div class="guidance-uploads-item">` +
                        `<a href="${BASE_URL}/uploads/${value}" target="_blank" class="guidance-uploads-text">` + (is_image ? ('<img class="guidance-principal-photo-item" src="' + (BASE_URL + "/uploads/" + value) + '" alt="' + value + '"/>') : (value.length > 20 ? value.substr(0, 20) : value)) + `</a>` +
                        `<i class="guidance-uploads-item-remove"></i>
                        <input type="hidden" name="data[guidance-uploads][]" value="${value}"/>
                    </div>`
                );
            })
            saveGuidanceForm();
        }
    });
}

$('#upload_from_computer').click(function(e) {
    $('#file').click();
});

function updateSendToAdminButton() {
    var is_error = $('#site').val() == 'site';
    is_error |= $('#subsite option').length >= 2 && $('#subsite').val() == 'subsite';

    if(!is_error) {
        $('#guidance-error-msg').html('');
    }
}
