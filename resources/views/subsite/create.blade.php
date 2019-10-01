@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Add new company</a>')}}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form method="POST" action="{{ route('users.store') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="role" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{__('Role')}}</label>

                                    <div class="col-md-6">
                                        <select name="role" id="role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('role'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" autofocus>

                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Create
                                        </button>
                                    </div>
                                </div>
                            </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

