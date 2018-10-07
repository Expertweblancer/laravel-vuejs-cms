<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailTemplateRequest extends FormRequest
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
        if ($this->method() === 'POST') {
            return [
                'name' => 'required|unique:email_templates',
                'category' => 'required|in:user'
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'subject' => 'required',
                'body' => 'required'
            ];
        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        if ($this->method() === 'POST') {
            return [
                'name' => trans('template.name'),
                'category' => trans('template.category'),
            ];
        }  else if ($this->method() === 'PATCH') {
            return [
                'subject' => trans('template.subject'),
                'body' => trans('template.body'),
            ];
        }
    }
}
