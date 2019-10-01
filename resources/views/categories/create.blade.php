@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Categories')}}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ action('CategoryController@store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{__('Category name')}}</label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="score">{{__('Score')}}</label>
                                <input type="number" min="0" class="form-control" name="score" />
                            </div>
                            <div class="form-group">
                                @if($paragraphs->count())
                                    <label for="paragraph">{{__('Select Paragraph')}}</label>
                                    @foreach($paragraphs as $paragraph)
                                        <div class="form-group">
                                            <input type="checkbox" name="paragraph[{{$paragraph->id }}]" value="paragraph[{{
                                            $paragraph->id }}]" />
                                            {{ $paragraph->finding }} / {{ $paragraph->risk
                                            }} / {{ $paragraph->finding }} / {{ $paragraph->repair }}
                                        </div>
                                    @endforeach
                                    @else
                                        <h3 class="text-danger">{{__('No paragraphs stored!')}}</h3>
                                    @endif
                            </div>
                            <button type="submit" class="btn btn-success btn-block">{{__('Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
