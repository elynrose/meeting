@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                   {{ trans('cruds.session.session_question') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.sessions.store") }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.session.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.name_helper') }}</span>
                        </div>
                        <!--
                        <div class="form-group">
                            <label for="audio">{{ trans('cruds.session.fields.audio') }}</label>
                            <div class="needsclick dropzone" id="audio-dropzone">
                            </div>
                            @if($errors->has('audio'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('audio') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.audio_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="audio_url">{{ trans('cruds.session.fields.audio_url') }}</label>
                            <textarea class="form-control" name="audio_url" id="audio_url">{{ old('audio_url') }}</textarea>
                            @if($errors->has('audio_url'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('audio_url') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.audio_url_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="transcription">{{ trans('cruds.session.fields.transcription') }}</label>
                            <textarea class="form-control ckeditor" name="transcription" id="transcription">{!! old('transcription') !!}</textarea>
                            @if($errors->has('transcription'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('transcription') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.transcription_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="summary">{{ trans('cruds.session.fields.summary') }}</label>
                            <textarea class="form-control ckeditor" name="summary" id="summary">{!! old('summary') !!}</textarea>
                            @if($errors->has('summary'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('summary') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.summary_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="task_created">{{ trans('cruds.session.fields.task_created') }}</label>
                            <input class="form-control" type="number" name="task_created" id="task_created" value="{{ old('task_created', '0') }}" step="1">
                            @if($errors->has('task_created'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('task_created') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.task_created_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="total_tasks">{{ trans('cruds.session.fields.total_tasks') }}</label>
                            <input class="form-control" type="number" name="total_tasks" id="total_tasks" value="{{ old('total_tasks', '') }}" step="1">
                            @if($errors->has('total_tasks'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('total_tasks') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.total_tasks_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="tokens_used">{{ trans('cruds.session.fields.tokens_used') }}</label>
                            <input class="form-control" type="number" name="tokens_used" id="tokens_used" value="{{ old('tokens_used', '0') }}" step="1">
                            @if($errors->has('tokens_used'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('tokens_used') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.tokens_used_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="status">{{ trans('cruds.session.fields.status') }}</label>
                            <input class="form-control" type="text" name="status" id="status" value="{{ old('status', 'New') }}" required>
                            @if($errors->has('status'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="user_id">{{ trans('cruds.session.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('user') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.session.fields.user_helper') }}</span>
                        </div>
-->
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                            <input type="hidden" name="status" value="New">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    Dropzone.options.audioDropzone = {
    url: '{{ route('frontend.sessions.storeMedia') }}',
    maxFilesize: 45, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 45
    },
    success: function (file, response) {
      $('form').find('input[name="audio"]').remove()
      $('form').append('<input type="hidden" name="audio" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="audio"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($session) && $session->audio)
      var file = {!! json_encode($session->audio) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="audio" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('frontend.sessions.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $session->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

@endsection