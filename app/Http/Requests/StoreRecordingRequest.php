<?php

namespace App\Http\Requests;

use App\Models\Todo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRecordingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('todo_create');
    }

    public function rules()
    {
        return [
            'audio_url' => [
                'string',
                'required',
            ],
            'session_id' => [
                'required',
                'integer',
            ],
            'transcription' => [
                'string',
                'nullable',
            ],
            'summary' => [
                'string',
                'nullable',
            ],
        ];
    }
}
