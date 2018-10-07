<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
        if (! request('is_draft')) {
            return [
                'subject' => 'required',
                'to_user_id' => 'required',
                'body' => 'required'
            ];
        } else {
            return [
                'subject' => 'required_without_all:to_user_id,body',
                'to_user_id' => 'required_without_all:subject,body',
                'body' => 'required_without_all:subject,to_user_id'
            ];
        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'to_user_id' => trans('message.recipient'),
            'subject' => trans('message.subject'),
            'body' => trans('message.body')
        ];
    }
}
