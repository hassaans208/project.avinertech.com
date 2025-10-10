<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RawQueryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'query' => [
                'required',
                'string',
                'max:10000', // Reasonable limit for query length
                'regex:/^SELECT\s+/i' // Must start with SELECT
            ]
        ];
    }

    public function messages()
    {
        return [
            'query.required' => __('validation.query_required'),
            'query.string' => __('validation.query_string'),
            'query.max' => __('validation.query_max_length'),
            'query.regex' => __('validation.query_must_start_with_select')
        ];
    }
}
