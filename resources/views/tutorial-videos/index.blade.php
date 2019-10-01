@extends('layouts.app')

@section('content')
    <?php ?>

    <style>
        .tutorial-video-action-icon {
            cursor: pointer;
        }
        .user-type-select {
            width: 110px !important;
            margin-right: 10px;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Tutorial videos')}}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" border="1">
                            <thead style="background-color:#0074D9;color:white;">
                            <tr>
                                <th>#</th>
                                <th>{{__('Name')}}</th>
                                @if($is_admin)
                                    <th>{{__('Link')}}</th>
                                    <th>{{__('User types')}}</th>
                                    <th>{{__('Actions')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tutorials as $index => $row)
                                <tr id="{{ $index }}-fixed">
                                    <td @if(!$is_admin) width="3%" @endif>{{ $index + 1 }}</td>
                                    <td width="50%"><a href="{{ $row['link'] }}" target="_blank">{{ $row['name'] }}</a></td>
                                    @if($is_admin)
                                        <td style="max-width: 150px;">
                                            <div style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                                {{ $row['link'] }}
                                            </div>
                                        </td>
                                        <td>{{ implode(', ', $row['user_types']) }}</td>
                                        <td>
                                            <input type="hidden" class="clicked_tutorial_id">
                                            <i class="fa fa-share-alt share-tutorial tutorial-video-action-icon"
                                               onclick="showSharePopup('{{ $row['id'] }}', '{{ $row['name'] }}', '{{ $row['link'] }}')"></i>
                                            <i class='fa fa-pencil-alt tutorial-video-action-icon'
                                               onclick="setEditableRow('{{ $index }}')"></i>
                                            <i class='fa fa-trash-alt tutorial-video-action-icon'
                                               onclick="deleteTutorial('{{ $row['id'] }}')"></i>
                                        </td>
                                    @endif
                                </tr>
                                <tr id="{{ $index }}-editing" style="display: none">
                                    <td>{{ $index + 1 }}</td>
                                    <td><input id="{{ $row['id'] }}-name" type="text" value="{{ $row['name'] }}"></td>
                                    @if($is_admin)
                                        <td><input id="{{ $row['id'] }}-link" type="text" value="{{ $row['link'] }}">
                                        </td>
                                        <td>
                                            <select
                                                    id="{{ $row['id'] }}-user-types"
                                                    multiple
                                                    class="user-type-select">
                                                @foreach ([__('Admin'), __('Client'), __('Contractor'), __('Employee')] as $type)
                                                    <option value="{{ $type }}" @if(in_array($type, $row['user_types'])) selected @endif
                                                        @if($type === __('Admin')) selected disabled @endif>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <i class="fa fa-check tutorial-video-action-icon"
                                               onclick="updateTutorial('{{ $row['id'] }}')"></i>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if($is_admin)
                                <tr id="{{ sizeof($tutorials) }}-fixed">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <i class="fa fa-plus-circle tutorial-video-action-icon"
                                           onclick="setEditableRow('{{ sizeof($tutorials) }}')"></i>
                                    </td>
                                </tr>
                                <tr id="{{ sizeof($tutorials) }}-editing" style="display: none">
                                    <td>{{ sizeof($tutorials) + 1 }}</td>
                                    <td><input id="add-name" type="text"></td>
                                    @if($is_admin)
                                        <td><input id="add-link" type="text"></td>
                                        <td>
                                            <select
                                                    id="add-user-types"
                                                    multiple
                                                    class="user-type-select">
                                                @foreach ([__('Admin'), __('Client'), __('Contractor'), __('Employee')] as $type)
                                                    <option value="{{ $type }}"
                                                    @if($type === __('Admin')) selected disabled @endif>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <i class="fa fa-check tutorial-video-action-icon"
                                               onclick="addTutorial()"></i>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ url('/') }}/css/multiselect.css">
    <script src="{{url('/')}}/js/multiselect.js"></script>


    <script>
        const Lang = {
            "SelectAll": "{{__('Select all')}}",
            "AllSelected": "{{__('All selected')}}",
            "NoMatchesFound": "{{__('No matches found')}}",
            "Message sent successfully": "<?php echo e(__('Message sent successfully')); ?>"
        };

        $('select[multiple]').multipleSelect({
            selectAllText: Lang['SelectAll'],
            allSelected: Lang['AllSelected'],
            noMatchesFound: Lang['NoMatchesFound']
        });

        var op = document.querySelectorAll('[data-name]');
        for (var i = 0; i < op.length; i++) {
            if (op[i].getAttribute('data-name') === 'selectAll') {
                op[i].parentElement.remove();
            }
        }

        function showSharePopup(id, name, link) {
            $(".clicked_tutorial_id").val(id.toString());
            $("#message_subject").val(name.toString());
            $("#message_body").val(link.toString());

            $('.modal.sharing').addClass('show');
        }

        function sendFile() {
            var tutorial_id = $(".clicked_tutorial_id").val();
            var to_address = $("#to_address").val();
            var message_subject = $("#message_subject").val();
            var message_body = $("#message_body").val();

            if (to_address === '' || message_subject === '' || message_body === '')
                return;

            $('.modal.sharing').removeClass('show');

            let data = '';
            data += 'to_address=' + to_address + '&' + 'subject=' + message_subject + '&' + 'message=' + message_body;

            const onSuccess = () => {
                showAlert('success', Lang['Message sent successfully']);
            };

            sendRequest("POST", "tutorial/send-email", data, onSuccess);
        }

        function setEditableRow(index) {
            $('#' + index + '-fixed').css('display', 'none')
            $('#' + index + '-editing').css('display', 'table-row')
        }

        function addTutorial() {
            const data = dataMaker('add');
            sendRequest("POST", 'tutorial', data);
        }

        function updateTutorial(id) {
            const data = dataMaker(id);
            sendRequest("PUT", "tutorial/" + id, data)
        }

        function dataMaker(id) {
            let data = '';
            const name = $('#' + id + '-name').val();
            if (name) {
                data += 'name=' + name;
            }
            const link = $('#' + id + '-link').val();
            if (link) {
                if (data) {
                    data += '&';
                }
                data += 'link=' + link;
            }
            let types = $('#' + id + '-user-types').val();

            if (types.length > 0) {
                types = types + ',????';
            } else {
                types = '????';
            }

            if (data) {
                data += '&';
            }
            data += 'user_types=' + types;
            return data;
        }

        function deleteTutorial(id) {
            swal({
                title: "{{__('Are you sure you want to delete it?')}}",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{__('Yes, delete it(site linking)')}}",
                cancelButtonText: "{{__('No, cancel(site linking)')}}"
            }).then((result) => {
                if (result.value) {
                    sendRequest("DELETE", "tutorial/" + id);
                }
            })
        }

        function sendRequest(method, url, data, onSuccess) {
            let finalData = "_token=<?php echo e(csrf_token()); ?>";
            if (data) {
                finalData += '&' + data;
            }

            $('#loadingBlock').fadeIn(100);
            $.ajax({
                type: method,
                url: url,
                dataType: "html",
                data: finalData,
                success: function (response) {
                    $('#loadingBlock').fadeOut();
                    onSuccess ? onSuccess() : window.location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#loadingBlock').fadeOut();
                    window.location.reload();
                }
            });
        }
    </script>
@stop
