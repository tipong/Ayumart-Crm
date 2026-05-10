<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Integrasi\Produk as ProdukIntegrasi;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'tb_detail_cart';
    protected $primaryKey = 'id_detail_cart';

    protected $fillable = [
        'id_pelanggan',
        'id_produk',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    // Relasi dengan pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relasi dengan produk dari database integrasi (PRIMARY)
     * Ini adalah relasi utama untuk mengambil data produk
     * Karena tb_detail_cart.id_produk sekarang langsung merujuk ke integrasi DB
     */
    public function product()
    {
        return $this->belongsTo(ProdukIntegrasi::class, 'id_produk', 'id_produk');
    }

    /**
     * Alias untuk relasi product
     */
    public function produkIntegrasi()
    {
        return $this->product();
    }

    /**
     * Legacy: Relasi ke produk CRM (DEPRECATED - JANGAN DIGUNAKAN)
     * Method ini hanya untuk backward compatibility
     */
    public function productCrm()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    /**
     * Get produk - langsung dari relasi
     * @deprecated Gunakan $cart->product langsung
     */
    public function getProduk()
    {
        // Langsung return relasi product (yang sudah menunjuk ke integrasi)
        return $this->product;
    }

    // Helper untuk menghitung subtotal dengan harga aktual (termasuk diskon jika aktif)
    public function getSubtotal()
    {
        $produk = $this->product; // Langsung dari relasi

        if (!$produk) {
            return 0;
        }

        $customerTier = null;
        if ($this->pelanggan && $this->pelanggan->user && $this->pelanggan->user->membership) {
            $customerTier = $this->pelanggan->user->membership->tier;
        }

        // Produk dari integrasi DB - gunakan harga_final atau getCurrentPrice()
        $price = method_exists($produk, 'getCurrentPrice')
            ? $produk->getCurrentPrice($customerTier)
            : ($produk->harga_final ?? $produk->harga_produk);

        return $price * $this->qty;
    }

    // Helper untuk menghitung subtotal dengan harga original (tanpa diskon)
    public function getOriginalSubtotal()
    {
        $produk = $this->product; // Langsung dari relasi

        if (!$produk) {
            return 0;
        }

        return $produk->harga_produk * $this->qty;
    }

    // Helper untuk menghitung total penghematan dari diskon
    public function getSavings()
    {
        return $this->getOriginalSubtotal() - $this->getSubtotal();
    }

    // Accessor untuk produk (untuk digunakan di views)
    // Ini akan otomatis dipanggil saat mengakses $cart->produk
    public function getProdukAttribute()
    {
        return $this->getProduk();
    }
}
