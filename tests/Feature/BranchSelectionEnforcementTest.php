<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Cabang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class BranchSelectionEnforcementTest extends TestCase
{
    use RefreshDatabase {
        refreshTestDatabase as baseRefreshTestDatabase;
    }

    protected function refreshTestDatabase()
    {
        // Ensure tb_produk exists on mysql_integrasi connection before migrations run
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

        $this->baseRefreshTestDatabase();
    }

    protected $user;
    protected $pelanggan;
    protected $branch;

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
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create pelanggan
        $this->pelanggan = Pelanggan::create([
            'id_user' => $this->user->id_user,
            'nama_pelanggan' => 'Pelanggan Test',
            'no_tlp_pelanggan' => '081234567890',
            'alamat_lengkap' => 'Alamat Lengkap Test',
        ]);
    }

    /**
     * Test checkout redirects to cart if no branch is chosen
     */
    public function test_checkout_redirects_to_cart_without_branch()
    {
        $response = $this->actingAs($this->user)
            ->withSession([]) // Empty session, no branch
            ->get(route('checkout'));

        $response->assertRedirect(route('pelanggan.cart'));
        $response->assertSessionHas('error', 'Silakan pilih cabang terlebih dahulu sebelum melanjutkan ke checkout.');
    }

    /**
     * Test checkout allows access if branch is chosen
     */
    public function test_checkout_allowed_with_branch()
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
            ->get(route('checkout'));

        // It should NOT redirect to cart due to branch validation, but will redirect due to empty cart
        $response->assertRedirect(route('pelanggan.cart'));
        $response->assertSessionHas('error', 'Keranjang belanja Anda kosong');
    }

    /**
     * Test placeOrder redirects to cart if no branch is chosen
     */
    public function test_place_order_redirects_to_cart_without_branch()
    {
        $response = $this->actingAs($this->user)
            ->withSession([])
            ->post(route('order.place'), [
                'metode_pengiriman' => 'pickup',
            ]);

        $response->assertRedirect(route('pelanggan.cart'));
        $response->assertSessionHas('error', 'Silakan pilih cabang terlebih dahulu sebelum melanjutkan ke checkout.');
    }

    /**
     * Test change-branch API sets session
     */
    public function test_change_branch_api_sets_session()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('api.change-branch'), [
                'id_cabang' => $this->branch->id_cabang,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertEquals($this->branch->id_cabang, session('nearest_branch_id'));
    }

    /**
     * Test set-user-location API sets session
     */
    public function test_set_user_location_api_sets_session()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('api.set-user-location'), [
                'latitude' => -8.650000,
                'longitude' => 115.216667,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertEquals($this->branch->id_cabang, session('nearest_branch_id'));
    }

    protected function tearDown(): void
    {
        Schema::connection('mysql_integrasi')->dropIfExists('tb_produk');
        parent::tearDown();
    }
}
