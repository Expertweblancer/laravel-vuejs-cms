<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
                            'to_user_id',
                            'subject',
                            'body'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'messages';

    public function userTo()
    {
        return $this->belongsTo('App\User', 'to_user_id');
    }

    public function userFrom()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Message', 'reply_id');
    }

    public function reply()
    {
        return $this->belongsTo('App\Message', 'reply_id');
    }

    public function scopeFilterByIsDraft($q, $is_draft)
    {
        return $q->where('is_draft', '=', $is_draft);
    }

    public function scopeFilterByIsDeletedByReceiver($q, $is_deleted_by_receiver)
    {
        return $q->where('is_deleted_by_receiver', '=', $is_deleted_by_receiver);
    }

    public function scopeFilterByIsDeletedBySender($q, $is_deleted_by_sender)
    {
        return $q->where('is_deleted_by_sender', '=', $is_deleted_by_sender);
    }

    public function scopeFilterByIsImportantByReceiver($q, $is_important_by_receiver)
    {
        return $q->where('is_important_by_receiver', '=', $is_important_by_receiver);
    }

    public function scopeFilterByIsImportantBySender($q, $is_important_by_sender)
    {
        return $q->where('is_important_by_sender', '=', $is_important_by_sender);
    }

    public function scopeFilterByIsRead($q, $is_read)
    {
        return $q->where('is_read', '=', $is_read);
    }

    public function scopeFilterByUuid($q, $uuid = null)
    {
        if (! $uuid) {
            return $q;
        }

        return $q->where('uuid', '=', $uuid);
    }

    public function scopeFilterById($q, $id = null)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByFromUserId($q, $from_user_id = null)
    {
        if (! $from_user_id) {
            return $q;
        }

        return $q->where('from_user_id', '=', $from_user_id);
    }

    public function scopeFilterByToUserId($q, $to_user_id = null)
    {
        if (! $to_user_id) {
            return $q;
        }

        return $q->where('to_user_id', '=', $to_user_id);
    }

    public function scopeFilterByReplyId($q, $reply_id = null)
    {
        if (! $reply_id) {
            return $q;
        }

        return $q->where('reply_id', '=', $reply_id);
    }
}
