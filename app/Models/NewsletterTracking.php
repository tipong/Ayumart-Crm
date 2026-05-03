<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterTracking extends Model
{
    protected $table = 'tb_tracking_newsletter';
    protected $primaryKey = 'id_tracking_newsletter';

    protected $fillable = [
        'id_newsletter',
        'id_pelanggan',
        'email_tujuan',
        'konten_email',
        'subjek_email',
        'tanggal_kirim',
        'status_kirim',
        'mailchimp_member_id',
        'waktu_dibuka',
        'waktu_klik',
        'jumlah_dibuka',
        'jumlah_klik',
        'phone',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
        'waktu_dibuka' => 'datetime',
        'waktu_klik' => 'datetime',
        'jumlah_dibuka' => 'integer',
        'jumlah_klik' => 'integer',
    ];

    /**
     * Get the newsletter
     */
    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class, 'id_newsletter', 'id_newsletter');
    }

    /**
     * Get the customer
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Check if email was sent successfully
     */
    public function isSent()
    {
        return $this->status_kirim === 'terkirim';
    }

    /**
     * Check if email failed
     */
    public function isFailed()
    {
        return $this->status_kirim === 'gagal';
    }

    /**
     * Check if email is pending
     */
    public function isPending()
    {
        return $this->status_kirim === 'pending';
    }
}
