@extends('layouts.frontend')

@section('content')
<div class="container">
    @if($sessions->count() > 0)
    <div class="row justify-content-center">
    <div class="col-md-4">
        @component('components.home-left-nav', ['performance'=>$performance])@endcomponent
    </div> 
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><strong>{!! $chart8->options['chart_title'] !!}</strong></h5>
                </div>
                <div class="card-body">
        <div class="{{ $chart8->options['column_class'] }}">
                            {!! $chart8->renderHtml() !!}
                        </div>
        </div></div>

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
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>{!! $chart8->renderJs() !!}{!! $chart8->renderJs() !!}

    <script>
        $(document).ready(function(){
            $('.progress-bar').each(function(){
                var width = $(this).attr('aria-valuenow');
                $(this).css('width', width + '%');
            });
        });
    </script>
@endsection

