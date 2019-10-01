@extends('layouts.app')

@section('content')

    <div id="statistics_page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card statistics" id="pdf-container">
                        <div class="card-header">
                            <h3 class="text-center text-primary">{{__('Statistics')}}</h3>
                            <div class="btn-group {{app()->getLocale() == 'he' ? 'left' : 'right'}}">
                                <a href="javascript:void(0);" class="btn-action" id="download_pdf" title="download chart"><img src="{{url('/')}}/img/frontend/action/download.png" width=25 height=25></a>
                                <a href="javascript:void(0);" class="btn-action" id="share_pdf" title="share pdf"><img src="{{url('/')}}/img/frontend/action/share.png" width=25 height=25></a>
                                <a href="javascript:void(0);" class="btn-action" title="print pdf" onclick="print();"><img src="{{url('/')}}/img/frontend/action/print.png" width=25 height=25></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="statistics-chart">
                                <form id="statistics-form">
                                    {{ csrf_field() }}

                                    <div class="form-header">
                                        <select id="site" name="site" multiple>
                                            @foreach($sites as $site)
                                                <option class='site-item' value="{{$site->title}}" data-has-subsites={{$site->has_subsites}}>{{$site->title}}</option>
                                            @endforeach
                                        </select>

                                        <select id="subsite" name="subsite" multiple>
                                        </select>

                                        <a href="javascript:void(0)" class='{{app()->getLocale() == "he" ? "float-left" : "float-right"}} btn-default btn-apply' id='apply'>{{__('Apply')}}</a>

                                        <input id="selected_type" type="hidden">
                                        <input id="paragraph_id" type="hidden">
                                        <input id="category_id" type="hidden">
                                        <input id="paragraph_id" type="hidden">

                                        <input id="statistic_types" class='{{app()->getLocale() == "he" ? "float-left" : "float-right"}} comboTreeWrapper' name="statistic_type" value='{{__("Statistics type")}}' readonly>

                                        <input id='date-range' class='{{app()->getLocale() == "he" ? "float-left" : "float-right"}} date-range' type="text" name="date-range" value="{{ isset($date_range) ? $date_range : '' }}" placeholder="{{__('Select date range')}}" readonly>
                                    </div>
                                </form>

                                <p class='site-names'></p>
                                <div id="chartContainer" style="height:370px; width:90%; margin: 20px auto; direction:ltr;"></div>
                                <div class='site-linecolors'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.alert')

@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"  media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/multiselect.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/comboTreePlugin.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/statistics/index.css" media="all">
    <link rel="stylesheet" href="{{ url('/') }}/css/statistics/print.css" media="print">

    @if (app()->getLocale() == 'he')
        <link rel="stylesheet" href="{{ url('/') }}/css/statistics/index_rtl.css" media="all">
    @endif

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{url('/')}}/js/multiselect.js"></script>
    <script src="{{url('/')}}/js/icontains.js"></script>
    <script src="{{url('/')}}/js/comboTreePlugin.js"></script>
    <script src="{{url('/')}}/js/canvasjs.min.js"></script>
    <script src="{{url('/')}}/js/pdf/jspdf.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2canvas.min.js"></script>
    <script src="{{url('/')}}/js/pdf/html2pdf.js"></script>

    <script type="text/javascript">
        var g_SitesSubsites = {!! json_encode($sites_subsites) !!},
            g_StatisticTypes = {!! json_encode($statistic_types) !!},
            GET_STATISTICS_URL = "<?php echo e(route('get-statistics')); ?>",
            SEND_PDF_URL = "<?php echo e(route('statisticSharePdf')); ?>",
            TOKEN = "{{ csrf_token() }}";

        var Lang = {
            "Site" : "{{__('Site')}}",
            "Sub-site" : "{{__('Sub-site')}}",
            "Score" : "{{__('CP Score')}}",
            "Number" : "{{__('Number')}}",
            "Date" : "{{__('Date')}}",
            "Print" : "{{__('Print')}}",
            "Message sent successfully" : "{{__('Message sent successfully')}}",
            "Email was not sent" : "{{__('Email was not sent')}}",
            "SelectAll" : "{{__('Select all')}}",
            "AllSelected" : "{{__('All selected')}}",
            "NoMatchesFound" : "{{__('No matches found')}}"
        };

        var risk_values = {0: '', 1: "{{__('Low')}}", 2: "{{__('Medium')}}", 3: "{{__('High')}}", 4: ''};
        var service_values = {0: "{{__('N/A')}}", 1: "{{__('Bad')}}", 2: "{{__('Good')}}", 3: "{{__('Very good')}}", 4: ''};

        $('[name="date-range"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                // direction: "{{app()->getLocale() == 'he' ? "rtl" : "ltr"}}",
                applyLabel: "{{__('Apply')}}",
                cancelLabel: "{{__('Clear')}}",
                daysOfWeek: ["{{__('Su')}}", "{{__('Mo')}}", "{{__('Tu')}}", "{{__('We')}}", "{{__('Th')}}", "{{__('Fr')}}", "{{__('Sa')}}"],
                monthNames: [ "{{__('January')}}", "{{__('February')}}", "{{__('March')}}", "{{__('April')}}", "{{__('May')}}", "{{__('June')}}", "{{__('July')}}", "{{__('August')}}", "{{__('September')}}", "{{__('October')}}", "{{__('November')}}", "{{__('December')}}"],
                firstDay: 1
            }
        }, function(start, end, label) {
            $('#date-range').val(end.format('YYYY-MM-DD') + '  ~  ' + start.format('YYYY-MM-DD'));
        });
    </script>

    <script src="{{asset('js/statistics/index.js')}}"></script>
@stop
