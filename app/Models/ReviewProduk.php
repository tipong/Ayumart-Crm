<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewProduk extends Model
{
    protected $table = 'tb_review_produk';
    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_produk',
        'id_pelanggan',
        'id_transaksi',
        'rating',
        'komentar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relasi dengan produk
    public function produk()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    // Relasi dengan pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi dengan transaksi
    public function transaksi()
    {
        return $this->belongsTo(Order::class, 'id_transaksi', 'id_transaksi');
    }
}
