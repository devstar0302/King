@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                      <h3 class="text-center text-primary">{{__('Add new user')}}</h3>
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
                                            <option value="{{ $role->id }}">{{ __($role->title) }}</option>
                                        @endforeach
                                    </select>
                                    <!-- @if ($errors->has('role'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                                    @endif -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" autofocus>
                                    <!-- @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Email address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                    <!-- @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif -->
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Password') }}</label>
                                <div class="col-md-6">
                                    <input id="password" type="text" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                                    <a href='javascript:void(0)' id='gen_password' class="btn-blue">{{__('Generate password')}}</a>
                                </div>
                                <!-- @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif -->
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
    $('#gen_password').click(function() {
        $.ajax({
            type: 'get',
            url: "{{ route('gen-password') }}",
            data: {_token: "{{ csrf_token() }}" },
            success: function (data) {
                $('#password').val(data.password);
            }
        });
    });
</script>

@endsection
