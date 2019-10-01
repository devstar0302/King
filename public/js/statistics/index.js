
$(document).ready(function() {
	$('#site').change(function() {
		var sites = [], parent_sites = [];
		var site_items = $('#site > option:selected');
		for(var i = 0; i < site_items.length; i++) {
			if($(site_items[i]).data('has-subsites')) {
				parent_sites.push($(site_items[i]).val());
			} else {
				sites.push($(site_items[i]).val());
			}
		}

		$('#subsite option.subsite-item').remove();
		var subsite_items = '';
		for(key in g_SitesSubsites) {
            var site_subsite = g_SitesSubsites[key];
			for(var j = 0; j < parent_sites.length; j++) {
				if(site_subsite.site == parent_sites[j]) {	
					subsite_items += '<option class="subsite-item" value="'+ site_subsite.subsite +'" data-site="'+ site_subsite.site +'">'+ site_subsite.subsite +'</option>';
					break;
				}
			}
		}
		$('#subsite').append(subsite_items);
		$('#subsite').multipleSelect({
            placeholder: Lang['Sub-site'],
            selectAllText: Lang['SelectAll'],
            allSelected: Lang['AllSelected'],
            noMatchesFound: Lang['NoMatchesFound']
        });
	});

    $('#statistics-form').submit(function(e) { e.preventDefault(); });

	$('#site').multipleSelect({
        placeholder: Lang['Site'],
        selectAllText: Lang['SelectAll'],
        allSelected: Lang['AllSelected'],
        noMatchesFound: Lang['NoMatchesFound']
    });

    $('#statistic_types').comboTree({source : g_StatisticTypes, isMultiple: false});

    $('.comboTreeItemTitle').on('click', function() {
        var category_id = -1, paragraph_id = -1;

        var id = $('.comboTreeItemHover').data('id');
        var value = $('.comboTreeItemHover').data('value');
        if(id != "undefined") {
            var parent_ul = $('.comboTreeItemHover').parent('.ComboTreeItemChlid').parent('ul');
            if($(parent_ul).parent('.ComboTreeItemParent').length > 0) {
                category_id = $(parent_ul).parent('.ComboTreeItemParent').find('.comboTreeItemTitle').data('id');
                paragraph_id = id;
            } else {
                category_id = id;
            }
        }

        $('#selected_type').val(value);
        $('#category_id').val(category_id);
        $('#paragraph_id').val(paragraph_id);
    });

	$('#apply').click(function() {
		var sites = [];
		var site_items = $('#site > option[data-has-subsites=0]:selected');		
		for(var i = 0; i < site_items.length; i++) {
			sites.push({site:$(site_items[i]).text(), subsite:''});
		}

		var subsite_items = $('#subsite > option:selected');		
		for(var i = 0; i < subsite_items.length; i++) {
			sites.push({site:$(subsite_items[i]).data('site'), subsite:$(subsite_items[i]).text()});
		}

		var start_date = $('#date-range').data('daterangepicker').startDate;
		if(start_date != undefined) {
			start_date = start_date.format('YYYY-MM-DD');
		}
		var end_date = $('#date-range').data('daterangepicker').endDate;
		if(end_date != undefined) {
			end_date = end_date.format('YYYY-MM-DD');
		}

        var statistic_type = $('#selected_type').val();
        var category_id = $('#category_id').val();
        var paragraph_id = $('#paragraph_id').val();

		var data = {
			_token: TOKEN,
			sites: sites,
			start_date: start_date,
			end_date: end_date,
            statistic_type: statistic_type,
            category_id: category_id,
            paragraph_id: paragraph_id
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
    });

    function updateChart(result) {
        var type = result.type;

        var sites = [], data = [], max = 0, start_date = null, end_date = null;
        for(var i = 0; result.data != undefined && i < result.data.length; i++) {
            var site_data = result.data[i];

            var tempDataPoints = [], dataPoints = [];
            for(var j = 0; j < site_data.data.length; j++) {
                var date = new Date(site_data.data[j].date);
                date.setHours(0); date.setMinutes(0); date.setSeconds(0);
                tempDataPoints.push({ x: date, y: parseFloat(site_data.data[j].score), indexLabel: site_data.data[j].score, markerType: "retangle",  markerColor: "#6B8E23" });
            }
            
            tempDataPoints.sort( function(a,b){ return new Date(a.x) - new Date(b.x); } );

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
                if( type != 'S' && type != 'B' && type != 'All' && type != 'Repeating' ) {
                    score = score / scores.length;
                }

                if(max < score) max = score;

                dataPoints[dataPoints.length - 1].y = score;
                if(type == 'risk level') {
                    dataPoints[dataPoints.length - 1].indexLabel = risk_values[score];
                }
                else if(type == 'service level') {
                    dataPoints[dataPoints.length - 1].indexLabel = service_values[score];
                }
                else {
                    dataPoints[dataPoints.length - 1].indexLabel = score.toFixed(2).toString();
                }

                j += scores.length;
            }

            var lineColour = 'rgb('+ (Math.floor(Math.random() * 256)) + ','+ (Math.floor(Math.random() * 256)) + ','+ (Math.floor(Math.random() * 256)) + ')';
            sites.push({site: site_data.site, lineColor: lineColour, thickness: 2});

            data.push({
                site: site_data.site,
                type: "line",
                markerSize: 12,
                lineColor: lineColour,
                lineThickness: 2,
                xValueFormatString: "YYYY-MM-DD",
                yValueFormatString: "###.#",
                toolTipContent: "<span>{x}: {indexLabel}</span>",
                dataPoints: dataPoints
            });
        }

        //checking overlapping
        for(var i = 0; i < data.length - 1; i ++) {
            for(var j = i + 1; j < data.length; j ++) {
                var data_first_line = data[i], data_second_line = data[j];

                if(data_first_line.dataPoints.length < 2 || data_second_line.dataPoints.length < 2) continue;

                var overlapped = false;
                for(var k = 0; k < data_first_line.dataPoints.length - 1; k ++) {
                    for(var l = 0; l < data_second_line.dataPoints.length - 1; l ++) {
                        var first_line_firt_point = {x: date_diff_indays(start_date, data_first_line.dataPoints[k].x), y:data_first_line.dataPoints[k].y};
                        var first_line_second_point = {x: date_diff_indays(start_date, data_first_line.dataPoints[k+1].x), y:data_first_line.dataPoints[k+1].y};
                        var second_line_first_point = {x: date_diff_indays(start_date, data_second_line.dataPoints[l].x), y:data_second_line.dataPoints[l].y};
                        var second_line_second_point = {x: date_diff_indays(start_date, data_second_line.dataPoints[l+1].x), y:data_second_line.dataPoints[l+1].y};

                        var has_intersect = !(first_line_firt_point.x >= second_line_second_point.x || second_line_first_point.x >= first_line_second_point.x);
                        if(has_intersect) {
                            var alpha = (first_line_second_point.y - first_line_firt_point.y) / (first_line_second_point.x - first_line_firt_point.x);
                            if( alpha * (second_line_first_point.x - first_line_firt_point.x) + first_line_firt_point.y == second_line_first_point.y && 
                                alpha * (second_line_second_point.x - first_line_firt_point.x) + first_line_firt_point.y == second_line_second_point.y ) {
                                overlapped = true; break;
                            }
                        }
                    }
                    if(overlapped) break;
                }
                if(overlapped && data_first_line.lineThickness == data_second_line.lineThickness) {
                    if(data_first_line.dataPoints.length >= data_second_line.dataPoints.length) {
                        data_first_line.lineThickness = data_second_line.lineThickness + 5;
                    } else {
                        data_second_line.lineThickness = data_first_line.lineThickness + 5;
                    }

                    for(var index = 0; index < sites.length; index ++) {
                        if(sites[index].site == data_first_line.site) {
                            sites[index].thickness = data_first_line.lineThickness;
                        } else if(sites[index].site == data_second_line.site) {
                            sites[index].thickness = data_second_line.lineThickness;
                        }
                    }
                }
            }
        }


        var site_fullnames = [], site_linecolors = '<p style="margin: 0 auto;">';
        for(var i = 0; i < sites.length; i++) {
            site_fullnames.push(sites[i].site);
            var site = sites[i].site.split('-')[0];
            site_linecolors += '<span class="site-line-color" style="border-top:'+ (sites[i].thickness) + 'px solid ' + sites[i].lineColor +'"></span><span class="site-line-desc">'+ site +'</span>';
        }
        site_linecolors += '</p>';

        $('.site-names').text(site_fullnames.join(' Vs. '));
        $('.site-linecolors').html(site_linecolors);

        for(var i = 0; chart.data != undefined && i < chart.data.length; i++) {
            chart.data[i].remove();
        }

        chart.options.data = data;

        if(start_date != null && end_date != null) {
            var week_date = new Date(start_date.getTime() + 7 * 24 * 60 * 60 * 1000);
            chart.options.axisX.intervalType = (week_date - end_date >= 0) ? "day" : "week";
        }

        if(type == 'risk level' || type == 'service level') {
            chart.options.axisY.interval = 1;
        } else {
            chart.options.axisY.interval = max / 6;
        }

        if( type == 's' || type == 'b' || type == 'all' || type == 'repeating' ) {
            chart.options.axisY.title = Lang['Number'];
        } else {
            chart.options.axisY.title = Lang['Score'];
        }

        chart.options.axisY.labelFormatter = function ( e ) {
            if(type == 'risk level') {
                return risk_values[e.value];  
            }
            else if(type == 'service level') {
                return service_values[e.value];  
            }
            return e.value.toFixed(2);
        }  

        if(chart.options.axisY.interval == 0) {
            chart.options.axisY.interval = undefined;
        }

        chart.render();
        clearTrialMark($('.canvasjs-chart-canvas')[0], '#fff', chart.height);
    }

    var chart = new CanvasJS.Chart("chartContainer", {
        theme: "light2",
        animationEnabled: true,
        axisX: {
            title: Lang["Date"],
            interval: 1,
            intervalType: "week",
            labelAngle: -50,
            labelFontSize: 16,
            valueFormatString: "YYYY-MM-DD"
        },
        axisY:{
            title: Lang["Score"],
            labelFontSize: 16,
            valueFormatString: "#0"
        },
        data: [{
            type: "line",
            markerSize: 12,
            xValueFormatString: "YYYY-MM-DD",
            yValueFormatString: "###.#",
            dataPoints: []
        }]
    });
    
    chart.render();
    clearTrialMark($('.canvasjs-chart-canvas')[0], '#fff', chart.height);

    $('#download_pdf').click(function() {
        $('#app nav').css('display', 'none');

        var width = $(document).width();
        var height = $(document).height();

        for(var i = 1; width > height, i <= 10; i++) {
            if(height * i > width) {
                height = height * i; break;
            }
        }

        html2pdf(document.getElementById('pdf-container'), {
            margin:       1,
            filename:     'statistics_chart_'+ (new Date().getTime()) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { dpi: 192, letterRendering: true },
            jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' }
        });

        $('#app nav').css('display', 'flex');
    });

    $('#share_pdf').click(function() {
        $('.modal.sharing').addClass('show');
    });
});

function sendFile() {
    var to_address = $('#to_address').val();
    var message_subject = $('#message_subject').val();
    var message_body = $('#message_body').val();
    message_body = message_body.replace(/(\n|\r)/g, '<br>');

    if(to_address == '' || message_subject == '' || message_body == '')
        return;

    $('.modal.sharing').removeClass('show');

    var width = $('.card.statistics').width();
    var height = $('.card.statistics').height();
    for(var i = 1; i <= 10; i++) {
        if(height * i > width) {
            height = height * i; break;
        }
    }

    $('#app nav').css('display', 'none');

    var pdf = html2pdf(document.documentElement, {
        margin:       1,
        filename:     'statistics_chart_'+ (new Date().getTime()) + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { dpi: 192, letterRendering: true },
        jsPDF:        { unit: 'pt', format: [width, height], orientation: 'portrait' },
        callback: function(pdf, filename) {
            var formData = new FormData();
            formData.append('_token', TOKEN);
            formData.append('pdf', pdf.output('blob'));
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
    
            $('#app nav').css('display', 'flex');
        }
    });
}
