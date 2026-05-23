<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Cabang;
use App\Models\CustomerAddress;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckoutCourierCoordinatesTest extends TestCase
{
    use RefreshDatabase {
        refreshTestDatabase as baseRefreshTestDatabase;
    }

    protected $user;
    protected $pelanggan;
    protected $branch;
    protected $incompleteAddress;
    protected $completeAddress;
    protected $product;

    /**
     * Override refreshTestDatabase to run schema queries before migrations run.
     */
    protected function refreshTestDatabase()
    {
        // Ensure tb_produk exists on mysql_integrasi connection before migrations run
        // so that migrations that alter it don't fail.
        if (!Schema::connection('mysql_integrasi')->hasTable('tb_produk')) {
            Schema::connection('mysql_integrasi')->create('tb_produk', function ($table) {
                $table->id('id_produk');
                $table->string('kode_produk', 50)->unique();
                $table->string('nama_produk', 100);
                $table->text('deskripsi_produk')->nullable();
                $table->unsignedInteger('id_jenis')->nullable();
                $table->decimal('harga_produk', 12, 2);
                $table->decimal('harga_diskon', 12, 2)->nullable();
                $table->string('berat_produk', 50)->nullable();
                $table->string('foto_produk')->nullable();
                $table->string('status_produk', 50)->default('aktif');
                $table->string('satuan', 50)->nullable();
                $table->decimal('harga_beli', 12, 2)->nullable();
                $table->timestamps();
            });
        }

        // Call base migration setup
        $this->baseRefreshTestDatabase();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create branch
        $this->branch = Cabang::create([
            'kode_cabang' => 'AYM-001',
            'nama_cabang' => 'Cabang Test',
            'alamat' => 'Alamat Test',
            'kelurahan' => 'Kelurahan Test',
            'kecamatan' => 'Kecamatan Test',
            'kota' => 'Kota Test',
            'latitude' => -8.650000,
            'longitude' => 115.216667,
            'no_telepon' => '081234567890',
            'is_active' => true,
        ]);

        // Create user
        $this->user = User::create([
            'id_role' => 5, // pelanggan
            'email' => 'customer-coord@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create pelanggan
        $this->pelanggan = Pelanggan::create([
            'id_user' => $this->user->id_user,
            'nama_pelanggan' => 'Pelanggan Test',
            'no_tlp_pelanggan' => '081234567890',
            'alamat_lengkap' => 'Alamat Lengkap Test',
        ]);

        // Create address without coordinates
        $this->incompleteAddress = CustomerAddress::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'label' => 'Kantor Tanpa Peta',
            'alamat_lengkap' => 'Jalan Tanpa Koordinat',
            'kota' => 'Denpasar',
            'nama_penerima' => 'Budi',
            'no_telp_penerima' => '081234567890',
            'latitude' => null,
            'longitude' => null,
        ]);

        // Create address with coordinates
        $this->completeAddress = CustomerAddress::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'label' => 'Rumah Ada Peta',
            'alamat_lengkap' => 'Jalan Ada Koordinat',
            'kota' => 'Denpasar',
            'nama_penerima' => 'Budi',
            'no_telp_penerima' => '081234567890',
            'latitude' => -8.660000,
            'longitude' => 115.226667,
        ]);

        // Ensure tb_stok_cabang exists on mysql_integrasi connection for testing
        if (!Schema::connection('mysql_integrasi')->hasTable('tb_stok_cabang')) {
            Schema::connection('mysql_integrasi')->create('tb_stok_cabang', function ($table) {
                $table->id('id_stok_cabang');
                $table->unsignedInteger('id_produk');
                $table->unsignedInteger('id_cabang');
                $table->integer('total_stok')->default(0);
                $table->integer('stok_minimum')->default(0);
                $table->timestamps();
            });
        }

        // Clear existing product and stocks in mysql_integrasi to avoid duplicate key issues if tables persistent
        DB::connection('mysql_integrasi')->table('tb_stok_cabang')->truncate();
        DB::connection('mysql_integrasi')->table('tb_produk')->truncate();

        // Create mock integration product
        $this->product = \App\Models\Integrasi\Produk::create([
            'id_produk' => 1,
            'kode_produk' => 'PRD-TEST-001',
            'nama_produk' => 'Produk Test',
            'harga_produk' => 10000,
            'status_produk' => 'aktif',
        ]);

        // Create mock local CRM product
        DB::table('tb_produk')->insert([
            'id_produk' => 1,
            'id_produk_integrasi' => 1,
            'kode_produk' => 'PRD-TEST-001',
            'nama_produk' => 'Produk Test',
            'harga_produk' => 10000,
            'status_produk' => 'aktif',
        ]);

        // Create mock stock in mysql_integrasi connection
        DB::connection('mysql_integrasi')->table('tb_stok_cabang')->insert([
            'id_produk' => $this->product->id_produk,
            'id_cabang' => $this->branch->id_cabang,
            'total_stok' => 100,
            'stok_minimum' => 5,
        ]);

        // Create cart item
        Cart::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'id_produk' => $this->product->id_produk,
            'qty' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        // Drop integration tables created dynamically for testing
        Schema::connection('mysql_integrasi')->dropIfExists('tb_stok_cabang');
        Schema::connection('mysql_integrasi')->dropIfExists('tb_produk');

        parent::tearDown();
    }

    /**
     * Test placing courier order with incomplete coordinates is blocked.
     */
    public function test_place_courier_order_fails_if_address_lacks_coordinates()
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'nearest_branch_id' => $this->branch->id_cabang,
                'nearest_branch' => [
                    'id_cabang' => $this->branch->id_cabang,
                    'nama_cabang' => $this->branch->nama_cabang,
                    'distance' => null,
                ]
            ])
            ->post(route('order.place'), [
                'metode_pengiriman' => 'kurir',
                'address_id' => $this->incompleteAddress->id,
                'shipping_cost' => 0,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Koordinat lokasi alamat pengiriman kurang lengkap. Silakan edit alamat untuk menyematkan lokasi di peta.');
    }

    /**
     * Test placing courier order with valid coordinates succeeds.
     */
    public function test_place_courier_order_succeeds_if_address_has_coordinates()
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'nearest_branch_id' => $this->branch->id_cabang,
                'nearest_branch' => [
                    'id_cabang' => $this->branch->id_cabang,
                    'nama_cabang' => $this->branch->nama_cabang,
                    'distance' => null,
                ]
            ])
            ->post(route('order.place'), [
                'metode_pengiriman' => 'kurir',
                'address_id' => $this->completeAddress->id,
                'shipping_cost' => 15000,
            ]);

        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.payment');
        $response->assertViewHas('order');
    }
}
