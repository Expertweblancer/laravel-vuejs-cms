<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocaleRequest extends FormRequest
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
        $id = $this->route('id');

        if ($this->path() === 'api/locale/add-word') {
            return [
                'word' => 'required',
                'translation' => 'required',
                'module' => 'required'
            ];
        }

        if ($this->method() === 'POST') {
            return [
                'name' => 'required|unique:locales',
                'locale' => 'required|unique:locales'
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'name' => 'required|unique:locales,name,'.$id.',id',
                'locale' => 'required|unique:locales,locale,'.$id.',id',
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
        if ($this->path() === 'api/locale/add-word') {
            return [
                'word' => trans('locale.word'),
                'translation' => trans('locale.translation'),
                'module' => trans('locale.module'),
            ];
        }

        return [
            'name' => trans('locale.name'),
            'locale' => trans('locale.locale'),
        ];
    }
}
