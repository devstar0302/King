@extends('layouts.frontend')

@section('content')
    <div id="printableArea">
        <iframe id="iframeprint" style="overflow:hidden;height:100vh;width:100%" src="{{url('/')}}/uploads/frontend/{{ $file->filename }}"></iframe>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function($) {
            $('#app nav').css('display', 'none');
            $('main').css('padding', '0 !important');

            function print() {
                $('#iframeprint').on("load", function() {
                    callPrint('iframeprint');
                });
            }

            //initiates print once content has been loaded into iframe
            function callPrint(iframeId) {
                var PDF = document.getElementById(iframeId);
                PDF.focus();
                PDF.contentWindow.print();
            }

            print();
        });
    </script>
@endsection