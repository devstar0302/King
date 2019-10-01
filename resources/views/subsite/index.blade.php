@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Users management')}}</h3>
                        <div class="btn-group">
                            <a href="{{ route('users.create') }}" class="btn-action">{{__('Add new user')}}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table id="users-table" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Role')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->role->title }}</td>
                                        <td style="display: flex;
    align-items: center;">
                                            <a href="{{ route('users.edit', $user->id) }}">{{__('Edit')}}</a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                                {{ method_field('delete') }}
                                                @csrf
                                                <button class="btn btn-default" type="submit">{{__('Delete')}}</button>
                                            </form>
                                        </td>
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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script>
      $(document).ready(function() {
        $('#users-table').DataTable();
      } );
    </script>
@endsection
