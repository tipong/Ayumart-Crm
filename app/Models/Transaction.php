<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'id_cabang',
        'kode_transaksi',
        'midtrans_order_id',
        'snap_token',
        'total_harga',
        'total_diskon',
        'ongkir',
        'biaya_membership',
        'status_pembayaran',
        'status_pengiriman',
        'metode_pengiriman',
        'catatan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'biaya_membership' => 'decimal:2',
    ];

    // Relationships
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'id_transaksi', 'id_transaksi');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'id_transaksi', 'id_transaksi');
    }

    public function cancellation()
    {
        return $this->hasOne(PembatalanTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_transaksi';
    }

    // Helpers
    public function getTotalAmount()
    {
        $ongkir = is_numeric($this->ongkir) ? (float)$this->ongkir : 0;
        return $this->total_harga - $this->total_diskon + $ongkir + ($this->biaya_membership ?? 0);
    }

    // Accessors for backwards compatibility
    public function getStatusAttribute()
    {
        // Map status_pembayaran to a general status
        return $this->attributes['status_pembayaran'] ?? 'unknown';
    }

    public function getIdAttribute()
    {
        return $this->attributes['id_transaksi'] ?? null;
    }

    public function getItemsAttribute()
    {
        // Alias for details relationship
        return $this->details;
    }

    public function getTotalPriceAttribute()
    {
        // Alias for total amount calculation
        return $this->getTotalAmount();
    }

    public function getOngkirDecimalAttribute()
    {
        // Convert ongkir string to decimal for calculations
        return is_numeric($this->ongkir) ? (float)$this->ongkir : 0;
    }

    public function getTglTransaksiAttribute()
    {
        // For backward compatibility, return created_at if exists in details
        // Otherwise return null or estimate from kode_transaksi
        return null;
    }
}
