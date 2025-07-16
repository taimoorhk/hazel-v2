<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'content_role',
        'start_time',
        'end_time',
        'total_time_seconds',
        'conversation_id',
    ];

    /**
     * Get the conversations that owns the message.
     */
    public function conversations(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
