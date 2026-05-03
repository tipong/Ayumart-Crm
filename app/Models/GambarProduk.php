<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GambarProduk extends Model
{
    protected $table = 'tb_gambar_produk';
    protected $primaryKey = 'id_gambar';

    protected $fillable = [
        'id_produk',
        'url_gambar',
    ];

    // Relasi dengan produk
    public function produk()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }
}
