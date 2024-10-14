@extends('layouts.frontend')

@section('content')
<div class="container">
    @if($sessions->count() > 0)
    <div class="row justify-content-center">
    <div class="col-md-4">
        @component('components.home-left-nav', ['performance'=>$performance])@endcomponent
    </div> 
        <div class="col-md-8">
          @component('components.session-list',  ['sessions'=>$sessions])@endcomponent
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