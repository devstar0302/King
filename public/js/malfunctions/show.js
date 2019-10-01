var chart = null;
$(document).ready(function() {
    $('.category').on('click', function () {
        $(this).next().toggle();
    });
    
    chart = new CanvasJS.Chart("chartContainer", {
        theme: "light2",
        animationEnabled: true,
        axisX: {
            title: Lang['Date'],
            interval: 1,
            intervalType: "week",
            labelAngle: -50,
            labelFontSize: 16,
            valueFormatString: "DD-MM-YYYY",
            reversed: true
        },
        axisY2:{
            title: Lang['Score'],
            labelFontSize: 16,
            valueFormatString: "#0",
        },
        data: [{
            type: "line",
            markerSize: 12,
            xValueFormatString: "DD-MM-YYYY",
            yValueFormatString: "###.#",
            dataPoints: []
        }]
    });
    chart.render();
    clearTrialMark($('.canvasjs-chart-canvas')[0], '#fff', chart.height);

    $('#duplicate_malfunction').click(function() {
        var data = {
            _token: TOKEN,
            malfunction_id: MALFUNCTION_ID
        }
    
        $.ajax({
            url: DUPLICATE_MALFUCNTION_URL,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (result) {
                location.href = BASE_URL + '/malfunctions/'+result.malfunction_id+'/edit';
            }
        });
    });

    $('#download_pdf').click(function() {
        
        $('#app nav').css('display', 'none');
        $('.malfunctions-item.other').css('display', 'none');
        $('.hide-in-pdf').css('display', 'none');
        $('.show-in-pdf').css('display', 'block');

        var width = $(document).width();
        var height = $(document).height();

        console.log(width);
        console.log(height);

        for(var i = 1; width > height, i <= 10; i++) {
            if(height * i > width) {

                height = height * i; 
                break;
            }
        }

        $('#chartContainer').find('.canvasjs-chart-container').addClass('print_style');
        $('table').addClass('table_style');

        html2pdf(document.getElementById('pdf-container'), {
            margin:       1,
            // filename:     'ציונים' + '.pdf',
            // filename: $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) ' + 'דוח בדיקה במערך המזון ב' + ' ) ' + ' - ( ' + $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + ' ) ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + ' ) ' + '.pdf',
            filename: 'דוח בדיקה במערך המזון ב' + ' ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) - ( ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + ' ( ' + $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { dpi: 192, letterRendering: true },
            jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
            callback: function(pdf, filename) {
                pdf.save(filename);

                $('#app nav').css('display', 'none');
                $('.malfunctions-item.scoring').css('display', 'none');
                $('.hide-in-pdf').css('display', 'none');
                $('.show-in-pdf').css('display', 'block');

                var width = $(document).outerWidth();
                var height = $(document).outerHeight();
        
                console.log(width);
                console.log(height);
        
                for(var i = 1; width > height, i <= 10; i++) {
                    if(height * i > width) {
                        height = height * i; break;
                    }
                }
        
                html2pdf(document.getElementById('pdf-container'), {
                    margin:       1,
                    // filename:     'מבדק' + '.pdf',
                    // filename: 'דוח בדיקה במערך המזון ב' + ' ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) ' + ' - ( ' + $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + ' ) ' + ' מיום ' + ' ( ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6) + ' )' + '.pdf',
                    filename: $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + ' ) ' + 'מבדק ניהול סיכונים במערך המזון ב' + ' ) ' + ' - ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + '.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { dpi: 192, letterRendering: true },
                    jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' }
                });
        
                $('.malfunctions-item.scoring').css('display', 'block');
                $('#app nav').css('display', 'flex');
                $('.hide-in-pdf').css('display', 'block');
                $('.show-in-pdf').css('display', 'none');
            }
        });

        $('.malfunctions-item.other').css('display', 'block');
        $('#app nav').css('display', 'flex');
        $('.hide-in-pdf').css('display', 'block');
        $('.show-in-pdf').css('display', 'none');

        $('#chartContainer').find('.canvasjs-chart-container').removeClass('print_style');
        $('table').removeClass('table_style');
    });

    $('#share_pdf').click(function() {
        $('.modal.sharing').addClass('show');
    });

    $('#print_pdf').click(function() {
        if(!window_focus) return;
        
        $('#printingScorinig').remove();
        $("#printing-holder").append("<div id='printingScorinig' style='background-color: white; padding: 20px;'></div>");
        $(".malfunctions-item.scoring").clone().appendTo('#printingScorinig');

        var content = document.getElementById('printingScorinig').innerHTML;
        var mywindow = window.open('', 'Print', 'width=800, height=600');
    
        mywindow.document.innerHTML = '';
        mywindow.document.write('<html>' +
                                    '<head>' +
                                        '<title>'+ Lang['Print'] +'</title>' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/app.css" media="print">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/custom.css" rel="stylesheet" media="print">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/main.css" media="print">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/show.css" media="print">' +
                                        '<link rel="stylesheet" href="' + BASE_URL + '/css/malfunctions/print.css" media="print">' +
                                        (LOCALE == 'he' ? '<link rel="stylesheet"href="' + BASE_URL + '/css/malfunctions/main_rtl.css" media="all">' : '') +
                                    '</head>' +
                                    '<body >' +
                                        '<div class="container">' +
                                            '<div class="row justify-content-center">' +
                                                '<div class="col-md-12 card malfunctions" style="text-align:center;">');
        mywindow.document.write(content);

        var context = mywindow.document.getElementsByClassName('canvasjs-chart-canvas')[0].getContext('2d');
        var oldCanvas = $('.card-body.details .canvasjs-chart-canvas')[0];
        context.drawImage(oldCanvas, 0, 0);

        mywindow.document.write('</div></div></div></body></html>');
    
        mywindow.document.close();
        mywindow.focus()

        setTimeout(() => {
            mywindow.print();
            mywindow.close();
    
            $('#printingScorinig').remove();
        }, 300);
    });

    reloadStatistics();
    updateLangText();
    updateReportDate();

    $('ul.malfunctions-principal li.malfunctions-item').each(function(){
        $(this).find('p:eq(0)').html('<span style="border: 1px solid black; padding-left: 3px; padding-right: 3px; margin-right: 3px;">X</span> ' + ' <span style="text-decoration: underline;">' + $(this).find('p:eq(0)').text() + '</span>');
        $(this).find('div:eq(0)').html('<span style="">' + ($(this).find('div:eq(0)').text().substring(1)) + '</span>');
        $(this).find('div:eq(0)').css('padding-right', '25px');
    });

});

function reloadStatistics() {
    var sites = [];
    sites.push({site: SITE, subsite: SUBSITE});

    var start_date = new Date(DATE), end_date = new Date(DATE);
    start_date = new Date(start_date.setMonth(start_date.getMonth() - 6));

    var data = {
        _token: TOKEN,
        sites: sites,
        start_date: getFormatDate(start_date),
        end_date: getFormatDate(end_date),
        statistic_type: 'Total score'
    }

    $.ajax({
        url: GET_STATISTICS_URL,
        type: 'get',
        dataType: 'json',
        data: data,
        success: function (result) {
            updateChart(result);
        }
    });
}

function updateChart(result) {
    if(chart == null) return;

    var sites = [], data = [], max = 0, start_date = null, end_date = null;
    for(var i = 0; result.data != undefined && i < result.data.length; i++) {
        var site_data = result.data[i];

        var tempDataPoints = [], dataPoints = [];
        for(var j = 0; j < site_data.data.length; j++) {
            var date = new Date(site_data.data[j].date);
            date.setHours(0); date.setMinutes(0); date.setSeconds(0);
            tempDataPoints.push({ x: date, y: parseFloat(site_data.data[j].score), indexLabel: site_data.data[j].score, markerType: "retangle",  markerColor: "#6B8E23" });
        }
        
        tempDataPoints.sort( function(a,b){ return new Date(b.x) - new Date(a.x); } );
        
        for(var j = 0; j < tempDataPoints.length;) {
            dataPoints.push(tempDataPoints[j]);
            
            if(start_date == null && end_date == null) {
                start_date = new Date(tempDataPoints[j].x); 
                end_date = new Date(tempDataPoints[j].x); 
            }

            if(start_date - new Date(tempDataPoints[j].x) > 0) {
                start_date = new Date(tempDataPoints[j].x);
            }

            if(new Date(tempDataPoints[j].x - end_date) > 0) {
                end_date = new Date(tempDataPoints[j].x);
            }

            var scores = []; 
            scores.push(tempDataPoints[j].y);
            for(var k = j + 1; k < tempDataPoints.length; k++) {
                if(dataPoints[dataPoints.length - 1].x - tempDataPoints[k].x == 0) {
                    scores.push(tempDataPoints[k].y);
                }
            }

            var score = 0;
            for(var k = 0; k < scores.length; k++) {
                score += $.isNumeric(scores[k]) ? scores[k] : 0;
            }
            score = score / scores.length;

            if(max < score) max = score;

            dataPoints[dataPoints.length - 1].y = score;
            dataPoints[dataPoints.length - 1].indexLabel = score.toFixed(2).toString();

            j += scores.length;
        }

        data.push({
            type: "line",
            markerSize: 12,
            lineThickness: 4,
            xValueFormatString: "YYYY-MM-DD",
            yValueFormatString: "###.#",
            dataPoints: dataPoints,
            axisYType: "secondary"
        });
    }

    for(var i = 0; chart.data != undefined && i < chart.data.length; i++) {
        chart.data[i].remove();
    }

    if(start_date != null && end_date != null) {
        var week_date = new Date(start_date.getTime() + 7 * 24 * 60 * 60 * 1000);
        chart.options.axisX.intervalType = (week_date - end_date >= 0) ? "day" : "week";
    }

    chart.options.data = data;
    chart.options.axisY2.interval = max ? max / 6 : undefined;
    chart.options.axisY2.labelFormatter = function ( e ) {
        return e.value.toFixed(2);
    }  

    chart.render();
    clearTrialMark($('.canvasjs-chart-canvas')[0], '#fff', chart.height);
}

function sendFile() {
    var to_address = $('#to_address').val();
    var message_subject = $('#message_subject').val();
    var message_body = $('#message_body').val();
    message_body = message_body.replace(/(\n|\r)/g, '<br>');

    if(to_address == '' || message_subject == '' || message_body == '')
        return;

    $('.modal.sharing').removeClass('show');

    $('#app nav').css('display', 'none');
    $('.malfunctions-item.other').css('display', 'none');

    var width = $(document).width();
    var height = $(document).height();

    for(var i = 1; width > height, i <= 10; i++) {
        if(height * i > width) {
            height = height * i; break;
        }
    }

    var scoring_pdf = null, other_pdf = null;
    html2pdf(document.documentElement, {
        margin:       1,
        filename:     'דוח בדיקה במערך המזון ב' + ' ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) - ( ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + ' ( ' + $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { dpi: 192, letterRendering: true },
        jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
        callback: function(pdf, filename) {
            scoring_pdf = pdf;

            $('#app nav').css('display', 'none');
            $('.malfunctions-item.scoring').css('display', 'none');

            width = $(document).outerWidth();
            height = $(document).outerHeight();
        
            for(var i = 1; width > height, i <= 10; i++) {
                if(height * i > width) {
                    height = height * i; break;
                }
            }
        
            html2pdf(document.documentElement, {
                margin:       1,
                filename:     $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + ' ) ' + 'מבדק ניהול סיכונים במערך המזון ב' + ' ) ' + ' - ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { dpi: 192, letterRendering: true },
                jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
                callback: function(pdf, filename) {
                    other_pdf = pdf;
        
                    var formData = new FormData();
                    formData.append('_token', TOKEN);
                    formData.append('other_pdf', other_pdf.output('blob'));
                    formData.append('scoring_pdf', scoring_pdf.output('blob'));
                    formData.append('to', to_address);
                    formData.append('subject', message_subject);
                    formData.append('body', message_body);
                    formData.append('other_pdf_name', $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + ' ) ' + 'מבדק ניהול סיכונים במערך המזון ב' + ' ) ' + ' - ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + '.pdf');
                    formData.append('scoring_pdf_name', 'דוח בדיקה במערך המזון ב' + ' ( ' + $('#print_sub').find('span').text().trim().substring(0, $('#print_sub').find('span').text().trim().indexOf('/')) + ' ) - ( ' + $('#print_date').text().substring($('#print_date').text().indexOf('Date') + 6).toString() + 'מיום' + ' ( ' + $('#print_sub').find('span').text().trim().substring($('#print_sub').find('span').text().trim().indexOf('/'), 10) + '.pdf');
                
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

            $('.malfunctions-item.scoring').css('display', 'block');
            $('#app nav').css('display', 'flex');
        }
    });

    $('.malfunctions-item.other').css('display', 'block');
    $('#app nav').css('display', 'flex');
}

$(".comment-editor").keyup(function(e) {
    if(e.keyCode != 13) 
        return;

    var uuid = Math.random().toString(36).substr(2, 9);
    var malfunction_item = $(this).closest(".malfunctions-item");
    var principal_id = malfunction_item.data('principal-id');
    var comment = $(this).val();
    var role = user.title.toLowerCase();

    $(malfunction_item).find('.comments').append(
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

$('.comments').submit(function(e) {
    e.preventDefault();
    e.stopPropagation();

    $('.comment-value').attr('readonly', '');
});

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

function updateLangText() {
    $('.repeating').text(Lang['Repeating']);
}

function deleteMalfunction() {
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
            window.location.href = DELETE_MALFUNCTION_URL;
        }
    });
}
