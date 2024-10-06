<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialChatVideo extends Model
{
    use HasFactory;

    protected $table = 'official_chat_video';

    protected $fillable = [
        'official_chat_id',
        'content',
        'type',
        'size',
    ];

    protected $casts = [
        'content' => 'json',
        'type' => 'json',
        'size' => 'json',
    ];
    
    public function chat()
    {
        return $this->belongsTo(OfficialChat::class, 'official_chat_id');
    }
}
