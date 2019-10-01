@extends('layouts.companies')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Add new company')}}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form method="POST" action="{{ route('companies.store') }}">
                                @csrf

                                <div class="form-group row">
                                    <label for="site" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{__('Site')}}</label>

                                    <div class="col-md-6">
                                        <select name="site" id="site" class="form-control{{ $errors->has('site') ? ' is-invalid' : '' }}">
                                            @foreach($sites as $site)
                                                <option value="{{ $site->id }}">{{ $site->title }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('site'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('site') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label {{app()->getLocale() == 'he' ? 'text-md-left' : 'text-md-right'}}">{{ __('Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" autofocus>

                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
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

