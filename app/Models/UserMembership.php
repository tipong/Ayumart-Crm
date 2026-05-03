<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
    protected $table = 'tb_user_membership';
    protected $primaryKey = 'id_user_membership';
    public $timestamps = true;

    protected $fillable = [
        'id_pelanggan',
        'id_membership',
        'status',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
}
