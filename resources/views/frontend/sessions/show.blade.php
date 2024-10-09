@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.show') }} {{ trans('cruds.session.title') }}
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.sessions.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $session->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $session->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.audio') }}
                                    </th>
                                    <td>
                                        @if($session->audio)
                                            <a href="{{ $session->audio->getUrl() }}" target="_blank">
                                                {{ trans('global.view_file') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.audio_url') }}
                                    </th>
                                    <td>
                                        {{ $session->audio_url }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.transcription') }}
                                    </th>
                                    <td>
                                        {!! $session->transcription !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.summary') }}
                                    </th>
                                    <td>
                                        {!! $session->summary !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.task_created') }}
                                    </th>
                                    <td>
                                        {{ $session->task_created }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.total_tasks') }}
                                    </th>
                                    <td>
                                        {{ $session->total_tasks }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.tokens_used') }}
                                    </th>
                                    <td>
                                        {{ $session->tokens_used }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.status') }}
                                    </th>
                                    <td>
                                        {{ $session->status }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.session.fields.user') }}
                                    </th>
                                    <td>
                                        {{ $session->user->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('frontend.sessions.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection