<?php

namespace App\Http\Requests;

use App\Models\Todo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTodoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('todo_create');
    }

    public function rules()
    {
        return [
            'item' => [
                'string',
                'nullable',
            ],
            'due_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'time_due' => [
                'date_format:' . config('panel.time_format'),
                'nullable',
            ],
            'assigned_tos.*' => [
                'integer',
            ],
            'assigned_tos' => [
                'array',
            ],
            'completed' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'session_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
