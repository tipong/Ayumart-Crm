<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Integrasi\Produk as ProdukIntegrasi;

class Wishlist extends Model
{
    protected $table = 'tb_wishlist';
    protected $primaryKey = 'id_wishlist';

    protected $fillable = [
        'id_pelanggan',
        'id_produk'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    // Relasi dengan produk dari database CRM (fallback)
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    // Relasi dengan produk dari database integrasi (UTAMA)
    public function produkIntegrasi()
    {
        return $this->hasOne(ProdukIntegrasi::class, 'id_produk', 'id_produk');
    }

    // Get produk dengan prioritas dari database integrasi
    public function getProduk()
    {
        // Coba ambil dari database integrasi dulu
        $produkIntegrasi = ProdukIntegrasi::find($this->id_produk);

        if ($produkIntegrasi) {
            return $produkIntegrasi;
        }

        // Fallback ke database CRM jika tidak ada di integrasi
        return $this->product;
    }

    // Accessor untuk produk (untuk digunakan di views)
    // Ini akan otomatis dipanggil saat mengakses $wishlist->produk
    public function getProdukAttribute()
    {
        return $this->getProduk();
    }
}
