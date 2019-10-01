@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Add new paragraph to category')}}: <b>{{ $category->name }} </b></h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ action('ParagraphController@store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="score">{{__('Score')}}</label>
                                <input type="number" min="0" max="100" name="score" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="finding">{{__('Finding')}}</label>
                                <input type="text" name="finding" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="risk">{{__('Risk')}}</label>
                                <input type="text" name="risk" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="repair">{{__('Repair')}}</label>
                                <input type="text" name="repair" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="type">{{__('Type')}}</label>
                                <select class="form-control" name="type">
                                    <option value="normal">{{__('normal')}}</option>
                                    <option value="principal">{{__('principal')}}</option>
                                    <option value="severe">{{__('severe')}}</option>
                                </select>
                            </div>
                            <input type="hidden" value="{{ $category->id }}" name="category_id" />
                            <button type="submit" class="btn btn-success btn-block">{{__('SAVE')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
