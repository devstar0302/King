@extends('layouts.app')

@section('content')
<div class="container">
    <div >
        <div class="col-md-8">
            <h1 class='home-title'>{{__('Main menu')}}</h1>
            <div class="row">
                <div class="@if (!strcmp($user_role, 'admin')) col-md-6 @else col-md-12 @endif">
                    <h3 class="home-sub-title">{{__('Reports')}}</h3>
                    <div class="home-link-item"><a href="malfunctions">{{__('Form list')}}</a></div>
                    <div class="home-link-item"><a href="statistics">{{__('Statistics')}}</a></div>
                    @if (!strcmp($user_role, 'admin'))
                        <div class="home-link-item"><a href="categories">{{__('Category management')}}</a></div>
                    @endif
                    <div class="home-link-item"><a href="nik">{{__('Signs&forms')}}</a></div>
                    <div class="home-link-item"><a href="tutorial-videos">{{__('Tutorial videos')}}</a></div>
                </div>
                @if (!strcmp($user_role, 'admin'))
                    <div class="col-md-6">
                        <h3 class="home-sub-title">{{__('Users')}}</h3>
                        <div class="home-link-item"><a href="users">{{__('Users management')}}</a></div>
                        <div class="home-link-item"><a href="companies">{{__('Site linking')}}</a></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
