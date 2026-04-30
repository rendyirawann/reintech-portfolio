<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioIdentity extends Model
{
    protected $table = 'portfolio_identity';

    protected $fillable = [
        'logo_text',
        'logo_subtext',
        'topbar_status_text',
        'topbar_role_text',
        'sidebar_icon_type',
        'sidebar_icon_value',
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
            'topbar_role_text'   => 'Full Stack Dev',
            'sidebar_icon_type'  => 'text',
            'sidebar_icon_value' => 'RD',
        ]);
    }
}
