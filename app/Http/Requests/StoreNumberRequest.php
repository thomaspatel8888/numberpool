<?php

namespace App\Http\Requests;

use App\Models\Number;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreNumberRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('number_create');
    }

    public function rules()
    {
        return [
            'number' => [
                'string',
                'required',
            ],
        ];
    }
}
