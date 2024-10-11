@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
            <div class="list-group">
                    <a href="{{ route('frontend.home') }}" class="list-group-item list-group-item-action">Home</a>
                    <a href="{{ route('frontend.sessions.create') }}" class="list-group-item list-group-item-action"> {{ trans('global.new') }} {{ trans('cruds.session.fields.recording') }}</a>
                    <a href="{{ route('frontend.sessions.index') }}" class="list-group-item list-group-item-action">Meetings</a>
                </div>
            </div>
        </div>
    </div> 
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <!--Create a list of all meetings with name, date, and time and link to the meeting page-->


                  
                    <ul class="list-group">
                        @foreach($sessions as $session)
                            <li class="list-group-item list-group-item-action">
                                <a href="{{ route('frontend.session.recorder', $session->id) }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <p class="mb-1">{{ $session->name }}
                                        <span style="display:block;" class="small">{{ $session->status }} </span>
                                        </p>
                                        
                                        <span class="small">{{ $session->created_at->diffForHumans() }} </span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection