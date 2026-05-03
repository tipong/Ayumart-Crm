<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'tb_pelanggan';
    protected $primaryKey = 'id_pelanggan';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_pelanggan',
        'no_tlp_pelanggan',
        'alamat',
        'status_pelanggan',
    ];

    protected $casts = [
        'status_pelanggan' => 'string',
    ];

    /**
     * Get the user (authentication) data
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi dengan transaksi
    public function transaksis()
    {
        return $this->hasMany(Order::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi dengan keranjang
    public function carts()
    {
        return $this->hasMany(Cart::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi dengan wishlist
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi dengan alamat customer
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'id_pelanggan', 'id_pelanggan');
    }
}
