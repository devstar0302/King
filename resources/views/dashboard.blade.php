@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center text-primary">{{__('Dashboard')}}</h3>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                            Users managment
                        </a>
                        <a href="{{ route('companies.index') }}" class="btn btn-primary">
                            Site linking
                        </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
