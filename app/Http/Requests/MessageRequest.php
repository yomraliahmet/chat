<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required',
            'receiver_id' => 'required'
        ];
    }


    public function attributes()
    {
        return [
            'message' => 'Message',
            'receiver_id' => 'Receiver',
        ];
    }
}
