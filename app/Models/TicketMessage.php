<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');  // FIXED: Added id_user as owner key
    }
}
