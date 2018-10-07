<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IpFilterRequest extends FormRequest
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
            'start_ip' => 'required|ip',
            'end_ip' => 'ip'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'start_ip' => trans('ip_filter.start_ip'),
            'end_ip' => trans('ip_filter.end_ip'),
        ];
    }
}
