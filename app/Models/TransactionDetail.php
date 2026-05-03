<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Integrasi\Produk as ProdukIntegrasi;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'tb_detail_transaksi';
    protected $primaryKey = 'id_detail_transaksi';

    protected $fillable = [
        'id_transaksi',
        'id_produk',
        'qty',
        'harga_item',
        'subtotal',
        'diskon_item',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_item' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'diskon_item' => 'decimal:2',
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Relasi dengan produk dari database integrasi (PRIMARY)
     * Ini adalah relasi utama karena id_produk sekarang langsung merujuk ke integrasi DB
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

    // Helpers
    public function getSubtotal()
    {
        // Return subtotal yang sudah disimpan di database
        return $this->subtotal ?? 0;
    }

    // Accessor untuk backward compatibility
    public function getHargaAttribute()
    {
        return $this->harga_item;
    }

    public function getDiskonAttribute()
    {
        return $this->diskon_item;
    }
}
