<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySessionRequest;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\Session;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('session_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessions = Session::with(['user', 'media'])->get();

        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        abort_if(Gate::denies('session_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.sessions.create', compact('users'));
    }

    public function store(StoreSessionRequest $request)
    {
        $session = Session::create($request->all());

        if ($request->input('audio', false)) {
            $session->addMedia(storage_path('tmp/uploads/' . basename($request->input('audio'))))->toMediaCollection('audio', 's3', 'audio');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $session->id]);
        }

        return redirect()->route('admin.sessions.index');
    }

    public function edit(Session $session)
    {
        abort_if(Gate::denies('session_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $session->load('user');

        return view('admin.sessions.edit', compact('session', 'users'));
    }

    public function update(UpdateSessionRequest $request, Session $session)
    {
        $session->update($request->all());

        if ($request->input('audio', false)) {
            if (! $session->audio || $request->input('audio') !== $session->audio->file_name) {
                if ($session->audio) {
                    $session->audio->delete();
                }
                $session->addMedia(storage_path('tmp/uploads/' . basename($request->input('audio'))))->toMediaCollection('audio');
            }
        } elseif ($session->audio) {
            $session->audio->delete();
        }

        return redirect()->route('admin.sessions.index');
    }

    public function show(Session $session)
    {
        abort_if(Gate::denies('session_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $session->load('user');

        return view('admin.sessions.show', compact('session'));
    }

    public function destroy(Session $session)
    {
        abort_if(Gate::denies('session_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $session->delete();

        return back();
    }

    public function massDestroy(MassDestroySessionRequest $request)
    {
        $sessions = Session::find(request('ids'));

        foreach ($sessions as $session) {
            $session->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('session_create') && Gate::denies('session_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Session();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
