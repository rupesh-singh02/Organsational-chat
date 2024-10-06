<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialChat extends Model
{
    protected $table = 'official_chat';

    protected $guarded = [''];

    protected $fillable = [
        'from_staff_id',
        'to_staff_id',
        'reply_id',
        'message_type',
        'view_status',
    ];

    public function message()
    {
        return $this->hasOne(OfficialChatText::class);
    }

    public function replyContent()
    {
        return $this->belongsTo(OfficialChat::class, 'reply_id');
    }

    public function replyDetails()
    {
        return $this->replyContent()->with(['content', 'imageContent', 'videoContent', 'documentContent']);
    }

    public function content()
    {
        return $this->hasOne(OfficialChatText::class, 'official_chat_id');
    }

    public function imageContent()
    {
        return $this->hasOne(OfficialChatImage::class, 'official_chat_id');
    }

    public function videoContent()
    {
        return $this->hasOne(OfficialChatVideo::class, 'official_chat_id');
    }

    public function documentContent()
    {
        return $this->hasOne(OfficialChatDocument::class, 'official_chat_id');        
    }
    
    public function sender()
    {
        return $this->belongsTo(Staff::class, 'from_staff_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Staff::class, 'to_staff_id');
    }
}
