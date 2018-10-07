<?php
namespace App\Repositories;

use App\Message;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class MessageRepository
{
    protected $message;
    protected $upload;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Message $message, UploadRepository $upload)
    {
        $this->message = $message;
        $this->upload = $upload;
    }

    /**
     * Find message by uuid.
     *
     * @param string $uuid
     * @return Message
     */
    public function findByUuid($uuid = null)
    {
        $message = $this->message->filterByUuid($uuid)->first();

        if (! $message) {
            throw ValidationException::withMessages(['message' => trans('message.could_not_find', ['type' => trans('message.message')])]);
        }

        return $message;
    }

    /**
     * Validate live message.
     *
     * @param string $uuid
     * @return Message
     */
    public function isValidMessage($uuid)
    {
        $message = $this->message->with('userFrom', 'userTo', 'userFrom.profile', 'userTo.profile')->filterByUuid($uuid)->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByFromUserId(\Auth::user()->id)
                ->filterByIsDeletedBySender(0);
            })->orWhere(function ($query2) {
                $query2->filterByToUserId(\Auth::user()->id)
                ->filterByIsDeletedByReceiver(0);
            });
        })->first();

        if (! $message) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        return $message;
    }

    /**
     * Validate trash message.
     *
     * @param string $uuid
     * @return Message
     */
    public function isTrashMessage($uuid)
    {
        $message = $this->message->filterByUuid($uuid)->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByFromUserId(\Auth::user()->id)
                ->filterByIsDeletedBySender(1);
            })->orWhere(function ($query2) {
                $query2->filterByToUserId(\Auth::user()->id)
                ->filterByIsDeletedByReceiver(1);
            });
        })->first();

        if (! $message) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        return $message;
    }

    /**
     * Send message or save as draft.
     *
     * @param array $params
     * @param string $module
     * @return Message
     */
    public function store($params = array(), $module)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;
        $is_draft = (isset($params['is_draft']) && $params['is_draft']) ? $params['is_draft'] : 0;

        if ($id) {
            $draft = $this->message->filterById($id)->filterByFromUserId(\Auth::user()->id)->filterByIsDraft(1)->first();

            if (!$draft) {
                throw ValidationException::withMessages(['message' => trans('message.invalid_action')]);
            }
        }

        $message = ($id) ? $this->message->find($id) : $this->message;
        
        $message->fill($this->formatParams($params));

        if (! $id) {
            $message->from_user_id = \Auth::user()->id;
            $message->uuid         = Str::uuid();
            $message->upload_token = $upload_token;
        }

        $message->is_draft = $is_draft;

        if (! $is_draft) {
            $message->created_at = Carbon::now();
        }

        $message->save();

        if (! $id) {
            $this->upload->store($module, $message->id, $upload_token);
        } else {
            $this->upload->update($module, $message->id, $upload_token);
        }

        $message->has_attachment = $this->upload->getAttachment($module, $message->id)->count() ? 1 : 0;

        $message->save();

        return $message;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'to_user_id' => (isset($params['to_user_id']) && $params['to_user_id']) ? $params['to_user_id'] : null,
            'subject'    => isset($params['subject']) ? $params['subject'] : null,
            'body'       => isset($params['body']) ? scriptStripper($params['body']) : null
        ];

        return $formatted;
    }

    /**
     * Send reply to message.
     *
     * @param array $params
     * @return Message
     */
    public function reply($params = array(), $module)
    {
        $uuid = isset($params['uuid']) ? $params['uuid'] : null;
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        $message = $this->isValidMessage($uuid, \Auth::user());

        $reply               = $this->message;
        $reply->uuid         = Str::uuid();
        $reply->upload_token = $upload_token;
        $reply->subject      = 'Re: '.$message->subject;
        $reply->body         = scriptStripper($params['body']);
        $reply->from_user_id = \Auth::user()->id;
        $reply->reply_id     = $message->id;
        $reply->to_user_id   = ($message->from_user_id === \Auth::user()->id) ? $message->to_user_id : $message->from_user_id;
        $reply->save();

        $this->upload->store($module, $reply->id, $upload_token);

        $reply->has_attachment = $this->upload->getAttachment($module, $reply->id)->count() ? 1 : 0;
        
        $reply->save();

        return $reply;
    }

    /**
     * Load reply of message.
     *
     * @param Message $message
     * @return null
     */
    public function loadReply($message)
    {
        return $this->message->with('userFrom', 'userTo', 'userFrom.profile', 'userTo.profile')->filterByReplyId($message->id)->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByToUserId(\Auth::user()->id)->filterByIsDeletedByReceiver(0);
            })->orWhere(function ($query2) {
                $query2->filterByFromUserId(\Auth::user()->id)->filterByIsDeletedBySender(0);
            });
        })->get();
    }

    /**
     * Get messages from inbox.
     *
     * @return Message
     */
    public function getInbox()
    {
        return $this->message->filterByToUserId(\Auth::user()->id)->filterByIsDeletedByReceiver('0')->filterByIsRead(0)->get();
    }

    /**
     * Get messages from sentbox.
     *
     * @return Message
     */
    public function getSent()
    {
        return $this->message->filterByFromUserId(\Auth::user()->id)->filterByIsDeletedBySender('0')->whereNull('reply_id')->get();
    }

    /**
     * Get messages from draft.
     *
     * @param array $params
     * @return Message
     */
    public function getDraft($params = array())
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $uuid = isset($params['uuid']) ? $params['uuid'] : null;

        $draft = $this->message->with('userTo', 'userFrom')->filterByFromUserId(\Auth::user()->id)->filterByIsDraft(1);

        if ($uuid) {
            $draft = $draft->filterByUuid($uuid)->first();

            if (! $draft) {
                throw ValidationException::withMessages(['message' => trans('message.could_not_find', ['type' => trans('message.draft')])]);
            }

            return $draft;
        }

        if (! isset($params['page_length'])) {
            return $draft->get();
        }

        return $draft->orderBy('created_at', 'desc')->paginate($page_length);
    }

    /**
     * Get messages from trash.
     *
     * @return Message
     */
    public function getTrash()
    {
        return $this->message->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByToUserId(\Auth::user()->id)->filterByIsDeletedByReceiver(1);
            })->orWhere(function ($query2) {
                $query2->filterByFromUserId(\Auth::user()->id)->filterByIsDeletedBySender(1);
            });
        })->get();
    }

    /**
     * Get paginated messages from inbox.
     *
     * @param array $param
     * @return array
     */
    public function getInboxList($params = array())
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        $inbox_message = $this->message->filterByToUserId(\Auth::user()->id)
            ->select(\DB::raw('reply_id'))
            ->filterByIsDeletedByReceiver(0)
            ->whereNotNull('reply_id')
            ->groupBy('reply_id')
            ->get()
            ->pluck('reply_id')
            ->all();

        $message_query = $this->message->with('userFrom', 'userTo')->where(function ($query) use ($inbox_message) {
            $query->where(function ($query1) use ($inbox_message) {
                $query1->filterByToUserId(\Auth::user()->id)
                ->filterByIsDeletedByReceiver(0)
                ->whereNull('reply_id');
            })->orWhereIn('id', $inbox_message);
        });

        $message_query->orderBy('created_at', 'desc');

        $messages = $message_query->paginate($page_length);

        $message_details = array();

        foreach ($messages as $message) {
            $read = ((!$message->is_read && $message->to_user_id === \Auth::user()->id) || ($message->Replies->where('to_user_id', '=', \Auth::user()->id)->where('is_read', '=', 0)->count())) ? 1 : 0;
            $count = ($message->Replies->where('to_user_id', '=', \Auth::user()->id)->where('is_deleted_by_receiver', '=', 0)->count())+($message->Replies->where('from_user_id', '=', \Auth::user()->id)->where('is_deleted_by_sender', '=', 0)->count())+1;
            $message_details[$message->id] = array('read' => $read, 'count' => $count);
        }

        return compact('messages', 'message_details');
    }

    /**
     * Get paginated messages from sentbox.
     *
     * @param array $param
     * @return array
     */
    public function getSentList($params = array())
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        $sent_message = $this->message->filterByFromUserId(\Auth::user()->id)
            ->select(\DB::raw('reply_id'))
            ->filterByIsDeletedBySender(0)
            ->whereNotNull('reply_id')
            ->groupBy('reply_id')
            ->get()
            ->pluck('reply_id')
            ->all();

        $message_query = $this->message->with('userTo', 'userFrom')->where(function ($query) use ($sent_message) {
            $query->where(function ($query1) use ($sent_message) {
                $query1->filterByFromUserId(\Auth::user()->id)
                ->filterByIsDeletedBySender('is_deleted_by_sender', '=', '0')
                ->whereNull('reply_id');
            })->orWhereIn('id', $sent_message);
        });

        $message_query->orderBy('created_at', 'desc');

        $messages = $message_query->paginate($page_length);

        $message_details = array();
        foreach ($messages as $message) {
            $count = ($message->Replies->where('to_user_id', '=', \Auth::user()->id)->where('is_deleted_by_receiver', '=', 0)->count())+($message->Replies->where('from_user_id', '=', \Auth::user()->id)->where('is_deleted_by_sender', '=', 0)->count())+1;
            $message_details[$message->id] = array('count' => $count);
        }

        return compact('messages', 'message_details');
    }

    /**
     * Get paginated messages from important.
     *
     * @param array $param
     * @return Message
     */
    public function getImportantList($params = array())
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->message->with('userTo', 'userFrom')->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByFromUserId(\Auth::user()->id)
                ->filterByIsDeletedBySender(0)
                ->filterByIsImportantBySender(1);
            })->orWhere(function ($query2) {
                $query2->filterByToUserId(\Auth::user()->id)
                ->filterByIsDeletedByReceiver(0)
                ->filterByIsImportantByReceiver(1);
            });
        })->orderBy('created_at', 'desc')->paginate($page_length);
    }

    /**
     * Get paginated messages from trash.
     *
     * @param array $param
     * @return Message
     */
    public function getTrashList($params = array())
    {
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->message->with('userTo', 'userFrom')->where(function ($query) {
            $query->where(function ($query1) {
                $query1->filterByFromUserId(\Auth::user()->id)
                ->filterByIsDeletedBySender(1);
            })->orWhere(function ($query2) {
                $query2->filterByToUserId(\Auth::user()->id)
                ->filterByIsDeletedByReceiver(1);
            });
        })->orderBy('created_at', 'desc')->paginate($page_length);
    }

    /**
     * Mark message as read.
     *
     * @param Message $message
     * @return null
     */
    public function markAsRead($message)
    {
        if (! $message->Replies->count()) {
            return;
        }

        $this->message->filterByReplyId($message->id)->filterByToUserId(\Auth::user()->id)->update(['is_read' => 1]);

        if ($message->to_user_id === \Auth::user()->id) {
            $message->is_read = 1;
            $message->save();
        }
    }

    /**
     * Validate draft before delete.
     *
     * @param string $uuid
     * @return Message
     */
    public function deletableDraft($uuid)
    {
        $draft = $this->message->filterByUuid($uuid)->filterByIsDraft(1)->filterByFromUserId(\Auth::user()->id)->first();

        if (! $draft) {
            throw ValidationException::withMessages(['message' => trans('message.could_not_find', ['type' => trans('message.draft')])]);
        }

        return $draft;
    }

    /**
     * Trash a message.
     *
     * @param Message $message
     * @return Message
     */
    public function trash($message)
    {
        if ($message->to_user_id === \Auth::user()->id) {
            $message->is_deleted_by_receiver = 1;
        } else {
            $message->is_deleted_by_sender = 1;
        }
        $message->save();

        if ($message->Replies->count()) {
            $sender_messages = $message->Replies->where('from_user_id', '=', \Auth::user()->id)->pluck('id');
            $this->message->whereIn('id', $sender_messages)->update(['is_deleted_by_sender' => 1]);
            $receiver_messages = $message->Replies->where('to_user_id', '=', \Auth::user()->id)->pluck('id');
            $this->message->whereIn('id', $receiver_messages)->update(['is_deleted_by_receiver' => 1]);
        }

        return $message;
    }

    /**
     * Restore a message from trash.
     *
     * @param Message $message
     * @return Message
     */
    public function restore($message)
    {
        if ($message->reply_id != null && (($message->Reply->to_user_id === \Auth::user()->id && $message->Reply->is_deleted_by_receiver > 0) || ($message->Reply->from_user_id === \Auth::user()->id && $message->Reply->is_deleted_by_sender > 0))) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_link')]);
        }

        if ($message->to_user_id === \Auth::user()->id) {
            $message->is_deleted_by_receiver = 0;
        } else {
            $message->is_deleted_by_sender = 0;
        }
        $message->save();

        if ($message->Replies->count()) {
            $sender_messages = $message->Replies->where('from_user_id', '=', \Auth::user()->id)->pluck('id');
            $this->message->whereIn('id', $sender_messages)->update(['is_deleted_by_sender' => 0]);
            $receiver_messages = $message->Replies->where('to_user_id', '=', \Auth::user()->id)->pluck('id');
            $this->message->whereIn('id', $receiver_messages)->update(['is_deleted_by_receiver' => 0]);
        }

        return $message;
    }

    /**
     * Delete a message.
     *
     * @param Message $message
     * @param string $module
     * @return null
     */
    public function delete($message, $module)
    {
        $this->upload->delete($module, $message->id);

        if ($message->to_user_id === \Auth::user()->id) {
            $message->is_deleted_by_receiver = 2;
        } else {
            $message->is_deleted_by_sender = 2;
        }
        $message->save();

        if ($message->Replies->count()) {
            $sender_messages = $message->Replies->where('from_user_id', '=', \Auth::user()->id)->pluck('id');

            $this->upload->bulkDelete($module, $sender_messages);

            $this->message->whereIn('id', $sender_messages)->update(['is_deleted_by_sender' => 2]);

            $receiver_messages = $message->Replies->where('to_user_id', '=', \Auth::user()->id)->pluck('id');

            $this->upload->bulkDelete($module, $receiver_messages);

            $this->message->whereIn('id', $receiver_messages)->update(['is_deleted_by_receiver' => 2]);
        }
    }
}
