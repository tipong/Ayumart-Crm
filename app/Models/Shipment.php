<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'tb_pengiriman';
    protected $primaryKey = 'id_pengiriman';
    public $timestamps = false;

    protected $fillable = [
        'id_address',
        'id_transaksi',
        'id_staff',
        'no_resi',
        'nama_penerima',
        'alamat_penerima',
        'status_pengiriman',
        'tgl_kirim',
        'tgl_sampai',
    ];

    protected $casts = [
        'tgl_kirim' => 'datetime',
        'tgl_sampai' => 'datetime',
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi', 'id_transaksi');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'id_staff', 'id_staff');
    }

    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'id_address', 'id');
    }

    // Accessor for backward compatibility
    public function getKurirAttribute()
    {
        return $this->staff ? $this->staff->user : null;
    }

    public function getIdKurirAttribute()
    {
        return $this->staff ? $this->staff->id_user : null;
    }
}
