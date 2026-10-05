{{-- Navigasi halaman proyek. Tautan tetap berupa ?proyek=N sehingga berfungsi
     tanpa JavaScript; front.js mencegatnya dan memuat lewat AJAX. --}}
@if ($p->lastPage() > 1)
    @php $url = fn (int $n) => request()->fullUrlWithQuery(['proyek' => $n]) . '#projects'; @endphp
    <a class="pager__btn" href="{{ $url(max(1, $p->currentPage() - 1)) }}" data-page="{{ $p->currentPage() - 1 }}" @if ($p->onFirstPage()) aria-disabled="true" tabindex="-1" @endif aria-label="Sebelumnya">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
    </a>
    @for ($n = 1; $n <= $p->lastPage(); $n++)
        <a class="pager__btn" href="{{ $url($n) }}" data-page="{{ $n }}" @if ($n === $p->currentPage()) aria-current="page" @endif>{{ $n }}</a>
    @endfor
    <a class="pager__btn" href="{{ $url(min($p->lastPage(), $p->currentPage() + 1)) }}" data-page="{{ $p->currentPage() + 1 }}" @if (! $p->hasMorePages()) aria-disabled="true" tabindex="-1" @endif aria-label="Berikutnya">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
    </a>
@endif
