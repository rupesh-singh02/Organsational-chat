<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialChatDocument extends Model
{
    use HasFactory;

    protected $table = 'official_chat_document';

    protected $fillable = [
        'official_chat_id',
        'content',
        'type',
        'doc_type',
        'size',
    ];

    public function chat()
    {
        return $this->belongsTo(OfficialChat::class, 'official_chat_id');
    }
}
