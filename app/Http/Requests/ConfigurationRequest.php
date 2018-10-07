<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'company_name' => 'sometimes|required',
            'contact_person' => 'sometimes|required',
            'address_line_1' => 'sometimes|required',
            'city' => 'sometimes|required',
            'state' => 'sometimes|required',
            'email' => 'sometimes|required',
            'driver' => 'sometimes|required',
            'from_name' => 'sometimes|required',
            'from_address' => 'sometimes|required',
            'smtp_host' => 'required_if:driver,smtp',
            'smtp_port' => 'required_if:driver,smtp',
            'smtp_username' => 'required_if:driver,smtp',
            'smtp_password' => 'required_if:driver,smtp',
            'mailgun_host' => 'required_if:driver,mailgun',
            'mailgun_port' => 'required_if:driver,mailgun',
            'mailgun_username' => 'required_if:driver,mailgun',
            'mailgun_password' => 'required_if:driver,mailgun',
            'mailgun_domain' => 'required_if:driver,mailgun',
            'mailgun_secret' => 'required_if:driver,mailgun',
            'mandrill_secret' => 'required_if:driver,mandrill',
            'smtp_encryption' => 'in:ssl,tls,'.config('system.hidden_field'),
            'mailgun_encryption' => 'in:ssl,tls,'.config('system.hidden_field'),
            'config_type' => 'required',
            'token_lifetime' => 'sometimes|integer|min:10',
            'nexmo_api_key' => 'sometimes|required',
            'nexmo_api_secret' => 'sometimes|required',
            'nexmo_sender_mobile' => 'sometimes|required',
            'nexmo_receiver_mobile' => 'sometimes|required',
            'lock_screen_timeout' => 'required_if:lock_screen,1|integer|min:1|max:120',
            'login_throttle_attempt' => 'required_if:login_throttle,1|integer|min:1|max:10',
            'login_throttle_timeout' => 'required_if:login_throttle,1|integer|min:1|max:300',
            'reset_password_token_lifetime' => 'sometimes|integer|min:5|max:300'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'company_name' => trans('configuration.company_name'),
            'contact_person' => trans('configuration.contact_person'),
            'address_line_1' => trans('configuration.address_line_1'),
            'city' => trans('configuration.city'),
            'state' => trans('configuration.state'),
            'email' => trans('configuration.email'),
            'driver' => trans('mail.driver'),
            'from_name' => trans('mail.from_name'),
            'from_address' => trans('mail.from_address'),
            'smtp_host' => trans('mail.host'),
            'smtp_port' => trans('mail.port'),
            'smtp_username' => trans('mail.username'),
            'smtp_password' => trans('mail.password'),
            'mailgun_host' => trans('mail.host'),
            'mailgun_port' => trans('mail.port'),
            'mailgun_username' => trans('mail.username'),
            'mailgun_password' => trans('mail.password'),
            'mailgun_domain' => trans('mail.domain'),
            'mailgun_secret' => trans('mail.secret'),
            'mandrill_secret' => trans('mail.secret'),
            'smtp_encryption' => trans('mail.encryption'),
            'mailgun_encryption' => trans('mail.encryption'),
            'token_lifetime' => trans('auth.token_lifetime'),
            'nexmo_api_key' => trans('configuration.api_key'),
            'nexmo_api_secret' => trans('configuration.api_secret'),
            'nexmo_sender_mobile' => trans('configuration.sender_mobile'),
            'nexmo_receiver_mobile' => trans('configuration.receiver_mobile'),
            'lock_screen_timeout' => trans('auth.lock_screen_timeout'),
            'login_throttle_attempt' => trans('auth.login_throttle_attempt'),
            'login_throttle_timeout' => trans('auth.login_throttle_timeout'),
            'reset_password_token_lifetime' => trans('auth.reset_password_token_lifetime'),
        ];
    }
}
