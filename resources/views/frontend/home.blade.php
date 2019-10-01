@extends('layouts.frontend')

@section('content')


@include('frontend._part._signs_forms_with_upload_button')
@include('frontend._part._modals')

@endsection

@section('scripts')
    <script>
        var SF_TOKEN = "{{ csrf_token() }}",
            SF_TIMESTAMP = "<? echo time();?>",
            SF_CURRENT_URL = "{{ $_SERVER['REQUEST_URI'] }}",
            SF_UPLOAD_ITEM_URL = "{{url("/")}}/nik/upload",
            SF_RESORTING_URL = "{{url('/')}}/nik/newsort",
            SF_UPDATE_CONTENT_URL = "{{url('/')}}/nik/newArea",
            SF_DELET_ITEM_URL = "{{url('/')}}/nik/delete",
            SF_SEND_EMAIL_URL = "{{url('/')}}/nik/sendmail";
    </script>

    <script>
        var sortTextArray = {
            "date_desc": "Upload date DESC",
            "date_asc" : "Upload date ASC",
            "type_asc" : "Type A-Z",
            "type_desc": "Type Z-A",
            "name_asc" : "Name A-Z",
            "name_desc": "Name Z-A"
        };

        var Lang = {
            "Are you sure you want to delete it?" : "{{__('Are you sure you want to delete it?')}}",
            "Yes" : "{{__('Yes')}}",
            "Cancel(no)" : "{{__('Cancel(no)')}}",
            "Message sent successfully" : "{{__('Message sent successfully')}}",
            "Email was not sent" : "{{__('Email was not sent')}}",
        }

        @if(Session::has('sort'))
            $(document).ready(function () {
                $('.item.sortBlock').find('span').text(sortTextArray["{{ Session::get('sort') }}"]);
            });
        @endif

        @if(Session::has('success'))
            $(document).ready(function () {
                alert('{{ Session::get("success") }}');
            });
        @endif
    </script>

    <script src="{{url('/')}}/js/frontend/uploadiFive/jquery.uploadifive.min.js"></script>
    <script src="{{url('/')}}/js/frontend/signs-forms.js"></script>

@endsection

@section('styles')
    <link rel="stylesheet" href="{{url('/')}}/js/frontend/uploadiFive/uploadifive.css">
@endsection
