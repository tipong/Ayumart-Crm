<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'id_pelanggan',
        'label',
        'alamat_lengkap',
        'kota',
        'kecamatan',
        'kode_pos',
        'nama_penerima',
        'no_telp_penerima',
        'latitude',
        'longitude',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the pelanggan that owns the address
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Set this address as default and unset others
     */
    public function setAsDefault()
    {
        // Unset all other defaults for this pelanggan
        self::where('id_pelanggan', $this->id_pelanggan)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [
            $this->alamat_lengkap,
            $this->kecamatan,
            $this->kota,
            $this->kode_pos
        ];

        return implode(', ', array_filter($parts));
    }
}
