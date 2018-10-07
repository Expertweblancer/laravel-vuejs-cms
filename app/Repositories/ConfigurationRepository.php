<?php
namespace App\Repositories;

use App\Configuration;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;

class ConfigurationRepository
{
    protected $config;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get all config variables
     * @return Configuration
     */
    public function getAll()
    {
        return $this->config->all()->pluck('value', 'name')->all();
    }

    /**
     * Get all config variables by public value
     * @return Configuration
     */
    public function getAllPublic()
    {
        return $this->config->all()->pluck('public_value', 'name')->all();
    }

    /**
     * Get config variable by name
     * @return Configuration
     */
    public function getByName($names)
    {
        return $this->config->filterByName($names)->get()->value;
    }

    /**
     * Get selected config variables by name
     * @return Configuration
     */
    public function getSelectedByName($names)
    {
        return $this->config->whereIn('name', $names)->get()->pluck('value', 'name')->all();
    }

    /**
     * Find configuration by name else create.
     *
     * @param array $params
     * @return null
     */
    public function firstOrCreate($name)
    {
        return $this->config->firstOrCreate(['name' => $name]);
    }

    /**
     * Store a configuration
     *
     * @param array $params
     * @return null
     */
    public function set($name, $value, $private = 0)
    {
        $config = $this->firstOrCreate([
            'name' => $name
        ]);

        $config->text_value = ($value) ? !is_numeric($value) ? $value : null : null;
        $config->numeric_value = is_numeric($value) ? $value : null;
        $config->is_private = $private;
        $config->save();

        return $config;
    }

    /**
     * Store configuration.
     *
     * @param array $params
     * @return null
     */
    public function store($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;

        $this->testModeOperation($params);

        $this->smsConfiguration($params);

        $this->socialLoginConfiguration($params);

        foreach ($params as $key => $value) {
            if (! in_array($key, ['config_type','providers']) && (!in_array($key, config('system.private_config_variables')) || (in_array($key, config('system.private_config_variables')) && $value != config('system.hidden_field')))) {
                $value = (is_array($value)) ? implode(',', $value) : $value;

                $config = $this->firstOrCreate($key);
                $config->numeric_value = is_numeric($value) ? $value : null;
                $config->text_value = !is_numeric($value) ? $value : null;
                $config->save();
            }
        }

        $this->setLocale($params);

        $this->setVisibility();

        if ($config_type === 'mail' || $config_type === 'system') {
            config(['config' => $this->getAll()]);
            $this->setEnv($config_type);
        }
    }

    /**
     * Store test mode configuration.
     *
     * @param array $params
     * @return null
     */
    public function testModeOperation($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;

        if ($config_type != 'system') {
            return;
        }

        $mode = isset($params['mode']) ? $params['mode'] : 1;

        if (isTestMode() && $mode != $this->firstOrCreate('mode')) {
            $this->set('mode', $mode, 0);
            config(['config.mode' => $mode]);
        }

        if (isTestMode()) {
            throw ValidationException::withMessages(['message' => trans('general.restricted_test_mode_action')]);
        }
    }

    /**
     * Store SMS configuration.
     *
     * @param array $params
     * @return null
     */
    public function smsConfiguration($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $nexmo_api_key = isset($params['nexmo_api_key']) ? $params['nexmo_api_key'] : null;
        $nexmo_api_secret = isset($params['nexmo_api_secret']) ? $params['nexmo_api_secret'] : null;
        $nexmo_receiver_mobile = isset($params['nexmo_receiver_mobile']) ? $params['nexmo_receiver_mobile'] : null;
        $nexmo_sender_mobile = isset($params['nexmo_sender_mobile']) ? $params['nexmo_sender_mobile'] : null;

        if ($config_type != 'sms') {
            return;
        }

        config(['nexmo.api_key' => $nexmo_api_key,'nexmo.api_secret' => $nexmo_api_secret]);
        try {
            $nexmo = app('Nexmo\Client');
            $nexmo->message()->send([
                'to'   => $nexmo_receiver_mobile,
                'from' => $nexmo_sender_mobile,
                'text' => 'Test Message!'
            ]);
        } catch (\Nexmo\Client\Exception\Request $e) {
            throw ValidationException::withMessages(['nexmo_api_key' => [$e->getMessage()]]);
        }
    }

    /**
     * Store social login configuration.
     *
     * @param array $params
     * @return null
     */
    public function socialLoginConfiguration($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $social_login = isset($params['social_login']) ? $params['social_login'] : 0;

        if ($config_type != 'authentication' || ! $social_login) {
            return;
        }

        $providers = isset($params['providers']) ? $params['providers'] : 0;

        foreach ($providers as $provider) {
            if ($provider['login'] && (empty($provider['client']) || empty($provider['secret']) || empty($provider['redirect_url']))) {
                throw ValidationException::withMessages(['message' => [trans('auth.provider_details_required', ['type' => $provider['provider']])]]);
            }
        }

        foreach ($providers as $provider) {
            if ($provider['login']) {
                $config = $this->firstOrCreate($provider['provider'].'_login');
                $config->numeric_value = 1;
                $config->save();

                if ($provider['client'] != config('system.hidden_field')) {
                    $config = $this->firstOrCreate($provider['provider'].'_client');
                    $config->text_value = $provider['client'] ? : null;
                    $config->save();
                }
                if ($provider['client'] != config('system.hidden_field')) {
                    $config = $this->firstOrCreate($provider['provider'].'_secret');
                    $config->text_value = $provider['secret'] ? : null;
                    $config->save();
                }
                $config = $this->firstOrCreate($provider['provider'].'_redirect_url');
                $config->text_value = $provider['redirect_url'] ? : null;
                $config->save();
            } else {
                $config = $this->firstOrCreate($provider['provider'].'_login');
                $config->numeric_value = 0;
                $config->save();
            }
        }
    }

