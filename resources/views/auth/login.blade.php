@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <!-- <div class="card-header">{{ __('Login') }}</div> -->
                <div class="card-header">
                    <h3 class="text-center text-primary">{{ __('Login to your account') }}</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <!-- <label for="email" class="col-sm-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Email address') }}</label> -->

                            <div class="col-md-12">
                                <input id="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" autofocus>

                                <!-- @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <!-- <label for="password" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Password') }}</label> -->

                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" name="password">

                                <!-- @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Keep me logged in') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-link {{app()->getLocale() == 'he' ? 'text-left float-left' : 'text-right float-right'}} p-0" href="{{ route('password.request') }}">
                                    {{ __('Forgot password') }}
                                </a>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100 mt-4">
                                    {{ __('Login') }}
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
