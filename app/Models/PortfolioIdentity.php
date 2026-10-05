<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioIdentity extends Model
{
    protected $table = 'portfolio_identity';

    protected $fillable = [
        'logo_text',
        'summary',
        'languages',
        'logo_subtext',
        'topbar_status_text',
        'topbar_role_text',
        'sidebar_icon_type',
        'sidebar_icon_value',
        'contact_email',
        'contact_whatsapp',
        'contact_linkedin',
        'footer_text',
        // SEO — disunting dari /admin/identity
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        // Data diri — dipakai bagian Tentang dan dokumen ekspor
        'profile_image',
        'full_name',
        'headline',
        'location',
    ];

    /**
     * Always return or create the single identity row.
     */
    public static function instance(): static
    {
        return static::firstOrCreate(['id' => 1], [
            'logo_text'          => 'RD',
            'logo_subtext'       => 'reintech.dev',
            'topbar_status_text' => 'Available for projects',
            'topbar_role_text'   => 'Full Stack Developer / Senior Programmer',
            'sidebar_icon_type'  => 'text',
            'sidebar_icon_value' => 'RD',
            'contact_email'      => 'hello@reintech.dev',
            'contact_whatsapp'   => '6281234567890',
            'footer_text'        => 'Built with ❤️ & Laravel',
        ]);
    }

    /**
     * Nomor WhatsApp dalam format internasional tanpa tanda: "0823…" / "+62 823…"
     * menjadi "62823…". Dipakai untuk tautan wa.me (yang menolak awalan 0) dan dokumen.
     */
    public function getWaAttribute(): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $this->contact_whatsapp);
        if ($d === '') {
            return null;
        }

        return str_starts_with($d, '0') ? '62' . substr($d, 1) : $d;
    }
}
