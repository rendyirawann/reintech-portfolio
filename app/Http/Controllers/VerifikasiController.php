<?php

namespace App\Http\Controllers;

use App\Models\DokumenSegel;
use App\Models\PortfolioIdentity;
use App\Services\PortfolioPdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pemeriksa keaslian PDF yang diterbitkan situs ini.
 *
 * Berkas unggahan hanya di-hash lalu dibuang — tidak pernah disimpan.
 */
class VerifikasiController extends Controller
{
    public function tampil(?string $kode = null): View
    {
        $dokumen = $kode ? DokumenSegel::where('kode', strtoupper($kode))->first() : null;

        return $this->halaman(['kode' => $kode ? strtoupper($kode) : null, 'dokumen' => $dokumen]);
    }

    public function periksa(Request $request): View
    {
        $data = $request->validate([
            'kode' => 'nullable|string|max:12|alpha_num',
            'berkas' => 'required|file|mimetypes:application/pdf|max:25600',
        ], [
            'berkas.required' => 'Pilih berkas PDF yang ingin diperiksa.',
            'berkas.mimetypes' => 'Berkas harus berupa PDF.',
            'berkas.max' => 'Ukuran berkas maksimal 25 MB.',
        ]);

        $hash = hash_file('sha256', $request->file('berkas')->getRealPath());
        $cocok = DokumenSegel::where('sha256', $hash)->first();
        $kode = strtoupper((string) ($data['kode'] ?? ''));

        return $this->halaman([
            'kode' => $kode ?: $cocok?->kode,
            'dokumen' => $cocok ?? ($kode ? DokumenSegel::where('kode', $kode)->first() : null),
            'hasil' => $cocok ? 'asli' : 'tidak-cocok',
            'hash' => $hash,
        ]);
    }

    private function halaman(array $data): View
    {
        return view('verifikasi', $data + [
            'identity' => PortfolioIdentity::instance(),
            'jenisLabel' => PortfolioPdfService::JENIS,
            'hasil' => null,
        ]);
    }
}
