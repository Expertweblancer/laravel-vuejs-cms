<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LocaleRepository;
use App\Http\Requests\ConfigurationRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ConfigurationRepository;

class ConfigurationController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $locale;

    protected $image_path = 'uploads/logo';
    protected $module = 'configuration';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, ConfigurationRepository $repo, ActivityLogRepository $activity, LocaleRepository $locale)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->locale = $locale;

        $this->middleware('permission:access-configuration')->except('getConfigurationVariable');
        $this->middleware('prohibited.test.mode')->only(['uploadLogo','removeLogo']);
    }

    /**
     * Used to get configuration
     * @get ("/api/configuration")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->getAllPublic());
    }

    /**
     * Used to save configuration
     * @post ("/api/configuration")
     * @param Various Configuration Variable
     * @return Response
     */
    public function store(ConfigurationRequest $request)
    {
        $this->repo->store($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => request('config_type') ? : null,
            'activity' => 'saved'
        ]);

        return $this->success(['message' => trans('configuration.stored')]);
    }

    /**
     * Used to get configuration variables
     * @post ("/api/configuration/variable")
     * @param ({
     *      @Parameter("type", type="string", required="true", description="Type of Configuration Variable, can be social_login or system or mail"),
     * })
     * @return Response
     */
    public function getConfigurationVariable()
    {
        $type = request('type') ? : 'system';

        $system_variables = getVar('system');

        if ($type === 'social_login') {
            $social_login_providers = isset($system_variables['social_login_providers']) ? $system_variables['social_login_providers'] : [];
            return $this->success(compact('social_login_providers'));
        }

        if ($type === 'mail') {
            $mail_drivers = isset($system_variables['mail_drivers']) ? $system_variables['mail_drivers'] : [];
            return $this->success(compact('mail_drivers'));
        }

        $color_themes = isset($system_variables['color_themes']) ? $system_variables['color_themes'] : [];

        $directions = isset($system_variables['directions']) ? $system_variables['directions'] : [];

        $sidebar = isset($system_variables['sidebar']) ? $system_variables['sidebar'] : [];

        $date_formats = isset($system_variables['date_formats']) ? $system_variables['date_formats'] : [];

        $time_formats = isset($system_variables['time_formats']) ? $system_variables['time_formats'] : [];

        $notification_positions = isset($system_variables['notification_positions']) ? $system_variables['notification_positions'] : [];

        $timezones = generateNormalSelectOptionValueOnly(getVar('timezone'));

        $locales = generateNormalSelectOption($this->locale->list());

        return $this->success(compact('color_themes', 'directions', 'date_formats', 'time_formats', 'notification_positions', 'timezones', 'locales','sidebar'));
    }

    /**
     * Used to fetch list data like gender, days
     * @post ("/api/fetch/lists")
     * @param ({
     *      @Parameter("lists", type="string", required="true", description="Type of lists to be fetched comma separated"),
     * })
     * @return Response
     */
    public function fetchList()
    {
        $lists = request('lists');
        $data = array();

        if (!$lists) {
            return $this->success(compact('data'));
        }

        $lists = explode(',', $lists);

        if (in_array('country', $lists)) {
            $data['country'] = generateNormalSelectOption(getVar('country'));
        }

        if (in_array('timezone', $lists)) {
            $data['timezone'] = generateNormalSelectOptionValueOnly(getVar('timezone'));
        }

        $list_data = getVar('list');
        foreach ($lists as $list) {
            $list_item = isset($list_data[$list]) ? $list_data[$list] : [];
            if ($list != 'country' && $list != 'timezone') {
                $data[$list] = count($list_item) ? generateTranslatedSelectOption($list_item) : [];
            }
        }
        
        return $this->success(compact('data'));
    }

    /**
     * Used to upload main or sidebar logo
     * @post ("/api/configuration/logo/{type}")
     * @param ({
     *      @Parameter("image", type="image", required="true", description="Image to be uploaded"),
     * })
     * @return Response
     */
    public function uploadLogo($type)
    {
        $image_path = config('system.upload_path.logo').'/';

        $image = config('config.'.$type.'._logo');

        if ($image && \File::exists($image)) {
            \File::delete($image);
        }

        $extension = request()->file('image')->getClientOriginalExtension();
        $filename = uniqid();
        $file = request()->file('image')->move($image_path, $filename.".".$extension);
        $img = \Image::make($image_path.$filename.".".$extension);
        $img->resize(null, 50, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($image_path.$filename.".".$extension);

        $config = $this->repo->firstOrCreate($type.'_logo');
        $config->text_value = $image_path.$filename.".".$extension;
        $config->save();

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => 'company_logo',
            'activity' => 'uploaded'
        ]);

        return $this->success(['message' => trans('configuration.logo_uploaded'),'image' => $image_path.$filename.".".$extension]);
    }

    /**
     * Used to remove main or sidebar logo
     * @post ("/api/configuration/logo/{type}/remove")
     * @return Response
     */
    public function removeLogo($type)
    {
        $image = config('config.'.$type.'_logo');

        if (!$image) {
            return $this->error(['message' => trans('configuration.no_logo_uploaded')]);
        }

        if (\File::exists($image)) {
            \File::delete($image);
        }

        $this->repo->set($type.'_logo', '');

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => 'company_logo',
            'activity' => 'removed'
        ]);

        return $this->success(['message' => trans('configuration.logo_removed')]);
    }
}
