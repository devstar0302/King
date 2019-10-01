@extends('layouts.app')

<style>
    .hide-save {
        display: none !important;
    }
</style>

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Users management')}}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <a href="{{ route('users.create') }}" class="btn-blue">{{__('Add new user')}}</a>
                        <table id="users-table" class="display responsive no-wrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>{{__('Actions')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Password')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('User type')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr id="{{ $user->id }}">
                                    <td style="display: flex; align-items: center;">
                                        <button class="btn btn-default mail-btn" onclick="mailUser({{ $user->id }})"><i
                                                    class="far fa-envelope"></i></button>
                                        <button class="btn btn-default edit-btn"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        <button class="btn btn-success update-btn hide-save">{{__('Save')}}</button>
                                    </td>
                                    <td data-title="name">{{ $user->name }}</td>
                                    <td data-title="pwd">{{ $user->orig }}</td>
                                    <td data-title="email">{{ $user->email }}</td>
                                    <td data-title="role"
                                        data-roleid="{{ $user->role->id }}">{{ __($user->role->title) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#users-table').DataTable({
                responsive: true,
                "oLanguage": {
                    "oAria": {
                        "sSortAscending": "{{__('sorting ascending')}}",
                        "sSortDescending": "{{__('sorting descending')}}"
                    },
                    "oPaginate": {
                        "sFirst": "{{__('First')}}",
                        "sLast": "{{__('Last')}}",
                        "sNext": "{{__('Next')}}",
                        "sPrevious": "{{__('Previous')}}"
                    },
                    "sEmptyTable": "{{__('No data available in table')}}",
                    "sLengthMenu": "{{__('show _MENU_ entries')}}",
                    "sLoadingRecords": "{{__('Loading...')}}",
                    "sProcessing": "{{__('Processing...')}}",
                    "sSearch": "{{__('Search')}}",
                    "sZeroRecords": "{{__('No matching records found')}}",
                    "sInfo": "{{__('Showing _START_ to _END_ of _TOTAL_ entries')}}",
                    "sInfoEmpty": "{{__('No entries to show')}}",
                    "sInfoFiltered": "{{__('filtered from _MAX_ total records')}}"
                }
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".update-btn").on('click', function () {
            let id = $(this).parent().parent().attr("id");
            let pwdEl = $("#" + id + " > *[data-title='pwd']");
            let emailEl = $("#" + id + " > *[data-title='email']");
            let nameEl = $("#" + id + " > *[data-title='name']");
            let roleEl = $("#" + id + " > *[data-title='role']");
            let pwd = $("#" + id + " > *[data-title='pwd'] > input[name='pwd']").val();
            let email = $("#" + id + " > *[data-title='email'] > input[name='email']").val();
            let name = $("#" + id + " > *[data-title='name'] > input[name='name']").val();
            let role = $("#" + id + " > *[data-title='role'] > select[name='role']").val();
            let data = {
                'id': id,
                'pwd': pwd,
                'email': email,
                'name': name,
                'role': role
            };

            $.ajax({
                type: 'PUT',
                url: "{!! route('users.update', ['id' => '']) !!}" + "/" + id,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    "_token": "{{ csrf_token() }}",
                    "pwd": data.pwd,
                    "email": data.email,
                    "name": data.name,
                    "role": data.role
                },

                success: function (data) {
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: "{{__('Updated!(data saved)')}}",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function (data) {
                    swal({
                        title: "{{__('Error!')}}",
                        type: 'error',
                        confirmButtonText: "{{__('Ok')}}"
                    })
                },
                finally: function () {

                }
            });

            pwdEl.html(data.pwd);
            emailEl.html(data.email);
            nameEl.html(data.name);
            roleEl.html(data.role);
            $(this).addClass('hide-save')
        });

        $(".edit-btn").on('click', function () {
            let id = $(this).parent().parent().attr("id");
            let pwdEl = $("#" + id + " > *[data-title='pwd']");
            let emailEl = $("#" + id + " > *[data-title='email']");
            let nameEl = $("#" + id + " > *[data-title='name']");
            let roleEl = $("#" + id + " > *[data-title='role']");
            let data = {
                'id': id,
                'pwd': pwdEl.html(),
                'email': emailEl.html(),
                'name': nameEl.html(),
                'role': roleEl.html(),
            };
            pwdEl.html("<input type='text' name='pwd' value='" + data.pwd + "'>");
            emailEl.html("<input type='email' name='email' value='" + data.email + "'>");
            nameEl.html("<input type='text' name='name' value='" + data.name + "'>");

            //Create array of options to be added
            let roles = ["מנהל", "לקוח", "קבלן", "עובד"];

            //Create and append select list
            var selectList = document.createElement("select");
            selectList.setAttribute("name", "role");
            roleEl.html(selectList);

            //Create and append the options
            for (let i = 0; i < roles.length; i++) {
                let option = document.createElement("option");
                option.setAttribute("value", roles[i]);
                if (data.role === roles[i]) {
                    option.setAttribute("selected", "selected");
                }
                option.text = roles[i];
                selectList.appendChild(option);
            }

            $(this).parent().find('.update-btn').removeClass('hide-save')
        });

        function deleteUser(id) {
            swal({
                title: "{{__('Are you sure?')}}",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{__('Yes')}}",
                cancelButtonText: "{{__('Cancel(no)')}}"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE',
                        url: "{!! route('users.destroy', ['id' => '']) !!}" + "/" + id,
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {"_token": "{{ csrf_token() }}"},

                        success: function (data) {
                            $("#" + id).remove();
                            swal({
                                title: "{{__('Deleted!')}}",
                                type: 'success',
                                confirmButtonText: "{{__('Ok')}}"
                            })
                        },
                        error: function (data) {
                            swal({
                                title: "{{__('Error!')}}",
                                type: 'error',
                                confirmButtonText: "{{__('Ok')}}"
                            })
                        }
                    });
                }
            })
        }

        function mailUser(id) {
            $(".mail-btn").attr("disabled", true);
            $.ajax({
                type: 'GET',
                url: "{!! route('send-cred', ['id' => '']) !!}" + "/" + id,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {"_token": "{{ csrf_token() }}"},

                success: function (data) {
                    swal({
                        position: 'top-end',
                        type: 'success',
                        title: "{{__('Mail was sent')}}",
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $('.mail-btn').removeAttr('disabled');
                },
                error: function (data) {
                    swal({
                        title: "{{__('Error!')}}",
                        type: 'error',
                        confirmButtonText: "{{__('Ok')}}"
                    })
                    $('.mail-btn').removeAttr('disabled');
                }
            });
        }
    </script>
@endsection
