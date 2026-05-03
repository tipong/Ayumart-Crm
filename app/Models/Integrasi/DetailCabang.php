<?php

namespace App\Models\Integrasi;

use Illuminate\Database\Eloquent\Model;

class DetailCabang extends Model
{
    protected $connection = 'mysql_integrasi';
    protected $table = 'tb_detail_cabang';
    protected $primaryKey = 'id_detail_cabang';
    public $timestamps = false;

    protected $fillable = [
        'id_detail_cabang',
        'nama_cabang',
        'alamat',
    ];

    public function stokCabang()
    {
        return $this->hasMany(StokCabang::class, 'id_detail_cabang', 'id_detail_cabang');
    }
}
