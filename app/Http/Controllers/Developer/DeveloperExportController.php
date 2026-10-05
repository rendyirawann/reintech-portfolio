<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Services\PortfolioPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class DeveloperExportController extends Controller
{
    public function __construct(private readonly PortfolioPdfService $pdf)
    {
    }

    /** Halaman pratinjau PDF di dalam aplikasi, sebelum diunduh. */
    public function index(Request $request): View
    {
        $jenis = $this->jenis($request);

        return view('developer.export', [
            'jenis' => $jenis,
            'semuaJenis' => PortfolioPdfService::JENIS,
            'namaBerkas' => $this->pdf->namaBerkas($jenis),
        ]);
    }

    private function jenis(Request $request): string
    {
        $j = (string) $request->query('jenis', 'portfolio');

        return array_key_exists($j, PortfolioPdfService::JENIS) ? $j : 'portfolio';
    }

    /**
     * Berkas PDF-nya.
     *
     * Bawaannya INLINE — ditampilkan peramban, tidak langsung diunduh — supaya
     * bisa dipratinjau di halaman ekspor atau di tab baru lebih dulu. Unduhan
     * hanya terjadi bila diminta dengan ?unduh=1.
     */
    public function pdf(Request $request): Response|RedirectResponse
    {
        try {
            $jenis = $this->jenis($request);
            $isi = $this->pdf->buat($jenis);
        } catch (RuntimeException $e) {
            return back()->withErrors(['export' => $e->getMessage()]);
        }

        $cara = $request->boolean('unduh') ? 'attachment' : 'inline';

        return response($isi, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $cara . '; filename="' . $this->pdf->namaBerkas($jenis) . '"',
            // Isinya data pribadi — jangan sampai tersimpan di cache perantara.
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    /** Pratinjau HTML yang sama persis dengan isi PDF. */
    public function pratinjau(Request $request): Response
    {
        return response($this->pdf->html($this->jenis($request)), 200, [
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }
}
