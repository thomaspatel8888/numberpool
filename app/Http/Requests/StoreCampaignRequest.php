<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCampaignRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('campaign_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'min:5',
                'max:10',
                'required',
            ],
            'dedup' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'dedup_limit' => [
                'required',
            ],
            'numbers.*' => [
                'integer',
            ],
            'numbers' => [
                'array',
            ],
        ];
    }
}
