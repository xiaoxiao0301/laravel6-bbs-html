<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ReplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|min:2'
        ];
    }
}
