<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'tb_newsletter';
    protected $primaryKey = 'id_newsletter';

    protected $fillable = [
        'judul',
        'subjek_email',
        'jenis_newsletter',
        'konten_email',
        'konten_html',
        'status',
        'metode_pengiriman',
        'tanggal_kirim',
        'total_penerima',
        'total_terkirim',
        'total_gagal',
        'dibuat_oleh',
        'mailchimp_campaign_id',
        'target_tiers',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
        'total_penerima' => 'integer',
        'total_terkirim' => 'integer',
        'total_gagal' => 'integer',
        'target_tiers' => 'array',
    ];

    /**
     * Get the user who created this newsletter
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');  // FIXED: Changed 'id' to 'id_user'
    }

    /**
     * Get tracking records for this newsletter
     */
    public function trackings()
    {
        return $this->hasMany(NewsletterTracking::class, 'id_newsletter', 'id_newsletter');
    }

    /**
     * Check if newsletter is sent
     */
    public function isSent()
    {
        return $this->status === 'terkirim';
    }

    /**
     * Check if newsletter is draft
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if newsletter is sending
     */
    public function isSending()
    {
        return $this->status === 'mengirim';
    }
}
