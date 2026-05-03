<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'tb_staff';
    protected $primaryKey = 'id_staff';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_staff',
        'email_staff',
        'posisi_staff',
        'profil_staff',
        'no_tlp_staff',
        'status_akun',
    ];

    /**
     * Get the user (authentication) data
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi dengan pengiriman
    public function pengirimans()
    {
        return $this->hasMany(Shipment::class, 'id_staff', 'id_staff');
    }
}

