@extends('layouts.frontend')

@section('content')
<div class="container">
    @if($sessions->count() > 0)
    <div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
            <div class="list-group">
                    <a href="{{ route('frontend.home') }}" class="list-group-item list-group-item-action">Home</a>
                    <a href="{{ route('frontend.sessions.create') }}" class="list-group-item list-group-item-action"> {{ trans('global.new') }} {{ trans('cruds.session.fields.recording') }}</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
            {{ trans('global.performance') }}
            </div>
            <div class="card-body">
            <p>You've completed {{ number_format($performance ?? 0, 1) }}% of your tasks.</p>
            <div class="progress mt-2 mb-2">
                    <div class="progress-bar progress-bar-striped @if($performance > 33.33 && $performance < 66.66) bg-warning @elseif($performance >= 66.66) bg-success @else bg-danger @endif" role="progressbar" aria-valuenow="{{ $performance ?? '0' }}" aria-valuemin="0" aria-valuemax="100%"></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                You have some tasks to do!
            </div>
            <div class="card-body">
            <p>Assigned to you : {{ $assigned_count }}</p>
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
                    <div class="row d-none d-md-flex">
                                        <div class="mb-1 col-md-5 col-sm-12">{{ trans('cruds.session.custom.name') }}</div>
                                        <div class="small col-sm-12 col-md-3">{{ trans('cruds.session.custom.status') }}</div> 
                                        <div class="col-md-3 col-sm-12 small">{{ trans('cruds.session.custom.date_created') }}</div>
                                        <div class="col-md-1 col-sm-12 small">
                                        </div>
                                    </div>
                        @foreach($sessions as $session)
                            <li class="list-group-item list-group-item-action">
                                <a href="{{ route('frontend.session.recorder', $session->id) }}">
                              <div class="row">
                              <div class="mb-1 col-md-12 col-sm-12">{{ $session->name }}</div>
                              </div>
                                    <div class="row">
                                        <div class="mb-1 col-md-5 col-sm-12 small">@if($session->todos_pending==0) {{ trans('cruds.session.custom.todo_completed') }}  @else <i class="fas fa-warning gold"></i> {{ $session->todos_pending ?? 'No' }} {{ trans('cruds.session.custom.todo_pending') }} @endif</div>
                                        <div class="small col-sm-12 col-md-3">{{ $session->status }} </div> 
                                        <div class="col-md-3 col-sm-12 small">{{ $session->created_at->diffForHumans() }} </div>
                                        <div class="col-md-1 col-sm-12 small">
                                        @can('session_delete')
                                                <form action="{{ route('frontend.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="trash" value="{{ trans('global.delete') }}"><i class="fas fa-trash"></i></button>
                                                </form>
                                            @endcan
                                            </div>
                                    </div>
                                </a>
                                <div class="progress mt-2 mb-2">
                                <div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="{{ ($session->todos_completed/$session->todos_total)*100 }}" aria-valuemin="0" aria-valuemax="100%"></div>
                                </div>
                                                            
                            </li>
                        @endforeach
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-header">{{ __('Dashboard') }}</div>
        <div class="card-body">
            <p>{{ __('You have no tasks assigned to you.') }}</p>
        </div>
    @endif
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('.progress-bar').each(function(){
                var width = $(this).attr('aria-valuenow');
                $(this).css('width', width + '%');
            });
        });
    </script>
@endsection