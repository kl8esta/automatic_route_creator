<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoutePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    //'list_json' => 'required|json'
    public function rules()
    {
        return [
            'route_post.title' => 'required|string|max:100'
        ];
    }
}