    /**
     * Store locale configuration.
     *
     * @param array $params
     * @return null
     */
    public function setLocale($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $locale = isset($params['locale']) ? $params['locale'] : config('app.locale');

        if ($config_type != 'system') {
            return;
        }

        if ($locale === config('app.locale')) {
            return;
        }

        config(['app.locale' => $locale]);
        \App::setLocale(config('app.locale'));
        \Cache::forget('lang.js');
    }

    /**
     * Set configuration visibility.
     *
     * @param array $params
     * @return null
     */
    public function setVisibility()
    {
        $this->config->whereIn('name', config('system.private_config_variables'))->update(['is_private' => 1]);
        $this->config->whereNotIn('name', config('system.private_config_variables'))->update(['is_private' => 0]);
    }

    /**
     * Set default configuration variable.
     *
     * @return null
     */
    public function setDefault()
    {
        $system_variables = getVar('system');
        $default_config = isset($system_variables['default_config']) ? $system_variables['default_config'] : [];
        foreach ($default_config as $key => $value) {
            $config = $this->firstOrCreate($key);

            if (! is_numeric($config->numeric_value) && ($config->value === '' || $config->value === null)) {
                $config->numeric_value = is_numeric($value) ? $value : null;
                $config->text_value    = !is_numeric($value) ? $value : null;
                $config->save();
            }
        }

        config(['config' => $this->getAll()]);
        config(['system' => $system_variables]);

        try {
            \JWTAuth::parseToken()->authenticate();
            config([
                'config.user_direction' => \Auth::user()->UserPreference->direction,
                'config.user_locale' => \Auth::user()->UserPreference->locale,
                'config.user_sidebar' => \Auth::user()->UserPreference->sidebar,
                'config.user_color_theme' => \Auth::user()->UserPreference->color_theme
            ]);
        } catch (JWTException $e) {
            
        }

        $this->setVisibility();

        config(['nexmo.api_key' => config('config.nexmo_api_key'),'nexmo.api_secret' => config('config.nexmo_api_secret')]);
        config(['jwt.ttl' => config('config.token_lifetime') ? : 120]);
        date_default_timezone_set(config('config.timezone') ? : 'Asia/Kolkata');
        \App::setLocale(config('config.locale') ? : 'en');

        $this->setSocialLogin();
    }

    /**
     * Set .env files.
     *
     * @return null
     */
    public function setEnv($type = null)
    {
        if (! $type) {
            return;
        }

        if ($type === 'system') {
            envu(['APP_DEBUG' => (!\App::environment('production') && config('config.error_display')) ? true : false]);
        }


        if ($type === 'mail') {
            envu([
                'MAIL_DRIVER'       => config('config.driver'),
                'MAIL_FROM_ADDRESS' => config('config.from_address'),
                'MAIL_FROM_NAME'    => config('config.from_name')
            ]);

            if (config('config.driver') === 'smtp') {
                envu([
                    'MAIL_HOST'       => config('config.smtp_host'),
                    'MAIL_PORT'       => config('config.smtp_port'),
                    'MAIL_USERNAME'   => config('config.smtp_username'),
                    'MAIL_PASSWORD'   => config('config.smtp_password'),
                    'MAIL_ENCRYPTION' => config('config.smtp_encryption'),
                ]);
            } elseif (config('config.driver') === 'mailgun') {
                envu([
                    'MAIL_HOST'       => config('config.mailgun_host'),
                    'MAIL_PORT'       => config('config.mailgun_port'),
                    'MAIL_USERNAME'   => config('config.mailgun_username'),
                    'MAIL_PASSWORD'   => config('config.mailgun_password'),
                    'MAIL_ENCRYPTION' => config('config.mailgun_encryption'),
                    'MAILGUN_DOMAIN'  => config('config.mailgun_domain'),
                    'MAILGUN_SECRET'  => config('config.mailgun_secret'),
                ]);
            } elseif (config('config.driver') === 'mandrill') {
                envu([
                    'MANDRILL_SECRET' => config('config.mandrill_secret'),
                ]);
            }
        }
    }

    /**
     * Set social login configuration.
     *
     * @return null
     */
    public function setSocialLogin()
    {
        if (config('config.social_login')) {
            foreach (config('system.social_login_providers') as $provider) {
                config([
                    'services.'.$provider => [
                        'client_id'     => config('config.'.$provider.'_client'),
                        'client_secret' => config('config.'.$provider.'_secret'),
                        'redirect'      => config('config.'.$provider.'_redirect_url'),
                    ]
                ]);
            }
        }
    }

    /**
     * Get company address
     * @return string
     */
    public function getCompanyAddress()
    {
        $address = config('config.address_line_1');
        $address .= (config('config.address_line_2')) ? (', <br >'.config('config.address_line_2')) : '';
        $address .= (config('config.city')) ? ', <br >'.(config('config.city')) : '';
        $address .= (config('config.state')) ? ', '.(config('config.state')) : '';
        $address .= (config('config.zipcode')) ? ', '.(config('config.zipcode')) : '';
        $address .= (config('config.country_id')) ? '<br >'.(config('config.country')) : '';

        return $address;
    }

    /**
     * Get company logo
     * @return string
     */
    public function getCompanyLogo()
    {
        if (config('config.main_logo') && \File::exists(config('config.main_logo'))) {
            return '<img src="'.url('/'.config('config.main_logo')).'">';
        } else {
            return '<img src="'.url('/images/default_main_logo.png').'">';
        }
    }
}
