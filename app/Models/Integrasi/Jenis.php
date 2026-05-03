<?php

namespace App\Models\Integrasi;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $connection = 'mysql_integrasi';
    protected $table = 'tb_jenis';
    protected $primaryKey = 'id_jenis';
    public $timestamps = false;

    protected $fillable = [
        'id_jenis',
        'nama_jenis',
        'deskripsi_jenis',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_jenis', 'id_jenis');
    }
}
