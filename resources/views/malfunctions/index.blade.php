@extends('layouts.app')

@section('content')
    <?php
        $user_role = strtolower(auth()->user()->role->title);
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Malfunctions list')}}</h3>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @php
                            $mPaginateClass = 'pull-right';

                            if (app()->getLocale() == 'he') {
                                $mPaginateClass = 'pull-left';
                            }
                        @endphp
                        <div class="row" style="padding: 0 15px 10px 15px; text-align: left;height: 45px;">
                            <div class="" style="position: absolute;left: 20px;">
                                <input type="text" class="form-control" name="date-range" value="" placeholder="{{__('Select date range')}}" style="height: 33px;">
                            </div>
                            <div>
                                @if ( $user_role == 'admin' || $user_role == 'employee')
                                    <a href="{{ action('MalfunctionController@create') }}" class="btn-blue"><i class="fa fa-plus-circle"></i> {{__('New form')}}</a>
                                @endif
                            </div>
                            <div>
                                @if ( $user_role == 'admin' || $user_role == 'employee')
                                    <a href="{{ action('MalfunctionController@createGuidance') }}" class="btn-blue"><i class="fa fa-plus-circle"></i> {{__('New guidance day')}}</a>
                                @endif
                            </div>
{{--                            <div class="col-3" style="padding:0; margin-right: 20px; text-align: left;">--}}
{{--                                <a href="javascript;" class="btn-blue btn_con" id="btn_next">{{ __('Next') }}</a>--}}
{{--                                <a href="javascript;" class="btn-blue btn_con" id="btn_previous">{{ __('Previous') }}</a>--}}
{{--                            </div>--}}
                        </div>

                        <div id="form_grid">

                        </div>
                        <div class="preloader-wrapper">
                            <div class="preloader">
                                <img src="{{ url('/') }}/css/malfunctions/images/preloader.gif" alt="NILA">
                            </div>
                        </div>
                            <input type="hidden" id="namecode" value="{{__('Form')}}">
                            <input type="hidden" id="status" value="{{__('Status')}}">
                            <input type="hidden" id="site" value="{{__('Site')}}">
                            <input type="hidden" id="subsite" value="{{__('Sub-site')}}">
                            <input type="hidden" id="employee" value="{{__('Employee')}}">
                            <input type="hidden" id="score" value="{{__('Score')}}">
                            <input type="hidden" id="date" value="{{__('Date')}}">
                            <input type="hidden" id="lang" value="{{app()->getLocale()}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/jqx.base.css">

    @if (app()->getLocale() == 'he')
        <link rel="stylesheet" href="{{ url('/') }}/css/malfunctions/main_rtl.css" media="all">
    @endif

    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jquery-1.11.1.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{url('/')}}/js/stupidtable.min.js"></script>

    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxcore.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxdata.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxbuttons.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxscrollbar.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxmenu.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxcheckbox.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxlistbox.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.sort.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.pager.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.edit.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxpanel.js"></script>
    <script type="text/javascript" src="{{url('/')}}/js/malfunctions/jqxgrid.filter.js"></script>

    <script type="text/javascript">
        $('[name="date-range"]').daterangepicker({
            opens: 'left',
            language: 'he',
            autoUpdateInput: false,
            locale: {
                applyLabel: "{{__('Apply')}}",
                cancelLabel: "{{__('Clear')}}",
                daysOfWeek: ["{{__('Su')}}", "{{__('Mo')}}", "{{__('Tu')}}", "{{__('We')}}", "{{__('Th')}}", "{{__('Fr')}}", "{{__('Sa')}}"],
                monthNames: [ "{{__('January')}}", "{{__('February')}}", "{{__('March')}}", "{{__('April')}}", "{{__('May')}}", "{{__('June')}}", "{{__('July')}}", "{{__('August')}}", "{{__('September')}}", "{{__('October')}}", "{{__('November')}}", "{{__('December')}}"],
                firstDay: 1
            },
            onSelect: function(dateText) {
                $('[name="date-range"]').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
            }
        }, function(start, end, label) {
            $('[name="date-range"]').val(start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
            getData(start.format('YYYY-MM-DD') + '/' + end.format('YYYY-MM-DD'));
        });


    </script>
    <script src="{{url('/')}}/js/malfunctions/index.js"></script>

@stop