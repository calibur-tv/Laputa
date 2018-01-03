<?php

namespace App\Api\V1\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class CommitRequest extends FormRequest
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
    public function rules()
    {
        return [
            'targetUserId' => 'required|integer',
            'content' => 'required|max:50'
        ];
    }
}
