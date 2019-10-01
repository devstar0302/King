$(document).ready(function(){
    // if ($('#lang').val() == 'he') {
    //     $('.btn_con').parent().css('padding-right', '278px');
    // }
    // else {
    //     $('.btn_con').parent().css('padding-left', '190px');
    // }
    $('#btn_previous').addClass('disabled');
    getData('');

    $('.cancelBtn').click(function () {
        if ($('[name="date-range"]').val() != '') {
            $('[name="date-range"]').val('');
            getData('');
        }
    });
});

function getData(date) {
    $("#form_grid").addClass('preloader-site');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: 'malfunctions/makegrid',
        type: 'POST',
        dataType: 'json',
        data: {
            date: date
        },
        success: function (data) {
            make_grid(data);
            $('.preloader-wrapper').fadeOut();
            $('#form_grid').removeClass('preloader-site');
        }
    });
}

function make_grid(data) {
    var source =
        {
            localdata: data,
            datatype: "json",
            datafields: [
                { name: 'nameCode', type: 'string' },
                { name: 'site', type: 'string' },
                { name: 'subsite', type: 'string' },
                { name: 'date', type: 'string' },
                { name: 'employee', type: 'string' },
                { name: 'score', type: 'string' },
                { name: 'status', type: 'string' },
                { name: 'employee_id', type: 'int' },
                { name: 'user_role', type: 'string' },
                { name: 'admin_changed_date', type: 'string' },
                { name: 'users', type: 'string' },
                { name: 'users_em', type: 'string' },
                { name: 'last_visit_date', type: 'string' },
                { name: 'last_comment_date', type: 'string' },
                { name: 'user', type: 'string' },
                { name: 'last_visit_date', type: 'string' }
            ]
        };
    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
        if (columnfield == 'nameCode') {
            return '<a href="https://pampuni.com/lac2/public/malfunctions/' + value.substring(4) + '/edit" style="float: right;padding: 9px 3px 0 0;">' + value + '</a>';
        }
        else if (columnfield == 'date') {
            return "<div style='float: right; padding-top: 8px; margin-right: 5px;'>" + value.substring(1) + "</div>";
        }
        else if (columnfield == 'status') {
            if (value != '' && ((value != 'draft' && (rowdata.user_role == 'admin' || rowdata.user_role == 'employee')) || value == 'publish' && (rowdata.user_role == 'client' || rowdata.user_role == 'contractor'))) {
                var checked = '', disabled = '';
                if (value != '' && value == 'publish') checked ='checked';
                if (rowdata.user_role != 'admin') disabled = 'disabled';
                return "<label class='toggle' style='margin:3px 5px 0 0; float: right;'><input type='checkbox' name='toggle-status' " + checked + " " + disabled + "><i data-swchon-text='{{__('ON')}}' data-swchoff-text='{{__('OFF')}}'></i></label>";
            }
            else if (value == '' || value == 'draft') {
                return "<img src='https://pampuni.com/lac2/public/img/draft.png' width=40 style='margin:3px 5px 0 0; float: right;'>";
            }
            else if (rowdata.user_role == 'admin' && rowdata.admin_changed_date != '' && rowdata.users != '' && rowdata.users_em != '' && strtotime(rowdata.last_visit_date) > strtotime(rowdata.admin_changed_date)) {
                return "<i class='fa fa-thumbs-up'></i>";
            }
            else if (value != '') {
                if (rowdata.last_comment_date != '' && rowdata.user == '' || strtotime(rowdata.last_visit_date) < strtotime(rowdata.last_comment_date)) {
                    return "<img src='https://pampuni.com/lac2/public/img/exclamation.png\' width=30>";
                }
            }
        }
    }
    var columns;
    if ($('#lang').val() == 'he') {
        columns = [
            { text: $('#status').val(), datafield: 'status', width: '15%', align: 'right', cellsalign: 'right', cellsrenderer: cellsrenderer },
            { text: $('#subsite').val(), datafield: 'subsite', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#site').val(), datafield: 'site', align: 'right', cellsalign: 'right', width: '10%' },
            { text: $('#score').val(), datafield: 'score', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#employee').val(), datafield: 'employee', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#date').val(), datafield: 'date', align: 'right', cellsalign: 'right', width: '15%', cellsrenderer: cellsrenderer },
            { text: $('#namecode').val(), datafield: 'nameCode', align: 'right', cellsalign: 'right', width: '15%', cellsrenderer: cellsrenderer }
        ]
    }
    else {
        columns = [
            { text: $('#namecode').val(), datafield: 'nameCode', align: 'right', cellsalign: 'right', width: '15%', cellsrenderer: cellsrenderer },
            { text: $('#date').val(), datafield: 'date', align: 'right', cellsalign: 'right', width: '15%', cellsrenderer: cellsrenderer },
            { text: $('#employee').val(), datafield: 'employee', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#score').val(), datafield: 'score', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#site').val(), datafield: 'site', align: 'right', cellsalign: 'right', width: '10%' },
            { text: $('#subsite').val(), datafield: 'subsite', align: 'right', cellsalign: 'right', width: '15%' },
            { text: $('#status').val(), datafield: 'status', width: '15%', align: 'right', cellsalign: 'right', cellsrenderer: cellsrenderer }
        ]
    }
    var dataAdapter = new $.jqx.dataAdapter(source);
    $("#form_grid").jqxGrid(
        {
            width: '100%',
            source: dataAdapter,
            pageable: true,
            autoheight: true,
            sortable: true,
            // filterable: true,
            altrows: true,
            // autoshowfiltericon: false,
            // autoshowcolumnsmenubutton: false,
            columns: columns
        });
    $('.jqx-grid-column-header.jqx-widget-header').each(function () {
        $(this).css('background-color', '#0074D9');
        $(this).css('color', 'white');
    });

    $('.iconscontainer').remove();
    $('.jqx-grid-column-menubutton.jqx-icon-arrow-down').parent().remove();

    $('.jqx-grid-column-header.jqx-widget-header').each(function () {
        $(this).find('div div').html('<img src="css/malfunctions/images/icon-arrow.png" class="icon-arrow">' + $(this).find('div div').html());
    });
    $('.jqx-grid-column-header.jqx-widget-header').attr('sort', '');
    $('.jqx-grid-column-header.jqx-widget-header div div').css('position', 'absolute');
    $('.jqx-grid-column-header.jqx-widget-header div div').css('width', '100%');
    $('.jqx-grid-column-header.jqx-widget-header div div').css('right', 0);
    $('.jqx-grid-column-header.jqx-widget-header').click(function () {
        $(this).attr('sort', '');
    });
    // $('.jqx-grid-column-header.jqx-widget-header div div').css('width', '125px');
    $('.jqx-grid-column-header.jqx-widget-header').each(function () {
        if ($(this).find('div div').html().indexOf($('#site').val()) != -1) {
            // $(this).find('div div').css('width', '80px');
            $(this).find('img').css('width', '12px');
        }
        if ($(this).find('div div').html().indexOf($('#subsite').val()) != -1) {
            // $(this).find('div div').css('width', '125px');
        }
    });

    $('#btn_next').show();
    $("#form_grid").on("pagechanged", function (event)
    {
        var args = event.args;
        var pagenum = args.pagenum;
        var pagesize = args.pagesize;
        if (!pagenum) {
            $('#btn_previous').addClass('disabled');
            $('#btn_next').removeClass('disabled');
        }
        else if (pagenum == pagesize + 2) {
            $('#btn_next').addClass('disabled');
            $('#btn_previous').removeClass('disabled');
        }
        else {
            $('#btn_previous').removeClass('disabled');
            $('#btn_next').removeClass('disabled');
        }
    });
    $('#btn_next').click(function (event) {
        event.preventDefault();
        $('.jqx-icon-arrow-right').parent().click();
    });
    $('#btn_previous').click(function (event) {
        event.preventDefault();
        $('.jqx-icon-arrow-left').parent().click();
    });

    $('label.toggle i').click(function () {
        $(this).css('left', '9991px');
        $(this).parent().find('input').css('opacity', 0);
    });

    $('.jqx-rc-all.jqx-button.jqx-widget.jqx-fill-state-normal').parent().find('div').addClass('align_left');
    $('.jqx-rc-all.jqx-button.jqx-widget.jqx-fill-state-normal').parent().find('div:eq(0)').css('margin-left', '30px');
    $('.jqx-rc-all.jqx-button.jqx-widget.jqx-fill-state-normal').parent().find('div:eq(0)').next().css('margin-left', '-60px');
    // if ($('#lang').val() == 'he')
    // alert($('.jqx-clear.jqx-position-absolute.jqx-grid-pager.jqx-widget-header').find('div:contains(לך לעמוד)').html())
}