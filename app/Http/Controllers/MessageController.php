<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Requests\MessageRequest;
use App\Repositories\UploadRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ActivityLogRepository;

class MessageController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $user;
    protected $upload;

    protected $module = 'message';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, MessageRepository $repo, ActivityLogRepository $activity, UserRepository $user, UploadRepository $upload)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->user = $user;
        
        $this->middleware('permission:access-message');
        $this->middleware('feature.available:message');
        $this->upload = $upload;
    }

    /**
     * Used to fetch Pre-Requisites for Message
     * @post ("/api/message/compose/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        return $this->ok(generateSelectOption($this->user->listByNameAndEmailExceptAuthUser()));
    }

    /**
     * Used to store Message
     * @post ("/api/message/compose")
     * @param ({
     *      @Parameter("subject", type="string", required="true", description="Subject of Message"),
     *      @Parameter("body", type="text", required="true", description="Body of Message"),
     *      @Parameter("to_user_id", type="integer", required="true", description="Id of Recipient User"),
     *      @Parameter("id", type="integer", required="optional", description="Id of Message if saved as draft"),
     *      @Parameter("upload_token", type="string", required="true", description="Unique token for Upload"),
     * })
     * @return Response
     */
    public function store(MessageRequest $request)
    {
        $message = $this->repo->store($this->request->all(), $this->module);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $message->id,
            'activity' => 'sent'
        ]);

        return $this->success(['message' => trans('message.message_processed', ['action' => request('is_draft') ? trans('message.drafted') : trans('message.sent')])]);
    }

    /**
     * Used to reply Message
     * @post ("/api/message/reply")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     *      @Parameter("subject", type="string", required="true", description="Subject of Message"),
     *      @Parameter("body", type="text", required="true", description="Body of Message"),
     *      @Parameter("to_user_id", type="integer", required="true", description="Id of Recipient User"),
     *      @Parameter("upload_token", type="string", required="true", description="Unique token for Upload"),
     * })
     * @return Response
     */
    public function reply()
    {
        $reply = $this->repo->reply($this->request->all(), $this->module);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $reply->id,
            'activity' => 'replied'
        ]);

        return $this->success(['message' => trans('message.message_processed', ['action' => trans('message.sent')])]);
    }

    /**
     * Used to load Reply Message
     * @get ("/api/message/{uuid}/reply")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function loadReply($uuid)
    {
        $message = $this->repo->isValidMessage($uuid);

        $replies = $this->repo->loadReply($message);

        $reply_attachments = array();
        foreach ($replies as $reply) {
            $reply_attachments[$reply->id] = $this->upload->getAttachment($this->module, $reply->id);
        }

        return $this->success(compact('replies', 'reply_attachments'));
    }

    /**
     * Used to fetch Message Statistics
     * @post ("/api/message/statistics")
     * @return Response
     */
    public function statistics()
    {
        $inbox = $this->repo->getInbox()->count();
        $sent  = $this->repo->getSent()->count();
        $draft = $this->repo->getDraft()->count();
        $trash = $this->repo->getTrash()->count();

        return $this->success(compact('inbox', 'sent', 'draft', 'trash'));
    }

    /**
     * Used to get Draft Message detail
     * @get ("/api/message/{uuid}/draft")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function getDraft($uuid)
    {
        $draft = $this->repo->getDraft(['uuid' => $uuid]);

        $selected_user = ($draft->to_user_id) ? ['name' => $draft->userTo->name_with_email,'id' => $draft->to_user_id] : [];

        return $this->success(compact('draft', 'selected_user'));
    }

    /**
     * Used to get all Draft Messages
     * @get ("/api/message/draft")
     * @return Response
     */
    public function getDraftList()
    {
        return $this->ok($this->repo->getDraft($this->request->all()));
    }

    /**
     * Used to get all Inbox Messages
     * @get ("/api/message/inbox")
     * @return Response
     */
    public function getInboxList()
    {
        $response = $this->repo->getInboxList($this->request->all());

        $messages = $response['messages'];
        $message_details = $response['message_details'];

        return $this->success(compact('messages', 'message_details'));
    }

    /**
     * Used to get all Sent Messages
     * @get ("/api/message/sent")
     * @return Response
     */
    public function getSentList()
    {
        $response = $this->repo->getSentList($this->request->all());

        $messages = $response['messages'];
        $message_details = $response['message_details'];

        return $this->success(compact('messages', 'message_details'));
    }

    /**
     * Used to get all Important Messages
     * @get ("/api/message/important")
     * @return Response
     */
    public function getImportantList()
    {
        return $this->ok($this->repo->getImportantList($this->request->all()));
    }

    /**
     * Used to get all Trash Messages
     * @get ("/api/message/trash")
     * @return Response
     */
    public function getTrashList()
    {
        return $this->ok($this->repo->getTrashList($this->request->all()));
    }

    /**
     * Used to get Message detail
     * @get ("/api/message/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function show($uuid)
    {
        $message = $this->repo->isValidMessage($uuid);

        $this->repo->markAsRead($message);

        if ($message->reply) {
            return $this->error(['message' => trans('general.invalid_link')]);
        }

        $attachments = $this->upload->getAttachment($this->module, $message->id);

        return $this->success(compact('message', 'attachments'));
    }

    /**
     * Used to toggle Important Message
     * @post ("/api/message/{uuid}/important")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function toggleImportant($uuid)
    {
        $message = $this->repo->isValidMessage($uuid);

        if ($message->from_user_id === \Auth::user()->id) {
            $message->is_important_by_sender = ($message->is_important_by_sender) ? 0 : 1;
        } else {
            $message->is_important_by_receiver = ($message->is_important_by_receiver) ? 0 : 1;
        }

        $message->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Used to download Message Attachment
     * @get ("/message/{message_uuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("message_uuid", type="string", required="true", description="Unique Id of Message"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($message_uuid, $attachment_uuid)
    {
        $message = $this->repo->isValidMessage($message_uuid);

        $attachment = $this->upload->getAttachment($this->module, $message->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module' => 'attachment',
            'module_id' => $attachment->id,
            'sub_module' => $this->module,
            'sub_module_id' => $message->id,
            'activity' => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }

    /**
     * Used to delete Draft Message
     * @delete ("/api/message/{uuid}/draft")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function destroyDraft($uuid)
    {
        $draft = $this->repo->deletableDraft($uuid);

        $this->upload->delete($this->module, $draft->id);

        $this->activity->record([
            'module' => 'draft',
            'module_id' => $draft->id,
            'activity' => 'deleted'
        ]);

        $draft->delete();

        return $this->success(['message' => trans('message.deleted', ['type' => trans('message.draft')])]);
    }

    /**
     * Used to trash Messages
     * @post ("/api/message/{uuid}/trash")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function trash($uuid)
    {
        $message = $this->repo->isValidMessage($uuid);

        $message = $this->repo->trash($message);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $message->id,
            'activity' => 'trashed'
        ]);

        return $this->success(['message' => trans('message.trashed')]);
    }

    /**
     * Used to restore Messages
     * @post ("/api/message/{uuid}/restore")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function restore($uuid)
    {
        $message = $this->repo->isTrashMessage($uuid);

        $message = $this->repo->restore($message);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $message->id,
            'activity' => 'restored'
        ]);

        return $this->success(['message' => trans('message.restored')]);
    }

    /**
     * Used to delete Messages
     * @delete ("/api/message/{uuid}/delete")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Message"),
     * })
     * @return Response
     */
    public function destroy($uuid)
    {
        $message = $this->repo->isTrashMessage($uuid);

        $this->repo->delete($message, $this->module);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $message->id,
            'activity' => 'deleted'
        ]);

        return $this->success(['message' => trans('message.deleted', ['type' => trans('message.message')])]);
    }
}
