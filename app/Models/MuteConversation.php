<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MuteConversation extends Model
{
    use HasFactory;
    protected $table = 'mute_conversations';
    protected $guarded = ['id'];
}
