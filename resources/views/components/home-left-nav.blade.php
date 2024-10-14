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
            <p>You've completed {{ $performance['total_completed'] ?? 0 }} out of {{ $performance['total_todos'] ?? 0 }} tasks.</p>
            <div class="progress mt-2 mb-2">
                    <div class="progress-bar progress-bar-striped @if($performance['total_completed'] / $performance['total_todos'] > 0.3333 && $performance['total_completed'] / $performance['total_todos'] < 0.6666) bg-warning @elseif($performance['total_completed'] / $performance['total_todos'] >= 0.6666) bg-success @else bg-danger @endif" role="progressbar" aria-valuenow="{{ ($performance['total_completed'] / $performance['total_todos']) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                You have some tasks to do!
            </div>
            <div class="card-body">
            <p>Pending for you : {{ $performance['total_pending'] }}</p>
            </div>
        </div>