<?php

namespace App\Services;

use App\Models\Integrasi\Produk;
use App\Models\Integrasi\StokCabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IntegrasiProdukService
{
    /**
     * Get produk dari database integrasi
     */
    public function getProduk($idProduk)
    {
        return Produk::find($idProduk);
    }

    /**
     * Get all produk aktif
     */
    public function getAllProdukAktif()
    {
        return Produk::where('status_produk', 'aktif')
            ->with(['jenis'])
            ->orderBy('nama_produk', 'asc')
            ->get();
    }

    /**
     * Get produk by jenis
     */
    public function getProdukByJenis($idJenis)
    {
        return Produk::where('status_produk', 'aktif')
            ->where('id_jenis', $idJenis)
            ->with(['jenis'])
            ->orderBy('nama_produk', 'asc')
            ->get();
    }

    /**
     * Get produk dengan stok by cabang
     * Return produk beserta informasi stok di cabang tertentu
     */
    public function getProdukWithStokByCabang($idCabang, $filters = [])
    {
        $query = Produk::where('status_produk', 'aktif')
            ->with(['jenis']);

        // Apply filters
        if (isset($filters['id_jenis'])) {
            $query->where('id_jenis', $filters['id_jenis']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_produk', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode_produk', 'like', '%' . $filters['search'] . '%');
            });
        }

        $products = $query->get();

        // Tambahkan informasi stok untuk setiap produk
        foreach ($products as $product) {
            $stok = $this->getStokProdukCabang($product->id_produk, $idCabang);
            $product->stok_cabang = $stok;
            $product->is_available = $stok > 0;
        }

        return $products;
    }

    /**
     * Get produk promo dengan stok by cabang
     */
    public function getProdukPromoWithStokByCabang($idCabang, $limit = 6)
    {
        $products = Produk::where('status_produk', 'aktif')
            ->where('is_diskon_active', 1)
            ->whereNotNull('harga_diskon')
            ->whereNotNull('tanggal_akhir_diskon')
            ->where('tanggal_akhir_diskon', '>=', now()->startOfDay())
            ->with(['jenis'])
            ->orderBy('harga_diskon', 'desc')
            ->limit($limit)
            ->get();

        // Tambahkan informasi stok untuk setiap produk
        foreach ($products as $product) {
            $stok = $this->getStokProdukCabang($product->id_produk, $idCabang);
            $product->stok_cabang = $stok;
            $product->is_available = $stok > 0;
        }

        return $products;
    }

    /**
     * Get stok produk di cabang tertentu
     */
    public function getStokProdukCabang($idProduk, $idCabang)
    {
        $stok = StokCabang::where('id_produk', $idProduk)
            ->where('id_cabang', $idCabang)
            ->first();

        $totalStok = $stok ? $stok->total_stok : 0;

        // Log untuk debugging
        Log::debug('Get Stok Produk Cabang', [
            'id_produk' => $idProduk,
            'id_cabang' => $idCabang,
            'total_stok' => $totalStok
        ]);

        return $totalStok;
    }

    /**
     * Cek apakah stok mencukupi
     */
    public function cekStokMencukupi($idProduk, $idCabang, $jumlah)
    {
        $stok = $this->getStokProdukCabang($idProduk, $idCabang);
        return $stok >= $jumlah;
    }

    /**
     * Kurangi stok produk di cabang
     * Digunakan saat pembelian sukses
     */
    public function kurangiStok($idProduk, $idCabang, $jumlah)
    {
        try {
            DB::connection('mysql_integrasi')->beginTransaction();

            $stok = StokCabang::where('id_produk', $idProduk)
                ->where('id_cabang', $idCabang)
                ->lockForUpdate()
                ->first();

            if (!$stok) {
                throw new \Exception("Stok produk tidak ditemukan di cabang ini");
            }

            if (!$stok->cukup($jumlah)) {
                throw new \Exception("Stok tidak mencukupi. Tersedia: {$stok->total_stok}, Diminta: {$jumlah}");
            }

            $stok->kurangiStok($jumlah);

            DB::connection('mysql_integrasi')->commit();

            Log::info("Stok dikurangi: Produk ID {$idProduk}, Cabang ID {$idCabang}, Jumlah: {$jumlah}, Sisa: {$stok->total_stok}");

            // Cek apakah stok di bawah minimum
            if ($stok->isDiBawahMinimum()) {
                Log::warning("PERINGATAN: Stok produk ID {$idProduk} di cabang ID {$idCabang} di bawah minimum! Sisa: {$stok->total_stok}, Minimum: {$stok->stok_minimum}");
            }

            return true;
        } catch (\Exception $e) {
            DB::connection('mysql_integrasi')->rollBack();
            Log::error("Gagal mengurangi stok: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tambah stok produk di cabang
     * Digunakan saat ada pengembalian/pembatalan
     */
    public function tambahStok($idProduk, $idCabang, $jumlah)
    {
        try {
            DB::connection('mysql_integrasi')->beginTransaction();

            $stok = StokCabang::where('id_produk', $idProduk)
                ->where('id_cabang', $idCabang)
                ->lockForUpdate()
                ->first();

            if (!$stok) {
                throw new \Exception("Stok produk tidak ditemukan di cabang ini");
            }

            $stok->tambahStok($jumlah);

            DB::connection('mysql_integrasi')->commit();

            Log::info("Stok ditambah: Produk ID {$idProduk}, Cabang ID {$idCabang}, Jumlah: {$jumlah}, Total: {$stok->total_stok}");

            return true;
        } catch (\Exception $e) {
            DB::connection('mysql_integrasi')->rollBack();
            Log::error("Gagal menambah stok: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get daftar produk dengan stok per cabang
     */
    public function getProdukDenganStok($idCabang = null)
    {
        $produk = Produk::where('status_produk', 'aktif')
            ->with(['jenis'])
            ->get();

        if ($idCabang) {
            $produk->load(['stokCabang' => function ($query) use ($idCabang) {
                $query->where('id_detail_cabang', $idCabang);
            }]);
        } else {
            $produk->load('stokCabang');
        }

        return $produk;
    }

    /**
     * Sync data produk untuk ditampilkan di frontend
     * Mengambil data dari database integrasi dan format untuk tampilan
     */
    public function getProdukForDisplay($idCabang = null)
    {
        $produkList = $this->getProdukDenganStok($idCabang);

        return $produkList->map(function ($produk) use ($idCabang) {
            $stok = 0;

            if ($idCabang) {
                $stokData = $produk->stokCabang->first();
                $stok = $stokData ? $stokData->total_stok : 0;
            } else {
                // Jika tidak ada cabang spesifik, jumlahkan semua stok
                $stok = $produk->stokCabang->sum('total_stok');
            }

            return [
                'id_produk' => $produk->id_produk,
                'kode_produk' => $produk->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'deskripsi_produk' => $produk->deskripsi_produk,
                'nama_jenis' => $produk->jenis ? $produk->jenis->nama_jenis : '-',
                'harga_produk' => $produk->harga_produk,
                'harga_diskon' => $produk->harga_diskon,
                'harga_final' => $produk->harga_final,
                'is_diskon_active' => $produk->is_diskon_active,
                'foto_produk' => $produk->foto_produk,
                'berat_produk' => $produk->berat_produk,
                'satuan' => $produk->satuan,
                'stok_tersedia' => $stok,
                'status_produk' => $produk->status_produk,
            ];
        });
    }
}
