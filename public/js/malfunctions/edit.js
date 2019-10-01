var chart = null;
var original_data = new Array();
var calculating = false;
var NOTHINIG = 0, LOAD_STATISTICS_CHART = 0, REDIRECT_TO_FORMLIST_PAGE = 1;

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
        }).on('changeDate', function (e) {
            $("input[name='data[date]']").val(this.value)
            updateReportDate(this.value);
            saveMalfunctionForm();
        });

        $('#time').datetimepicker({
            format: 'HH:mm',
        })

        if (!$("input[name='data[service_level]']").val()) {
            $("input[name='data[service_level]']").val($("#service_level").val());
        }

        today();
        currentTime();
        updateScoreLevels();
        saveMalfunctionForm();

        var is_submited = IS_SUBMITTED;
        if (is_submited) {
            $('.malfunction-general.hidden, .malfunction-repaired.hidden, .malfunction-principal.hidden, .malfunction-list.hidden, .malfunction-summary.hidden, .malfunction-uploaded-documents.hidden').removeClass('hidden');
            if ($('.malfunction-principal__item .summernote-textarea').length > 0) {
                $('#malfunction-principal__items').sortable({
                    handle: ".malfunction-sort",
                    update: saveMalfunctionForm
                })
            }
        }
    });

    $('#service_level').on('change', function () {
        $("input[name='data[service_level]']").val($(this).val());
        saveMalfunctionForm();
    });

    $('#company').on('change', function () {
        $("input[name='data[site]']").val('');
        $("input[name='data[subsite]']").val('');
        $("input[name='data[company_representative]']").val('');
        // $("input[name='data[contractor_representative]']").val('');
        // $("input[name='data[admin_name]']").val('');
        // $("input[name='data[employee_name]']").val('');
        $('#site').val('site');
        $('#subsite').val('subsite');
        // $('.employe li:last-child span').html('')
        // $('.employe li:first-child span').html('')
        // $('.employe li:nth-child(2) span').html('')
        // $('#resp').val('');
        var i = 0;
        if ($(this).val() == 'company') {
            $('#site').attr('disabled', true);
            $('#subsite').attr('disabled', true);
            $("input[name='data[company]']").val('');
            saveMalfunctionForm();

        } else {
            $("input[name='data[company]']").val($("option:selected", this).text());
            saveMalfunctionForm();
            $.ajax({
                url: FILTER_COMPANY_URL,
                type: 'post',
                data: {id: $(this).val()},
                success: function (data) {
                    $('#site').attr('disabled', false);
                    $('#subsite').attr('disabled', true);
                    $('#site').html('');
                    $('#site').prepend('<option value="site">Site</option>');
                    for (var i = 0; i < data.sites.length; i++) {
                        $('#site').append("<option value='" + data.sites[i].id + "'>" + data.sites[i].title + "</option>");
                    }
                }
            })
        }
    });

    $('#site').on('change', function () {
        $("input[name='data[subsite]']").val('');
        if ($(this).val() == 'site') {
            $('#subsite').attr('disabled', true);
            $("input[name='data[site]']").val('');
            $("input[name='data[company_representative]']").val('');
            saveMalfunctionForm(LOAD_STATISTICS_CHART);
        } else {
            $("input[name='data[site]']").val($("option:selected", this).text());

            var data = {
                id: $(this).val(),
                malfunction_id: MALFUNCTION_ID
            };

            $.ajax({
                url: FILTER_SITE_URL,
                type: 'post',
                data: data,
                success: function (data) {
                    $('#subsite').attr('disabled', false);
                    $('#subsite').html('');
                    $('#subsite').prepend('<option  value="subsite">' + Lang['Sub-site'] + '</option>');
                    for (var i = 0; i < data.subsites.length; i++) {
                        $('#subsite').append("<option value='" + data.subsites[i].id + "'>" + data.subsites[i].title + "</option>");
                    }
                    $("input[name='data[company_representative]']").val(data.repres[0] ? data.repres[0].representative : "");

                    lastMalfunctionType = JSON.parse(data.lastMalfunctionType);
                    lastMalfunctionFinding = JSON.parse(data.lastMalfunctionFinding);

                    saveMalfunctionForm(LOAD_STATISTICS_CHART);
                }
            });
        }
    });

    $('#subsite').on('change', function () {
        $("input[name='data[subsite]']").val($("option:selected", this).text());

        var data = {
            id: $(this).val(),
            site_id: $('#site').val(),
            malfunction_id: MALFUNCTION_ID
        };

        $.ajax({
            url: FILTER_SUBSITE_URL,
            type: 'post',
            data: data,
            async: false,
            success: function (data) {
                if (data.repres) {
                    $("input[name='data[company_representative]']").val(data.repres);
                }

                lastMalfunctionType = JSON.parse(data.lastMalfunctionType);
                lastMalfunctionFinding = JSON.parse(data.lastMalfunctionFinding);

                saveMalfunctionForm(LOAD_STATISTICS_CHART);
            }
        });
    });

    $('.malfunction-type-select').on('change', function () {
        if (this.value == 'B') {
            var paragraph_type = $(this).closest("tr").data("type")
            if (paragraph_type == "severe") {
                $('.risk').text(Lang['High']);
                $('.risk').data('value', 'High');
                $("input[name='data[risk_level]']").val($('.risk').data('value'));
                saveMalfunctionForm();
            }
        }
    });

    $(document).on('click', '.note-btn-group.btn-group.note-color .note-color:last-child .dropdown-menu .note-palette:first-child button.note-color-btn', function () {
        $('.note-btn-group.btn-group.note-color .note-color:first-child .dropdown-menu .note-palette:first-child button.note-color-btn[data-value="' + $(this).data('value') + '"').click();
    })

    $.each($('.summernote-textarea').not('.disabled'), function (index, element) {
        initTextArea(element)
    });

    function toggleMalfunctionFields(td, state) {
        var risk_textarea = td.siblings('.malfunction-scoring__risk').find('.summernote-textarea'),
            finding_input = $(td.siblings('.malfunction-scoring__finding').children()[0]),
            principal_cb = td.siblings('.malfunction-scoring__principal').find('.malfunction-scoring__principal-checkbox');
        // photos_field = td.siblings('.malfunction-scoring__photos'),
        // photos_input = photos_field.find('.malfunction-upload-image');

        if (state) {
            // enable fields
            finding_input.multipleSelect('enable');

            // photos_field.removeClass('disabled');
            // photos_input.removeAttr('disabled');
        } else {
            // disable fields
            finding_input.multipleSelect('disable');
            finding_input.multipleSelect('setSelects', []);
            finding_input.removeClass('warn');

            // photos_field.addClass('disabled');
            // photos_input.attr('disabled', 'true');

            principal_cb.attr('disabled', 'true');
            principal_cb.prop('checked', false);

            $.each(risk_textarea, function (index, element) {
                $(element).summernote('disable');
                $(element).addClass('disabled');
            });
        }
    }

    $('.malfunction-type-select').on('change', function () {
        if (this.value == 'S' || this.value == 'B') {
            toggleMalfunctionFields($(this).parent(), true);
        } else {
            toggleMalfunctionFields($(this).parent(), false);
        }
    });

    $('.malfunction-finding-select').on('change', function () {
        $(this).attr("disabled", false);
        var find = $('.malfunction-finding-select  li.selected').text();
        var find_val = $(this).val();
        var td = $(this).parent(),
            paragraph_id = td.parent().data('id'),
            risk_td = td.siblings('.malfunction-scoring__risk'),
            category_id = td.parent().data('category-id'),
            principal_cb = td.siblings('.malfunction-scoring__principal').find('.malfunction-scoring__principal-checkbox');
        var $_checkbox, find_disabled_values = [], index = 0;
        $.each(find_val, function (key, value) {
            if ($("input[name='data[malfunction_principal][" + category_id + "][" + paragraph_id + "][" + value + "]']").prop("disabled")) {
                find_disabled_values[index] = value;
                index++;
            }
        })
        if ($('.malfunction-type-select option:selected').val() == 'B') {
            $.ajax({
                url: FIND_URL,
                type: 'post',
                data: {find: find, paragraph_id: paragraph_id},
                success: function (data) {
                    var $_checkbox;
                    $.each(find_disabled_values, function (key, value) {
                        $_checkbox = $("input[name='data[malfunction_principal][" + category_id + "][" + paragraph_id + "][" + value + "]']")
                        if (data.success == true) {
                            $_checkbox.prop('checked', true);
                        } else {
                            $_checkbox.prop('checked', false);
                        }
                    })
                }
            })
        }

        $.each($('.summernote-paragraph-' + paragraph_id), function (index, element) {
            initTextArea(element);

            $(element).summernote('disable');
            $(element).addClass('disabled');
        });

        $.each($(this).find('option'), function (index, element) {
            if (element.selected) {
                principal_cb[index].disabled = false;
            } else {
                principal_cb[index].disabled = true;
            }
        })

        var ar_ms = $(this).prev().multipleSelect('getSelects');
        if (ar_ms.length == 0) {
            risk_td.addClass('hasnt_active');
            td.siblings('.malfunction-scoring__principal').addClass('hasnt_active');
        } else {
            risk_td.removeClass('hasnt_active');
            td.siblings('.malfunction-scoring__principal').removeClass('hasnt_active');
        }

        if (typeof ar_ms == 'object' && ar_ms.length > 0) {
            ar_ms.forEach(function (element, index) {
                risk_td.find('#summernote-paragraph-' + paragraph_id + '-' + element).summernote('enable');
                risk_td.find('#summernote-paragraph-' + paragraph_id + '-' + element).removeClass('disabled');
            })
        }

        saveMalfunctionForm();
    })

    function initTextArea(el) {
        $(el).summernote({
            toolbar: [
                ['para', ['ol', 'numListStyles', 'ul', 'bullListStyles', 'listStyles', 'paragraph']],
                ['style', ['bold', 'underline', 'italic']],
                ['color', ['color', 'color']],
                ['Misc', ['undo', 'redo']],
            ],
            callbacks: {
                onChange: delay(function (e, contents) {
                    var newHtml = $(this).summernote('code');
                    if (user.title.toLowerCase() == 'admin') {
                        newHtml = getHtmlDiff(getOriginalHtml($(this).attr("uuid")), newHtml);
                        if (newHtml.indexOf("<span class='inserted'>") != -1 || newHtml.indexOf("<span class='deleted'>") != -1) {
                            $('input[name="data[status][admin_changed_date]"]').val("changed");
                        }
                    }
                    $(this).find('*').remove();
                    var children = $(newHtml).find('*');
                    for (var i = 0; i < children.length; i++) {
                        var element = children.eq(i);
                        if (!element[0].classList.contains || !element[0].classList.contains
                            || element.text().replace(/(\s|\r|\n)*/g, "") != "") {
                            $(this).append(element);
                        }
                    }
                    saveMalfunctionForm();
                }, 500),
                onInit: function () {
                    $("button.note-btn.btn.btn-light.btn-sm.list-styles").removeClass("dropdown-toggle")
                }
            }
        });
    }

    $(document).on('click', '.malfunction-principal__photo-remove, .malfunction-uploads__item-remove', function () {
        $(this).parent().remove();
        saveMalfunctionForm();
    })

    $('body').on('change', '.malfunction-upload-image', function () {
        var that = this;
        var file_data = $(this).prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);

        $(this).closest(".malfunction-scoring__photos").prepend("<div class='malfunction-principal__photo-item'><span class='uploadProgressBar'></span></div>")
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
                var category_id = $(that).closest('.malfunction-principal__item').data('category-id'),
                    paragraph_id = $(that).closest('.malfunction-principal__item').data('id');

                $(that).closest(".malfunction-scoring__photos").prepend(
                    '<div class="malfunction-principal__photo-item">' +
                    '<a href="' + path + name + '" target="_blank">' +
                    (is_image ? '<img src="' + path + name + '" class="malfunction-principal__photo-item" alt="' + name + '"/>' : (name.length > 20 ? name.substr(0, 20) : name)) +
                    '</a>' +
                    '<i class="malfunction-principal__photo-remove"></i>' +
                    '<input type="hidden" name="data[photo][' + category_id + '][' + paragraph_id + '][]" value="' + name + '"/>' +
                    '</div>'
                );

                saveMalfunctionForm();
            },
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", uploadProgressHandler, false);
                xhr.addEventListener("load", loadHandler, false);
                return xhr;
            }
        });
    });

    var type_value = {
        F: 1,
        S: 0.5,
        B: 0,
        N: 1,
        C: 1
    };

    $('#calculate').on('click', function (e) {
        e.preventDefault();
        
        var is_error = false,
            categories_data = {},
            repair_text = '',
            principal_textareas = [],
            paragraph_scores = [];

        $('.warn').removeClass('warn');

        if ($('#site').val() == 'site') {
            is_error = true;
            $('#site').addClass('warn');
        }

        if ($('#subsite').val() == 'subsite' && $('#subsite option').length >= 2) {
            is_error = true;
            $('#subsite').addClass('warn');
        }

        if ($('#service_level').val() == -1) {
            is_error = true;
            $('#service_level').addClass('warn');
        }

        $.each($('.malfunction-scoring__item'), function (index, paragraph_elem) {
            // Define paragraph info
            var paragraph = $(paragraph_elem),
                number = paragraph.find('.malfunction-scoring__paragraph-number').html(),
                name = paragraph.find('.malfunction-scoring__paragraph-name').html(),
                type = paragraph.find('.malfunction-type-select').val(),
                finding = paragraph.find('.malfunction-finding-select').html(),
                risk_repair = paragraph.find('.summernote-textarea').val(),
                is_principal = paragraph.find('.malfunction-scoring__principal-checkbox'),
                category_id = paragraph.data('category-id'),
                paragraph_id = paragraph.data('id'),
                frr = paragraph.data('frr'),
                photos_html = '',
                photos = $('.malfunction-principal__item[data-category-id="' + category_id + '"][data-id="' + paragraph_id + '"]').find('.malfunction-principal__photo-item').clone(true);

            $.each(photos, function (element, index) {
                photos_html += $(index).get(0).outerHTML
                console.log(name);
            });

            if (!type) {
                paragraph.find('.malfunction-type-select').addClass('warn');
                is_error = true;
            } else if ((type == 'S' || type == 'B') && !finding.length) {
                paragraph.find('.malfunction-finding-select').addClass('warn');
                is_error = true;
            }

            // Showing paragraph score
            var paragraph_score = type_value[type] * paragraph.data('value');
            paragraph.find('.malfunction-scoring__score').html((paragraph_score ? paragraph_score.toFixed(1) : 0) + '%');
            $('#calc-input-paragraph-' + category_id + '-' + paragraph_id).val(paragraph_score.toFixed(1) + '%');
            paragraph_scores[index] = {"score": paragraph.data("value"), "html": ""};
            var paragraph_malfunction_text = "",
                malfunction_principal_html = "",
                name_addition = "";

            // Creating principal malfunctions list
            $.each(paragraph.find('.summernote-textarea'), function (_index, element) {
                if (!$(element).hasClass('disabled')) {
                    var text = $(element).val().trim()
                    text = text.replace(/(<p><\/p>|<ol><\/ol>|<ul><\/ul>)*/g, "");
                    text = text.replace(/<p><br><\/p>/g, '');
                    text = text.replace(/<p/g, "<span")
                    text = text.replace(/<\/p>/g, "</span>");
                    text = text.replace(/></g, ">, <");
                    text = 'X ' + text.trim(' ,');

                    //@TODO display flex has removed.
                    malfunction_principal_html += '<div>' + text + '</div>';
                    paragraph_malfunction_text += '<a>' + name + ' ' + '(' + number + ')' + ':' + '</a>' + $(element).val();
                }
            });

            principal_textareas.push('#principal-textarea-' + category_id + '-' + paragraph_id + '-' + index);
            if ($.inArray(type, ["S", "B"]) !== -1 && (typeof lastMalfunctionType[category_id] !== "undefined" && lastMalfunctionType[category_id] && $.inArray(lastMalfunctionType[category_id][paragraph_id], ["S", "B"]) !== -1)) {
                name_addition = " <strong class='repeating'></strong>";
            }

            let showPrincipalTextArea = false;
            $.each(paragraph.find('.malfunction-scoring__principal-checkbox'), function (index, element) {
                if (element.checked) {
                    showPrincipalTextArea = true
                }
            });

            if (malfunction_principal_html && showPrincipalTextArea) {
                paragraph_scores[index]["html"] =
                    `<div class="malfunction-principal__item" data-category-id="` + category_id + `" data-id="` + paragraph_id + `">
                    <div class="malfunction-sort"></div>
                    <textarea onchange="saveMalfunctionForm()" class="summernote-textarea"  name="data[malfunction_principal_detail][` + index + `][value]" id="principal-textarea-` + category_id + `-` + paragraph_id + `-${index}">
                        <p>` + name + name_addition + `</p>
                        ` + malfunction_principal_html + `
                    </textarea>
                    <input type="hidden" name="data[malfunction_principal_detail][` + index + `][category_id]" value="` + category_id + `" />
                    <input type="hidden" name="data[malfunction_principal_detail][` + index + `][id]" value="` + paragraph_id + `" />
                    <div class="malfunction-scoring__photos ` + ($.inArray(type, ["S", "B"]) === -1 ? "disabled" : "") + `">
                        ` + photos_html + `
                         <div class='upload-photo-wrapper'>
                                <label class="malfunction-scoring__add-photo" for="add-image-\` + category_id + \`-\` + paragraph_id + \`">
                                <i class="fa fa-upload"></i>
                                <input id="add-image-\` + category_id + \`-\` + paragraph_id + \`" class="malfunction-upload-image" type="file" \` + ($.inArray(type, ["S", "B"]) === -1 ? "disabled" : "") + \`>                                                        </label>
                         </div>
                  
                    </div>
                </div>`;
            }

            // if 'finding' is active
            if (!$(paragraph.find('.malfunction-finding-select')[0]).next().children('button.ms-choice').hasClass('disabled')) {
                if (categories_data[paragraph.data('category-id')]) {
                    categories_data[paragraph.data('category-id')]['score'] += paragraph_score;
                    categories_data[paragraph.data('category-id')]['malfunctions'].push(paragraph_malfunction_text)
                    categories_data[paragraph.data('category-id')]['name'] = paragraph.data('category-name')
                    categories_data[paragraph.data('category-id')]['in_list'] = true;
                } else {
                    categories_data[paragraph.data('category-id')] = {
                        score: paragraph_score,
                        malfunctions: [paragraph_malfunction_text],
                        name: paragraph.data('category-name'),
                        in_list: true
                    }
                }

                var numb = 0;
                $.each(paragraph.find('.summernote-textarea'), function (index, elementt) {
                    if (!$(elementt).hasClass('disabled')) {
                        $.each($($.parseHTML($(elementt).val())), function (indexx, element) {
                            if (typeof element.classList == 'object' && element.classList.contains('frr_repair')) {
                                if (is_principal[index].checked) {
                                    numb++;
                                    repair_text += '<p>4.' + numb + ' ' + element.innerHTML + '</p>';
                                }
                            }
                        });
                    }
                });
            } else {
                if (categories_data[paragraph.data('category-id')]) {
                    categories_data[paragraph.data('category-id')]['score'] += paragraph_score;
                } else {
                    categories_data[paragraph.data('category-id')] = {
                        score: paragraph_score,
                        malfunctions: []
                    }
                }
            }
        });

        paragraph_scores.sort(function (a, b) {
            if (a.score < b.score) {
                return 1;
            }
            if (a.score > b.score) {
                return -1;
            }
            return 0;
        });

        if (!is_error) {
            $('#malfunction-principal__items').html('');
            var principal_html = "";
            $.each(paragraph_scores, function (key, paragraph_data) {
                if (paragraph_data.html) {
                    principal_html += paragraph_data.html;
                }
            });
            $('#malfunction-principal__items').html($('#malfunction-principal__items').html() + principal_html);
            $('.malfunction-general.hidden, .malfunction-repaired.hidden, .malfunction-principal.hidden, .malfunction-list.hidden, .malfunction-summary.hidden, .malfunction-uploaded-documents.hidden').removeClass('hidden');

            var list_text = '',
                categories_total_score = 0;
            var i = 1;
            var order = 1;
            for (category_id in categories_data) {
                var category_total = categories_data[category_id]['score'];
                // * $('#category-score-' + category_id).data('category-value');

                $('#category-score-' + category_id).html(category_total.toFixed(1) + '%');
                if (category_id != $(".fork-number").attr('fork')) {
                    categories_total_score += category_total;
                }
                $('#calc-input-category-' + category_id).val(category_total.toFixed(1) + '%');

                if (categories_data[category_id]['in_list']) {
                    list_text += '<p><b><u>' + order + '. ' + categories_data[category_id]['name'] + ' (' + i + ')' + ':' + '</u></b></p>';
                    var subOrder = 1;
                    categories_data[category_id]['malfunctions'].forEach(function (element) {
                        var subOrderText = order + '.' + subOrder + '. ';
                        var text = element.replace('<a>', '<a>' + subOrderText);
                        list_text += text;
                        subOrder++;
                    });
                    list_text += '<p><br></p>';
                    order++;
                }
                i++;
            }


            var general_text = '<p>2.1 ' + Lang['Inspection_made'] + '</p>' +
                '<p>2.2 ' + Lang['Inspection_done'] + '</p>' +
                '<p>2.3 ' + Lang['TwoPointThreeValue'] + '</p>';


            var repaired_text = '';
            $.each($("select.malfunction-type-select"), function (key, type_item) {
                var type = $(type_item).val();
                var finding_select = $(type_item).closest("tr").find("select.malfunction-finding-select");
                var ctg = finding_select.attr("name").replace("data[malfunction_finding][", "");
                ctg = ctg.split(']')[0];
                var prgrph = finding_select.attr("name").replace("data[malfunction_finding][" + ctg + "][", "");
                prgrph = prgrph.split(']')[0];

                if (type === 'F' && typeof lastMalfunctionType[ctg] != "undefined" && typeof lastMalfunctionType[ctg][prgrph] != "undefined" && (lastMalfunctionType[ctg][prgrph] === 'S' || lastMalfunctionType[ctg][prgrph] === 'B')) {
                    $.each(categories, function (i, category) {
                        if (category.id == ctg) {
                            $.each(category.paragraphs, function (j, paragraph) {
                                if (paragraph.id == prgrph) {
                                    $.each(paragraph.frr, function (index, frr) {
                                        if (typeof lastMalfunctionFinding[ctg] !== "undefined" && typeof lastMalfunctionFinding[ctg][prgrph] !== "undefined" && lastMalfunctionFinding[ctg][prgrph].indexOf(frr.id.toString()) != -1) {
                                            repaired_text += '<p>&#x2714;' + ' ' + frr.finding + '</p>';
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });

            $('#malfunction-general-textarea').summernote('code', general_text);

            $('#malfunction-repaired-textarea').summernote('code', repaired_text);

            $('#malfunction-list-textarea').summernote('code', list_text);

            $('#malfunction-summary-textarea').summernote('code', repair_text);

            $('.malfunction-scoring__total-info span').html(categories_total_score.toFixed(1) + '%')
            $('.total').html(categories_total_score.toFixed(1) + '%');

            var low = (categories_total_score >= 85 && categories_total_score <= 100) ? true : false;
            var medium = (categories_total_score >= 75 && categories_total_score <= 84) ? true : false;
            var high = (categories_total_score < 75) ? true : false;

            var bb = [];
            for (var k = 0; k < $('.malfunction-type-select').length; k++) {
                bb.push($('.malfunction-type-select').eq(k).val());
            }

            var logic = bb.indexOf('B');
            if (logic == -1) {
                if (low) {
                    $('.risk').text(Lang['Low']);
                    $('.risk').data('value', 'Low');
                } else if (medium) {
                    $('.risk').text(Lang['Medium']);
                    $('.risk').data('value', 'Medium');
                } else if (high) {
                    $('.risk').text(Lang['High']);
                    $('.risk').data('value', 'High');
                }
            } else {
                $('.risk').text(Lang['High']);
                $('.risk').data('value', 'High');
            }

            $("input[name='data[risk_level]']").val($('.risk').data('value'));

            $('#malfunction-principal__items').sortable({
                handle: ".malfunction-sort",
                update: saveMalfunctionForm
            })

            $('#calc-input-total').val(categories_total_score.toFixed(1) + '%');

            principal_textareas.forEach(function (element, index) {
                $(element).summernote({
                    toolbar: [
                        ['para', ['ol', 'numListStyles', 'ul', 'bullListStyles', 'listStyles', 'paragraph']],
                        ['style', ['bold', 'underline', 'italic']],
                        ['color', ['color', 'color']],
                        ['Misc', ['undo', 'redo']],
                    ],
                    callbacks: {
                        onChange: delay(function (e, contents) {
                            var newHtml = $(this).summernote('code');
                            if (user.title.toLowerCase() == 'admin') {
                                newHtml = getHtmlDiff(getOriginalHtml($(this).attr("uuid")), newHtml);
                                if (newHtml.indexOf("<span class='inserted'>") != -1 || newHtml.indexOf("<span class='deleted'>") != -1) {
                                    $('input[name="data[status][admin_changed_date]"]').val("changed");
                                }
                            }
                            $(this).find('*').remove();
                            var children = $(newHtml).find('*');
                            for (var i = 0; i < children.length; i++) {
                                var element = children.eq(i);
                                if (!element[0].classList.contains || !element[0].classList.contains
                                    || element.text().replace(/(\s|\r|\n)*/g, "") != "") {
                                    $(this).append(element);
                                }
                            }
                            saveMalfunctionForm();
                        }, 500),
                        onInit: function () {
                            $("button.note-btn.btn.btn-light.btn-sm.list-styles").removeClass("dropdown-toggle")
                        }
                    }
                });
            });

            var paragraph_total = 0;
            var malfunction_types = $('.malfunction-scoring__table[data-category-id=' + FORK + '] .malfunction-type-select');
            $.each(malfunction_types, function (index, type) {
                switch ($(type).val()) {
                    case 'S':
                        paragraph_total += 50;
                        break;
                    case 'B':
                        paragraph_total += 0;
                        break;
                    default:
                        paragraph_total += 100;
                        break;
                }
            });

            var gastronomy_score = paragraph_total / (typeof malfunction_types != 'undefined' && malfunction_types.length != 0 ? malfunction_types.length : 1);
            gastronomy_score = gastronomy_score.toFixed(2);

            $('#gastronome').html(gastronomy_score + '%');
            $("input[name='data[gastronomy_score]']").val($('#gastronome').text());
            $('#malfunction-calc-error-msg').html('');
            // $('#calculate').style.background = '';
            calculating = false;
            
            saveMalfunctionForm(LOAD_STATISTICS_CHART);
        } else {
            $('#malfunction-calc-error-msg').html(Lang['SomeFieldsAreMissiing']);

        }

        updateLangText();
        updateReportRep();
        updateReportDate();
        updateOriginalData();
        updateSendToAdminButton();

        var malfunction_id = MALFUNCTION_ID;
        var level = $('.risk').val();
        $.ajax({
            url: LEVEL_URL,
            type: 'post',
            data: {id: malfunction_id, level: level},
            success: function () {
            }
        })

        $('.malfunction-principal__item .note-editable.card-block').each(function(){
            $(this).find('p:eq(0)').html('<span style="border: 1px solid black; padding-left: 3px; padding-right: 3px; margin-right: 3px;">X</span> ' + ' <span style="text-decoration: underline;">' + $(this).find('p:eq(0)').text().trim() + '</span>');
            var obj = $(this).find('div:eq(0)');
            while (typeof(obj.html()) !== 'undefined') {
                obj.html('<span style="">' + (obj.text().substring(1)) + '</span>');
                obj.css('padding-right', '25px');
                obj = obj.next();
            }            
        });
        // $('#malfunction-principal__items .note-editable.card-block').find('p:eq(0)').text('X' + $('#malfunction-principal__items .note-editable.card-block').find('p:eq(0)').text());
        // $('#malfunction-principal__items .note-editable.card-block').find('div:eq(0)').text($('#malfunction-principal__items .note-editable.card-block').find('div:eq(0)').text().substring(1));

    });

    $('.malfunction-type-select, .malfunction-scoring__principal-checkbox, .contractor_representative').on('change', function () {
        saveMalfunctionForm();
    })

    $(document).on('change', '.warn', function () {
        $(this).removeClass('warn');
    })

    $("#malfunction-culinary-check").click(function () {
        $(".malfunction-culinary-heading").toggleClass("hidden");
        $("#malfunction-culinary-textarea").toggleClass("summernote-textarea").toggleClass("hidden");
        if ($("#malfunction-culinary-textarea").hasClass("summernote-textarea")) {
            $('#malfunction-culinary-textarea').summernote({
                toolbar: [
                    ['para', ['ol', 'numListStyles', 'ul', 'bullListStyles', 'listStyles', 'paragraph']],
                    ['style', ['bold', 'underline', 'italic']],
                    ['color', ['color', 'color']],
                    ['Misc', ['undo', 'redo']],
                ],
                callbacks: {
                    onChange: delay(function (e, contents) {
                        var newHtml = $(this).summernote('code');
                        if (user.title.toLowerCase() == 'admin') {
                            newHtml = getHtmlDiff(getOriginalHtml($(this).attr("uuid")), newHtml);
                            if (newHtml.indexOf("<span class='inserted'>") != -1 || newHtml.indexOf("<span class='deleted'>") != -1) {
                                $('input[name="data[status][admin_changed_date]"]').val("changed");
                            }
                        }
                        $(this).find('*').remove();
                        var children = $(newHtml).find('*');
                        for (var i = 0; i < children.length; i++) {
                            var element = children.eq(i);
                            if (!element[0].classList.contains || !element[0].classList.contains
                                || element.text().replace(/(\s|\r|\n)*/g, "") != "") {
                                $(this).append(element);
                            }
                        }
                        saveMalfunctionForm();
                    }, 500),
                    onInit: function () {
                        $("button.note-btn.btn.btn-light.btn-sm.list-styles").removeClass("dropdown-toggle")
                    }
                }
            });

            updateOriginalData();

            $("#malfunction-culinary-textarea").next(".note-editor.note-frame.card").removeClass("hidden")
        } else {
            $("#malfunction-culinary-textarea").next(".note-editor.note-frame.card").addClass("hidden");
        }

        saveMalfunctionForm();
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
            valueFormatString: "YYYY-MM-DD",
            reversed: true
        },
        axisY2: {
            title: Lang['Score'],
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

    $('#duplicate_malfunction').click(function () {
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
                location.href = BASE_URL + '/malfunctions/' + result.malfunction_id + '/edit';
            }
        });
    });

    $('#download_pdf').click(function () {

        $('#app nav').css('display', 'none');
        $('.malfunction-other').css('display', 'none');

        var width = $(document).width();
        var height = $(document).height();

        for (var i = 1; width > height, i <= 10; i++) {
            if (height * i > width) {
                height = height * i;
                break;
            }
        }

        html2pdf(document.documentElement, {
            margin: 1,
            filename: 'ציונים' + '.pdf',
            image: {type: 'jpeg', quality: 0.98},
            html2canvas: {dpi: 192, letterRendering: true},
            jsPDF: {unit: 'pt', format: [width, height], orientation: 'portrait'},
            callback: function (pdf, filename) {
                pdf.save(filename);

                $('#app nav').css('display', 'none');
                $('.malfunction-scoring').css('display', 'none');

                var width = $(document).outerWidth();
                var height = $(document).outerHeight();

                for (var i = 1; width > height, i <= 10; i++) {
                    if (height * i > width) {
                        height = height * i;
                        break;
                    }
                }

                html2pdf(document.documentElement, {
                    margin: 1,
                    filename: 'מבדק' + '.pdf',
                    image: {type: 'jpeg', quality: 0.98},
                    html2canvas: {dpi: 192, letterRendering: true},
                    jsPDF: {unit: 'pt', format: [width, height], orientation: 'portrait'}
                });

                $('.malfunction-scoring').css('display', 'block');
                $('#app nav').css('display', 'flex');
            }
        });

        $('.malfunction-other').css('display', 'block');
        $('#app nav').css('display', 'flex');
    });

    $('#share_pdf').click(function () {
        $('.modal.sharing').addClass('show');
    });

    $('#print_pdf').click(function () {
        if (!window_focus) return;

        $('#printingScorinig').remove();
        $("#printing-holder").append("<div id='printingScorinig' style='background-color: white; padding: 20px;'></div>");
        $(".malfunction-scoring").clone().appendTo('#printingScorinig');

        var content = document.getElementById('printingScorinig').innerHTML;
        var mywindow = window.open('', Lang['Print'], 'width=1200, height=600');

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
            '<div class="row justify-content-center">' +
            '<div class="col-md-12 card malfunctions">' +
            '<form id="malfunction-form">');
        mywindow.document.write(content);

        var context = mywindow.document.getElementsByClassName('canvasjs-chart-canvas')[0].getContext('2d');
        var oldCanvas = $('.malfunction-scoring .canvasjs-chart-canvas')[0];
        context.drawImage(oldCanvas, 0, 0);

        mywindow.document.write('</form></div></div></div></body></html>');

        mywindow.document.close();
        mywindow.focus()

        setTimeout(() => {
            mywindow.print();
            mywindow.close();

            $('#printingScorinig').remove();
        }, 500);
    });

    $('#send_to_admin').click(function () {
        if ($('#send_to_admin').hasClass('disabled'))
            return;

        swal({
            title: Lang['Are you sure?(send to admin)'],
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: Lang['Yes'],
            cancelButtonText: Lang['Cancel(no)'],
        }).then((result) => {
            if (result.value) {
                $('.loader-container').css('display', 'block');
                $('#loadingBlock').fadeIn(100);
                $("input[name='data[status][stage]']").val('pending');
                calculating = false;
                saveMalfunctionForm(REDIRECT_TO_FORMLIST_PAGE);
            }
        });
    });

    reloadStatistics();

    updateLangText();
    updateReportRep();
    updateReportDate();
    updateOriginalData();
    updateSendToAdminButton();

    
});

function isImage(name = "") {
    name = name.split(".");
    var extention = name[name.length - 1];//name.last()
    return ($.inArray(extention.toLowerCase(), ["jpg", "jpeg", "bmp", "gif", "png"]) !== -1);
}

function saveMalfunctionForm(after_saved = NOTHINIG) {
    if (calculating) {
        console.log('do nothing');
        return;
    }
    
    calculating = true;
    
    var form_data = $('#malfunction-form').serialize();

    if ($('#change-risk-select').find(":selected").text().trim() === 'ח' && $('#paragraph-type-hidden-input').val() === 'severe') {
        $('#risk-value-span').text('גבוה')
    }
    
    $.ajax({
        url: UPDATE_URL,
        data: form_data,
        method: 'POST',
        // async: false,
        success: function (data) {
            switch (after_saved) {
                case LOAD_STATISTICS_CHART:
                    reloadStatistics();
                    calculating = false;
                    break;
                case REDIRECT_TO_FORMLIST_PAGE:
                    location.href = FORM_LIST_URL;
                    break;
                case NOTHINIG:
                    break;
            }
            
            calculating = false;
        }
    });
}

function uploadProgressHandler(event) {
    // $("#loaded_n_total").html("Uploaded "+event.loaded+" bytes of "+event.total);
    var percent = (event.loaded / event.total) * 100;
    var progress = Math.round(percent);
    $(".uploadProgressBar").css("width", progress + "%");
}

function loadHandler(event) {
    $(".uploadProgressBar").parent().remove();
}

function updateTime(target) {
    $("input[name='data[time_]']").val($(target).val())
    saveMalfunctionForm();
}

function uploadFiles(target) {
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
            $.each(JSON.parse(data), function (key, value) {
                is_image = isImage(value);
                $("#malfunction-uploaded-documents").prepend(
                    //malfunction-principal__photo-item" style="background-image: url('${data}');"
                    `<div class="malfunction-uploads__item">` +
                    `<a href="${BASE_URL}/uploads/${value}" target="_blank" class="malfunction-uploads__text">` + (is_image ? ('<img class="malfunction-principal__photo-item" src="' + (BASE_URL + "/uploads/" + value) + '" alt="' + value + '"/>') : (value.length > 20 ? value.substr(0, 20) : value)) + `</a>` +
                    `<i class="malfunction-uploads__item-remove"></i>
                        <input type="hidden" name="data[malfunction-uploads][]" value="${value}"/>
                     </div>`
                );

            })
            saveMalfunctionForm();
        }
    });
}

function getTodayDate() {
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
    if (!$('#datepicker').val()) {
        $('#datepicker').val(curr_date + "-" + curr_month + "-" + curr_year);
        $("input[name='data[date]']").val($('#datepicker').val())
    }
}

function reloadStatistics() {
    if ($('#site option:selected').val() == 'site')
        return;

    var site = $('#site option:selected').text();
    var subsite = $('#subsite option:selected').text();
    if ($('#subsite option:selected').val() == 'subsite') {
        subsite = '';
    }

    var sites = [];
    sites.push({site: site, subsite: subsite});

    var report_date = $('#datepicker').val();
    var date_pieces = report_date.split('-');
    if (date_pieces.length == 3) {
        report_date = (2000 + parseInt(date_pieces[2])) + '-' + date_pieces[1] + '-' + date_pieces[0];
    }

    var start_date = new Date(report_date), end_date = new Date(report_date);
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
    if (chart == null) return;

    var sites = [], data = [], max = 0, start_date = null, end_date = null;
    for (var i = 0; result.data != undefined && i < result.data.length; i++) {
        var site_data = result.data[i];

        var tempDataPoints = [], dataPoints = [];
        for (var j = 0; j < site_data.data.length; j++) {
            var date = new Date(site_data.data[j].date);
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            tempDataPoints.push({
                x: date,
                y: parseFloat(site_data.data[j].score),
                indexLabel: site_data.data[j].score,
                markerType: "retangle",
                markerColor: "#6B8E23"
            });
        }

        tempDataPoints.sort(function (a, b) {
            return new Date(b.x) - new Date(a.x);
        });

        for (var j = 0; j < tempDataPoints.length;) {
            dataPoints.push(tempDataPoints[j]);

            if (start_date == null && end_date == null) {
                start_date = new Date(tempDataPoints[j].x);
                end_date = new Date(tempDataPoints[j].x);
            }

            if (start_date - new Date(tempDataPoints[j].x) > 0) {
                start_date = new Date(tempDataPoints[j].x);
            }

            if (new Date(tempDataPoints[j].x - end_date) > 0) {
                end_date = new Date(tempDataPoints[j].x);
            }

            var scores = [];
            scores.push(tempDataPoints[j].y);
            for (var k = j + 1; k < tempDataPoints.length; k++) {
                if (dataPoints[dataPoints.length - 1].x - tempDataPoints[k].x == 0) {
                    scores.push(tempDataPoints[k].y);
                }
            }

            var score = 0;
            for (var k = 0; k < scores.length; k++) {
                score += $.isNumeric(scores[k]) ? scores[k] : 0;
            }
            score = score / scores.length;

            if (max < score) max = score;

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

    for (var i = 0; chart.data != undefined && i < chart.data.length; i++) {
        chart.data[i].remove();
    }

    if (start_date != null && end_date != null) {
        var week_date = new Date(start_date.getTime() + 7 * 24 * 60 * 60 * 1000);
        chart.options.axisX.intervalType = (week_date - end_date >= 0) ? "day" : "week";
    }

    chart.options.data = data;
    chart.options.axisY2.interval = max ? max / 6 : undefined;
    chart.options.axisY2.labelFormatter = function (e) {
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

    if (to_address == '' || message_subject == '' || message_body == '')
        return;

    $('.modal.sharing').removeClass('show');

    $('#app nav').css('display', 'none');
    $('.malfunction-other').css('display', 'none');

    var width = $(document).width();
    var height = $(document).height();

    for (var i = 1; width > height, i <= 10; i++) {
        if (height * i > width) {
            height = height * i;
            break;
        }
    }

    var scoring_pdf = null, other_pdf = null;
    html2pdf(document.documentElement, {
        margin: 1,
        filename: 'statistics_scoring' + (new Date().getTime()) + '.pdf',
        image: {type: 'jpeg', quality: 0.98},
        html2canvas: {dpi: 192, letterRendering: true},
        jsPDF: {unit: 'pt', format: [width, height], orientation: 'portrait'},
        callback: function (pdf, filename) {
            scoring_pdf = pdf;

            $('#app nav').css('display', 'none');
            $('.malfunction-scoring').css('display', 'none');

            width = $(document).outerWidth();
            height = $(document).outerHeight();

            for (var i = 1; width > height, i <= 10; i++) {
                if (height * i > width) {
                    height = height * i;
                    break;
                }
            }

            html2pdf(document.documentElement, {
                margin: 1,
                filename: 'מבדק' + '.pdf',
                image: {type: 'jpeg', quality: 0.98},
                html2canvas: {dpi: 192, letterRendering: true},
                jsPDF: {unit: 'pt', format: [width, height], orientation: 'portrait'},
                callback: function (pdf, filename) {
                    other_pdf = pdf;

                    var formData = new FormData();
                    formData.append('_token', TOKEN);
                    formData.append('other_pdf', other_pdf.output('blob'));
                    formData.append('scoring_pdf', scoring_pdf.output('blob'));
                    formData.append('to', to_address);
                    formData.append('subject', message_subject);
                    formData.append('body', message_body);

                    $.ajax({
                        url: SEND_PDF_URL,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                    }).done(function (data) {
                        showAlert('success', Lang['Message sent successfully']);
                    }).fail(function () {
                        showAlert('success', Lang['Email was not sent']);
                    });
                }
            });

            $('.malfunction-scoring').css('display', 'block');
            $('#app nav').css('display', 'flex');
        }
    });

    $('.malfunction-other').css('display', 'block');
    $('#app nav').css('display', 'flex');
}

function getOriginalHtml(uuid) {
    var org_data = '';
    $.each(original_data, function (index, value) {
        if (value.uuid == uuid) {
            org_data = value.data;
        }
    });

    return org_data;
}

function updateOriginalData() {
    $.each($('.summernote-textarea').not('.disabled'), function (index, element) {
        var uuid = $(element).attr("uuid");
        if (typeof uuid === "undefined" || uuid === false) {
            uuid = Math.random().toString(36).substr(2, 9);
            $(element).attr("uuid", uuid);
            original_data.push({"uuid": uuid, data: $(element).summernote('code')});
        }
    });
}

function checkError() {
    var is_error = $('#site').val() == 'site';
    is_error |= $('#subsite option').length >= 2 && $('#subsite').val() == 'subsite';
    is_error |= $('#service_level').val() == -1;

    $.each($('.malfunction-scoring__item'), function (index, paragraph_elem) {
        var paragraph = $(paragraph_elem);
        var type = paragraph.find('.malfunction-type-select').val();
        var finding = paragraph.find('.malfunction-finding-select').val();

        is_error |= !type || ((type == 'S' || type == 'B') && !finding.length);
    });

    return is_error;
}

function updateSendToAdminButton() {
    if (checkError()) {
        $('#send_to_admin').addClass('disabled');
    } else {
        $('#send_to_admin').removeClass('disabled');
    }
}

function updateScoreLevels() {
    if (!checkError()) return;

    $('#calc-input-total').val('');
    $('.total').html('---');

    $("input[name='data[risk_level]']").val(-1);
    $('.risk').html('---');
    $('.risk').data('value', -1);

    $("input[name='data[gastronomy_score]']").val('');
    $('#gastronome').html('---');
}

function updateReportDate(selected_date = "undefined") {
    if (selected_date == 'undefined') {
        selected_date = $('#datepicker').val();
    }
    date = selected_date.split('-');
    if (date.length == 3) {
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

function updateReportRep() {
    $('.report-rep').text($('.employe li:last-child input').val());
}

function uploadFromSignsForms() {
    $('.subview-container').addClass('show');
    $('.signs_forms .fileContent .item-check').prop('checked', false);
}

$('.subview-container').on('click', function () {
    $('.subview-container').removeClass('show');
});

$('.subview-container').on('keyup', function (e) {
    if (e.keycode == 27) {
        $('.subview-container').removeClass('show');
    }
});

$('.signs_forms').on('click', function (e) {
    e.stopPropagation();
});

$('.modal').on('click', function (e) {
    e.stopPropagation();
});

function uploadCheckedFiles() {
    $('.subview-container').removeClass('show');

    var file_ids = [];
    $.each($("input.item-check:checked"), function () {
        file_ids.push($(this).data('id'));
    });

    if (file_ids.length == 0)
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
            $.each(JSON.parse(data), function (key, value) {
                is_image = isImage(value);
                $("#malfunction-uploaded-documents").prepend(
                    `<div class="malfunction-uploads__item">` +
                    `<a href="${BASE_URL}/uploads/${value}" target="_blank" class="malfunction-uploads__text">` + (is_image ? ('<img class="malfunction-principal__photo-item" src="' + (BASE_URL + "/uploads/" + value) + '" alt="' + value + '"/>') : (value.length > 20 ? value.substr(0, 20) : value)) + `</a>` +
                    `<i class="malfunction-uploads__item-remove"></i>
                        <input type="hidden" name="data[malfunction-uploads][]" value="${value}"/>
                    </div>`
                );

            })
            saveMalfunctionForm();
        }
    });
}

$('#upload_from_computer').click(function (e) {
    $('#file').click();
});


function valid() {
    let is_error = false;

    if ($('#site').val() === 'site') {
        is_error = true;
    }

    if ($('#subsite').val() === 'subsite' && $('#subsite option').length >= 2) {
        is_error = true;
    }

    if ($('#service_level').val() === -1) {
        is_error = true;
    }

    $.each($('.malfunction-scoring__item'), function (index, paragraph_elem) {
        const paragraph = $(paragraph_elem);
        const type = paragraph.find('.malfunction-type-select').val();
        const finding = paragraph.find('.malfunction-finding-select').val();

        if (!type) {
            is_error = true;
        } else if ((type === 'S' || type === 'B') && !finding.length) {
            is_error = true;
        }
    });

    return !is_error;
}

function updateButtonColor() {
    const elem = document.getElementById('calculate');
    if (valid()) {
        elem.style.backgroundColor = '#0074d9'
    } else {
        elem.style.backgroundColor = ''
    }
}
