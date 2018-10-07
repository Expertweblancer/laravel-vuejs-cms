<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Requests\EmailTemplateRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\EmailTemplateRepository;

class EmailTemplateController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $user;

    protected $module = 'email_template';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, EmailTemplateRepository $repo, ActivityLogRepository $activity, UserRepository $user)
    {
        $this->repo     = $repo;
        $this->activity = $activity;
        $this->request  = $request;
        $this->user     = $user;

        $this->middleware('permission:access-configuration')->except('getContent');
        $this->middleware('feature.available:email_template');
    }

    /**
     * Used to get all email templates
     * @get ("/api/email-template")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to get email template names of certain category
     * @get ("/api/email-log/{category}/lists")
     * @return Response
     */
    public function lists($category)
    {
        return $this->ok($this->repo->listByCategory($category));
    }

    /**
     * Used to get Email Template detail
     * @get ("/api/email-template/{id}")
     * @return Response
     */
    public function show($id)
    {
        $email_template = $this->repo->findOrFail($id);

        $template_variables = getVar('template');

        $fields = isset($template_variables[$email_template->slug]['fields']) ? $template_variables[$email_template->slug]['fields'] : config('system.default_template_field.'.$email_template->category);

        return $this->success(compact('email_template', 'fields'));
    }

    /**
     * Used to store email template
     * @post ("/api/email-template")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Email Template"),
     *      @Parameter("category", type="string", required="true", description="Category of Email Template"),
     * })
     * @return Response
     */
    public function store(EmailTemplateRequest $request)
    {
        $email_template = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $email_template->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('template.added')]);
    }

    /**
     * Used to update email template
     * @patch ("/api/email-template/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of email template to be updated"),
     *      @Parameter("subject", type="string", required="true", description="Subject of Email Template"),
     *      @Parameter("body", type="text", required="true", description="Body of Email Template"),
     * })
     * @return Response
     */
    public function update(EmailTemplateRequest $request, $id)
    {
        $email_template = $this->repo->findOrFail($id);

        $email_template = $this->repo->update($email_template, $this->request->all());

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $email_template->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('template.updated')]);
    }

    /**
     * Used to delete email template
     * @delete ("/api/email-template/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of email template to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $email_template = $this->repo->deletable($id);

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $email_template->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($email_template);

        return $this->success(['message' => trans('template.deleted')]);
    }

    /**
     * Used to get email template content
     * @delete ("/api/email-template/{id}/content")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of email template to be retrieved"),
     *      @Parameter("user_id", type="integer", required="true", description="Id of user to be retrieved"),
     * })
     * @return Response
     */
    public function getContent($id)
    {
        $email_template = $this->repo->findOrFail($id);

        $user = $this->user->findOrFail(request('user_id'));

        if ($email_template->category === 'user' && !$user) {
            return $this->error(['message' => trans('template.missing_parameter', ['parameter'=> trans('user.user')])]);
        }

        $mail_data = $this->repo->getContent(['template' => $email_template,'user' => $user]);

        return $this->success(compact('mail_data'));
    }
}
