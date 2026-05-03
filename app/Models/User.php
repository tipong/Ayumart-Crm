<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_role',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that are cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the name for remember token.
     *
     * @return string|null
     */
    public function getRememberTokenName()
    {
        return null; // Disable remember token
    }

    /**
     * Get name attribute (accessor)
     * This makes $user->name work by fetching from staff or pelanggan table
     */
    public function getNameAttribute()
    {
        return $this->getName();
    }

    /**
     * Get phone attribute (accessor)
     * This makes $user->phone work by fetching from staff or pelanggan table
     */
    public function getPhoneAttribute()
    {
        return $this->getPhone();
    }

    /**
     * Get id attribute (accessor for backward compatibility)
     * This makes $user->id work by returning id_user value
     */
    public function getIdAttribute()
    {
        return $this->id_user;
    }

    /**
     * Get is_active attribute (accessor)
     */
    public function getIsActiveAttribute()
    {
        return $this->isActive();
    }

    /**
     * Get the role that owns the user.
     */
    public function getRoleName()
    {
        $roles = [
            1 => 'owner',
            2 => 'admin',
            3 => 'cs',
            4 => 'kurir',
            5 => 'pelanggan',
        ];

        return $roles[$this->id_role] ?? 'unknown';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->getRoleName() === $roleName;
    }

    /**
     * Check if user is staff (owner, admin, cs, kurir)
     */
    public function isStaff()
    {
        return $this->id_role >= 1 && $this->id_role <= 4;
    }

    /**
     * Check if user is customer
     */
    public function isCustomer()
    {
        return $this->id_role == 5;
    }

    /**
     * Get membership for the user (pelanggan)
     */
    public function membership()
    {
        return $this->hasOne(Membership::class, 'user_id', 'id_user');
    }

    /**
     * Get pelanggan (customer) data for the user
     * Relasi ke tabel tb_pelanggan
     */
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id_user', 'id_user');
    }

    /**
     * Get staff data for the user (owner, admin, cs, kurir)
     * Relasi ke tabel tb_staff
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'id_user', 'id_user');
    }

    /**
     * Check if user has staff data
     */
    public function hasStaffData()
    {
        return $this->staff()->exists();
    }

    /**
     * Check if user has pelanggan data
     */
    public function hasPelangganData()
    {
        return $this->pelanggan()->exists();
    }

    /**
     * Get or create pelanggan data for the user
     * Helper method untuk backward compatibility
     * UPDATED: Use id_user after restructure
     */
    public function getOrCreatePelanggan()
    {
        // If pelanggan exists, return it
        if ($this->pelanggan) {
            return $this->pelanggan;
        }

        // If user is pelanggan (id_role = 5), create pelanggan record
        if ($this->id_role == 5) {
            $pelanggan = Pelanggan::create([
                'id_user' => $this->id_user,
                'nama_pelanggan' => $this->nama_lengkap,
                'no_tlp_pelanggan' => $this->no_telp ?? '',
                'alamat' => '',
                'status_pelanggan' => 'aktif',
            ]);

            // Refresh relation
            $this->load('pelanggan');

            return $pelanggan;
        }

        return null;
    }

    /**
     * Get staff position (if user is staff)
     */
    public function getStaffPosition()
    {
        if ($this->staff) {
            return $this->staff->posisi_staff;
        }

        return $this->getRoleName();
    }

    /**
     * Get user name from related table (staff or pelanggan)
     */
    public function getName()
    {
        if ($this->staff) {
            return $this->staff->nama_staff;
        }

        if ($this->pelanggan) {
            return $this->pelanggan->nama_pelanggan;
        }

        return $this->email;
    }

    /**
     * Get user phone from related table (staff or pelanggan)
     */
    public function getPhone()
    {
        if ($this->staff) {
            return $this->staff->no_tlp_staff;
        }

        if ($this->pelanggan) {
            return $this->pelanggan->no_tlp_pelanggan;
        }

        return '';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        if ($this->staff) {
            return $this->staff->status_akun === 'aktif';
        }

        if ($this->pelanggan) {
            return $this->pelanggan->status_pelanggan === 'aktif';
        }

        return true;
    }
}
