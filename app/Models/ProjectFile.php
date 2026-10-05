<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Berkas pendukung sebuah proyek (PDF, arsip, dokumen).
 *
 * Terpisah dari ProjectImage karena yang perlu ditampilkan berbeda: nama asli
 * dan ukuran berkas berarti bagi pengunjung yang akan mengunduh, sedangkan
 * untuk gambar keduanya tidak relevan.
 */
class ProjectFile extends Model
{
    protected $fillable = [
        'project_id', 'file_path', 'original_name',
        'mime_type', 'size_bytes', 'label', 'sort_order',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** Ukuran yang enak dibaca manusia, mis. "2,4 MB". */
    public function getUkuranTerbacaAttribute(): string
    {
        $bytes = (int) $this->size_bytes;

        foreach (['B', 'KB', 'MB', 'GB'] as $satuan) {
            if ($bytes < 1024 || $satuan === 'GB') {
                return number_format($bytes, $satuan === 'B' ? 0 : 1, ',', '.') . ' ' . $satuan;
            }
            $bytes /= 1024;
        }

        return '0 B';
    }
}
