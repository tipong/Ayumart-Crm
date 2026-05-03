<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatalanTransaksi extends Model
{
    protected $table = 'tb_pembatalan_transaksi';
    protected $primaryKey = 'id_pembatalan_transaksi';

    protected $fillable = [
        'id_transaksi',
        'alasan_pembatalan',
        'catatan_admin',
        'status_pembatalan',
        'diproses_oleh',
    ];

    // Relasi dengan transaksi
    public function transaksi()
    {
        return $this->belongsTo(Order::class, 'id_transaksi', 'id_transaksi');
    }

    // Relasi dengan admin yang memproses
    public function admin()
    {
        return $this->belongsTo(User::class, 'diproses_oleh', 'id_user');
    }
}
