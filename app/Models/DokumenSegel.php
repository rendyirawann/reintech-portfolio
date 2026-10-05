<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenSegel extends Model
{
    protected $table = 'dokumen_segel';

    protected $fillable = ['kode', 'jenis', 'sha256', 'ukuran'];
}
