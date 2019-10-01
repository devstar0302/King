$(document).ready(function(){
    $('#btn_previous').hide();
    $('#btn_next').hide();
    getData('');
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
                return "<label class='toggle' style='margin:3px 5px 0 0; float: right;'><input type='checkbox' name='toggle-status' " + checked + " " + disabled + "><i data-swchon-text='{{__('ON')}}' data-swchoff-text='{{__('OFF')}}'></i></i></label>";
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
            filterable: true,
            altrows: true,
            columns: columns
        });
    $('.jqx-grid-column-header.jqx-widget-header').each(function () {
        $(this).css('background-color', '#0074D9');
        $(this).css('color', 'white');
    });

    $('#btn_next').show();
    $("#form_grid").on("pagechanged", function (event)
    {
        var args = event.args;
        var pagenum = args.pagenum;
        var pagesize = args.pagesize;
        if (!pagenum) {
            $('#btn_previous').hide();
            $('#btn_next').show();
        }
        else if (pagenum == pagesize + 2) {
            $('#btn_next').hide();
            $('#btn_previous').show();
        }
        else {
            $('#btn_previous').show();
            $('#btn_next').show();
        }
    });
    $('#btn_next').click(function () {
        $('.jqx-icon-arrow-right').parent().click();
    });
}