@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center text-primary">{{__('Paragraphs')}}</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if($paragraphs->count())
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <td>{{__('Name')}}</td>
                                    <td>{{__('Score')}}</td>
                                    <td>{{__('Finding')}}</td>
                                    <td>{{__('Risk')}}</td>
                                    <td>{{__('Repair')}}</td>
                                    <td>{{__('Type')}}</td>
                                    <td>{{__('Actions')}}</td>
                                </tr>

                                @foreach($paragraphs as $paragraph)
                                    <tr>
                                        <td>{{ $paragraph->name }}</td>
                                        <td>{{ $paragraph->score }}</td>
                                        <td>{{ implode(';',$paragraph->finding) }}</td>
                                        <td>{{ $paragraph->risk }}</td>
                                        <td>{{ $paragraph->repair }}</td>
                                        <td>{{ $paragraph->type }}</td>
                                        <td>
                                            <a href="{{ action('ParagraphController@edit', $paragraph->id) }}"><i class="fa fa-edit"></i> </a>
                                            <a  href="{{ action('ParagraphController@destroy', $paragraph->id) }}"><i class="fa fa-trash text-danger"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </thead>
                            </table>
                                <form action="{{ action('ParagraphController@store') }}" method="POST">
                                    @csrf
                                <div class="row">
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Name" name="name" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Score" name="score" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Finding" name="finding" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Risk" name="risk" class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" placeholder="Repair" name="repair"
                                                   class="form-control" />
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" name="type">
                                                <option value="normal">{{__('normal')}}</option>
                                                <option value="principal">{{__('principal')}}</option>
                                                <option value="severe">{{__('severe')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 offset-md-10" style="margin-top:15px;">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa
                                            fa-save"></i> </button>
                                        </div>
                                </div>
                            </form>
                        @else
                            <h3 class="text-center text-danger">{{__('No data in paragraphs table')}}</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
