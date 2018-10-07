<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\RegisterRequest;
use App\Repositories\LocaleRepository;
use App\Http\Requests\UserEmailRequest;
use App\Http\Requests\UserProfileRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;

class UserController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $role;
    protected $locale;
    protected $module = 'user';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, UserRepository $repo, ActivityLogRepository $activity, RoleRepository $role, LocaleRepository $locale)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->role = $role;

        $this->middleware('prohibited.test.mode')->only(['forceResetPassword','destroy','uploadAvatar','removeAvatar']);
        $this->locale = $locale;
    }

    /**
     * Used to get Pre Requisite for User Module
     * @get ("/api/user/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('create', User::class);

        $countries = generateNormalSelectOption(getVar('country'));
        $genders = generateTranslatedSelectOption(getVar('list')['gender']);
        $roles = generateSelectOption($this->role->listExceptName([config('system.default_role.admin')]));

        return $this->success(compact('countries', 'roles', 'genders'));
    }

    /**
     * Used to get profile Pre Requisite
     * @get ("/api/user/profile/pre-requisite")
     * @return Response
     */
    public function profilePreRequisite()
    {
        $user = $this->repo->findOrFail(\Auth::user()->id);
        $genders = generateTranslatedSelectOption(getVar('list')['gender']);

        return $this->success(compact('user','genders'));
    }

    /**
     * Used to get preference Pre Requisite
     * @get ("/api/user/preference/pre-requisite")
     * @return Response
     */
    public function preferencePreRequisite()
    {
        $system_variables = getVar('system');
        $color_themes = isset($system_variables['color_themes']) ? $system_variables['color_themes'] : [];
        $directions = isset($system_variables['directions']) ? $system_variables['directions'] : [];
        $sidebar = isset($system_variables['sidebar']) ? $system_variables['sidebar'] : [];
        $locales = generateNormalSelectOption($this->locale->list());

        return $this->success(compact('color_themes','directions','sidebar','locales'));
    }

    /**
     * Used to store user preference
     * @post ("/api/user/preference")
     * @return Response
     */
    public function preference()
    {
        $this->repo->updatePreference(\Auth::user()->UserPreference,$this->request->all());

        return $this->success(['message' => trans('user.preference_updated')]);
    }

    /**
     * Used to get all Users
     * @get ("/api/user")
     * @return Response
     */
    public function index()
    {
        $this->authorize('view', User::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store User
     * @post ("/api/user")
     * @param ({
     *      @Parameter("first_name", type="string", required="true", description="First Name of User"),
     *      @Parameter("last_name", type="string", required="true", description="Last Name of User"),
     *      @Parameter("email", type="email", required="true", description="Email of User"),
     *      @Parameter("password", type="password", required="true", description="Password of User"),
     *      @Parameter("password_confirmation", type="password_confirmation", required="true", description="Confirm Password of User"),
     *      @Parameter("role_id", type="array", required="true", description="Roles of User"),
     *      @Parameter("address_line_1", type="string", required="optional", description="Address Line 1 of User"),
     *      @Parameter("address_line_2", type="string", required="optional", description="Address Line 2 of User"),
     *      @Parameter("city", type="string", required="optional", description="City of User"),
     *      @Parameter("state", type="string", required="optional", description="State of User"),
     *      @Parameter("zipcode", type="string", required="optional", description="Zipcode of User"),
     *      @Parameter("country_id", type="integer", required="true", description="Country of User"),
     * })
     * @return Response
     */
    public function store(RegisterRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $user->id,
            'activity'  => 'created'
        ]);

        return $this->success(['message' => trans('user.added')]);
    }

    /**
     * Used to fetch User detail
     * @get ("/api/user/detail")
     * @return Response
     */
    public function detail()
    {
        return $this->ok($this->repo->findOrFail(\Auth::user()->id));
    }

    /**
     * Used to get User detail
     * @get ("/api/user/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', User::class);

        $user = $this->repo->findOrFail($id);

        $selected_roles = generateSelectOption($user->roles()->pluck('name', 'id')->all());
        
        $roles = $user->roles()->pluck('id')->all();

        return $this->success(compact('user', 'selected_roles', 'roles'));
    }

    /**
     * Used to update User
     * @patch ("/api/user/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("first_name", type="string", required="true", description="First Name of User"),
     *      @Parameter("last_name", type="string", required="true", description="Last Name of User"),
     *      @Parameter("role_id", type="array", required="true", description="Roles of User"),
     *      @Parameter("gender", type="string", required="true", description="Gender of User"),
     *      @Parameter("date_of_birth", type="date", required="optional", description="Date of Birth of User"),
     *      @Parameter("date_of_anniversary", type="date", required="optional", description="Date of Anniversary of User"),
     * })
     * @return Response
     */
    public function update(UserProfileRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $user = $this->repo->update($user, $this->request->all());

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Used to update User Status
     * @post ("/api/user/{id}/status")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("status", type="string", required="true", description="Status of User"),
     * })
     * @return Response
     */
    public function updateStatus($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $this->repo->status($user, request('status'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Used to update User Contact
     * @patch ("/api/user/{id}/contact")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("address_line_1", type="string", required="optional", description="Address Line 1 of User"),
     *      @Parameter("address_line_2", type="string", required="optional", description="Address Line 2 of User"),
     *      @Parameter("city", type="string", required="optional", description="City of User"),
     *      @Parameter("state", type="string", required="optional", description="State of User"),
     *      @Parameter("zipcode", type="string", required="optional", description="Zipcode of User"),
     *      @Parameter("country_id", type="integer", required="true", description="Country of User"),
     *      @Parameter("phone", type="string", required="true", description="Phone of User"),
     * })
     * @return Response
     */
    public function updateContact(UserProfileRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $user = $this->repo->update($user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Used to update User Social Links
     * @patch ("/api/user/{id}/social")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("facebook_profile", type="string", required="optional", description="Facebook Profile of User"),
     *      @Parameter("twitter_profile", type="string", required="optional", description="Twitter Profile of User"),
     *      @Parameter("linkedin_profile", type="string", required="optional", description="Linked Profile of User"),
     *      @Parameter("google_plus_profile", type="string", required="optional", description="Google Plus Profile of User"),
     * })
     * @return Response
     */
    public function updateSocial($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('update', $user);

        $user = $this->repo->update($user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Used to force change User Password
     * @patch ("/api/user/{id}/force-reset-password")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("new_password", type="string", required="true", description="New Password of User"),
     *      @Parameter("new_password_confirmation", type="string", required="true", description="Confirm New Password of User"),
     * })
     * @return Response
     */
    public function forceResetPassword(ChangePasswordRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('forceResetUserPassword', $user);

        $user = $this->repo->forceResetPassword($user, request('new_password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('passwords.change')]);
    }

    /**
     * Used to send email to User
     * @patch ("/api/user/{id}/email")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("template_id", type="integer", required="true", description="Id of Template"),
     *      @Parameter("subject", type="string", required="true", description="Subject of Email"),
     *      @Parameter("body", type="text", required="true", description="Body of Email"),
     * })
     * @return Response
     */
    public function sendEmail(UserEmailRequest $request, $id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('email', $user);

        $this->repo->sendEmail($user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'sub_module' => 'mail',
            'activity' => 'sent'
        ]);

        return $this->success(['message' => trans('template.mail_sent')]);
    }

    /**
     * Used to update User Profile
     * @post ("/api/user/profile/update")
     * @param ({
     *      @Parameter("first_name", type="string", required="true", description="First Name of User"),
     *      @Parameter("last_name", type="string", required="true", description="Last Name of User"),
     *      @Parameter("gender", type="string", required="true", description="Gender of User"),
     *      @Parameter("date_of_birth", type="date", required="optional", description="Date of Birth of User"),
     *      @Parameter("date_of_anniversary", type="date", required="optional", description="Date of Anniversary of User"),
     *      @Parameter("facebook_profile", type="string", required="optional", description="Facebook Profile of User"),
     *      @Parameter("twitter_profile", type="string", required="optional", description="Twitter Profile of User"),
     *      @Parameter("linkedin_profile", type="string", required="optional", description="Linked Profile of User"),
     *      @Parameter("google_plus_profile", type="string", required="optional", description="Google Plus Profile of User"),
     * })
     * @return Response
     */
    public function updateProfile(UserProfileRequest $request)
    {
        $auth_user = \Auth::user();

        $user = $this->repo->update($auth_user, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $auth_user->id,
            'sub_module' => 'profile',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('user.profile_updated')]);
    }

    /**
     * Used to update User Avatar
     * @post ("/api/user/profile/avatar/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     *      @Parameter("image", type="file", required="true", description="Image File to be uploaded"),
     * })
     * @return Response
     */
    public function uploadAvatar($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('avatar', $user);

        $image_path = config('system.upload_path.avatar').'/';

        $profile = $user->Profile;
        $image = $profile->avatar;

        if ($image && \File::exists($image)) {
            \File::delete($image);
        }

        $extension = request()->file('image')->getClientOriginalExtension();
        $filename = uniqid();
        $file = request()->file('image')->move($image_path, $filename.".".$extension);
        $img = \Image::make($image_path.$filename.".".$extension);
        $img->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($image_path.$filename.".".$extension);

        $profile->avatar = $image_path.$filename.".".$extension;
        $profile->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'sub_module' => 'avatar',
            'activity' => 'uploaded'
        ]);

        return $this->success(['message' => trans('user.avatar_uploaded'),'image' => $image_path.$filename.".".$extension]);
    }

    /**
     * Used to remove User Avatar
     * @delete ("/api/user/profile/avatar/remove/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of User"),
     * })
     * @return Response
     */
    public function removeAvatar($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('avatar', $user);

        $image_path = config('system.upload_path.avatar').'/';

        $profile = $user->Profile;
        $image = $profile->avatar;

        if (!$image) {
            return $this->error(['message' => trans('user.no_avatar_uploaded')]);
        }

        if (\File::exists($image)) {
            \File::delete($image);
        }

        $profile->avatar = null;
        $profile->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'sub_module' => 'avatar',
            'activity' => 'removed'
        ]);

        return $this->success(['message' => trans('user.avatar_removed')]);
    }

    /**
     * Used to delete User
     * @delete ("/api/user/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Uuid of User"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->repo->findOrFail($id);

        $this->authorize('delete', $user);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $user->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($user);

        return $this->success(['message' => trans('user.deleted')]);
    }
}
