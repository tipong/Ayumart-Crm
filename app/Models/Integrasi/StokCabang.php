<?php

namespace App\Models\Integrasi;

use Illuminate\Database\Eloquent\Model;

class StokCabang extends Model
{
    protected $connection = 'mysql_integrasi';
    protected $table = 'tb_stok_cabang';
    protected $primaryKey = 'id_stok_cabang';
    public $timestamps = false;

    protected $fillable = [
        'id_stok_cabang',
        'id_produk',
        'id_detail_cabang',
        'total_stok',
        'stok_minimum',
    ];

    protected $casts = [
        'total_stok' => 'integer',
        'stok_minimum' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function detailCabang()
    {
        return $this->belongsTo(DetailCabang::class, 'id_detail_cabang', 'id_detail_cabang');
    }

    /**
     * Kurangi stok
     */
    public function kurangiStok($jumlah)
    {
        $this->total_stok -= $jumlah;
        $this->save();
    }

    /**
     * Tambah stok
     */
    public function tambahStok($jumlah)
    {
        $this->total_stok += $jumlah;
        $this->save();
    }

    /**
     * Cek apakah stok mencukupi
     */
    public function cukup($jumlah)
    {
        return $this->total_stok >= $jumlah;
    }

    /**
     * Cek apakah stok di bawah minimum
     */
    public function isDiBawahMinimum()
    {
        return $this->total_stok < $this->stok_minimum;
    }
}
