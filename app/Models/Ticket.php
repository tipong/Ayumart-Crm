<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'resolved_at',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id_user');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }
}
