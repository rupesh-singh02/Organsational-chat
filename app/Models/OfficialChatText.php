<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialChatText extends Model
{
    use HasFactory;

    protected $table = 'official_chat_text';

    protected $fillable = [
        'official_chat_id',
        'content',
    ];

    public function chat()
    {
        return $this->belongsTo(OfficialChat::class, 'official_chat_id');
    }

}
