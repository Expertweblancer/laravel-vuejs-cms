<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'country_id' => 'sometimes|required',
            'role_id' => 'sometimes|required',
        ];

        if (config('config.terms_and_conditions')) {
            $rules['tnc'] = 'sometimes|accepted';
        }

        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        $attributes = [
            'first_name' => trans('auth.first_name'),
            'last_name' => trans('auth.last_name'),
            'email' => trans('auth.email'),
            'password' => trans('auth.password'),
            'password_confirmation' => trans('auth.password_confirmation'),
            'country_id' => trans('user.country'),
            'role_id' => trans('role.role'),
        ];

        if (config('config.terms_and_conditions')) {
            $attributes['tnc'] = trans('auth.tnc');
        }

        return $attributes;
    }
}
