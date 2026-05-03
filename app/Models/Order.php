<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false; // IMPORTANT: tb_transaksi tidak punya created_at & updated_at

    protected $fillable = [
        'id_pelanggan',
        'id_cabang',
        'address_id',  // ✅ ADDED: Store shipping address for kurir method
        'kode_transaksi',
        'tanggal_transaksi',  // ADDED: New column
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
        'payment_expired_at',
        'last_payment_check_at',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'biaya_membership' => 'decimal:2',
        'tanggal_transaksi' => 'datetime:Y-m-d H:i:s',  // ADDED: Cast tanggal_transaksi to datetime with format
        'payment_expired_at' => 'datetime:Y-m-d H:i:s',
        'last_payment_check_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Get the address
     */
    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'address_id');
    }

    /**
     * Get the branch
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    /**
     * Get order items
     */
    public function items()
    {
        return $this->hasMany(TransactionDetail::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Get order details (alias for items - for compatibility)
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Get cancellation request
     */
    public function cancellation()
    {
        return $this->hasOne(PembatalanTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Get shipment/delivery info
     */
    public function pengiriman()
    {
        return $this->hasOne(Shipment::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Get shipment info (alias for compatibility)
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Check if order is paid
     */
    public function isPaid()
    {
        return $this->status_pembayaran === 'sudah_bayar';
    }

    /**
     * Check if order is for pickup
     */
    public function isPickup()
    {
        return $this->metode_pengiriman === 'ambil_sendiri';
    }

    /**
     * Check if order is for delivery
     */
    public function isDelivery()
    {
        return $this->metode_pengiriman === 'kurir';
    }

    /**
     * Get final total including shipping
     */
    public function getFinalTotalAttribute()
    {
        return $this->total_harga - $this->total_diskon + $this->ongkir;
    }

    /**
     * Get total amount (method version for compatibility)
     */
    public function getTotalAmount()
    {
        return $this->total_harga - $this->total_diskon + $this->ongkir;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status_pembayaran) {
            'sudah_bayar' => 'success',
            'belum_bayar' => 'warning',
            'kadaluarsa' => 'danger',
            default => 'secondary'
        };
    }
}
