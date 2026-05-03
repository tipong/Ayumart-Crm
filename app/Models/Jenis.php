<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $table = 'tb_jenis';
    protected $primaryKey = 'id_jenis';

    protected $fillable = [
        'id_jenis',
        'nama_jenis',
        'deskripsi_jenis',
    ];

    // Relasi dengan produk
    public function produks()
    {
        return $this->hasMany(Product::class, 'id_jenis', 'id_jenis');
    }

    // Alias untuk produk
    public function products()
    {
        return $this->produks();
    }
}
