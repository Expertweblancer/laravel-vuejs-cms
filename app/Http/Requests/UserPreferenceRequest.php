<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
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
            'locale' => 'required',
            'direction' => 'required|in:ltr,rtl',
            'color_theme' => 'required',
            'sidebar' => 'required|in:mini,normal'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'locale' => trans('locale.locale'),
            'direction' => trans('configuration.direction'),
            'color_theme' => trans('configuration.color_theme'),
            'sidebar' => trans('configuration.sidebar'),
        ];
    }
}
