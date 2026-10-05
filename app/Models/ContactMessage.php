<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Pesan dari formulir kontak halaman depan.
 *
 * Disimpan walau surelnya gagal terkirim — lihat migrasi
 * create_contact_messages_and_seo_fields untuk alasannya.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'message',
        'ip_address', 'is_read', 'mail_sent_at', 'mail_error',
    ];

    protected $casts = [
        'is_read'      => 'boolean',
        'mail_sent_at' => 'datetime',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}
