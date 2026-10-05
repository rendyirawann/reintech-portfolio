{{--
    Kotak "Panduan" di atas halaman admin: apa yang diatur halaman ini, di mana
    ia tampil, dan langkahnya. Teksnya ada di App\Support\AdminContent::panduan(),
    satu tempat untuk semua halaman.
--}}
@php
    [$teks, $langkah] = \App\Support\AdminContent::panduan()[$kunci] ?? [null, null];
@endphp
@if ($teks || $langkah)
    <div class="pf-guide" role="note">
        <div class="pf-guide__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
        </div>
        <div style="min-width:0;">
            <div class="pf-guide__title">Panduan</div>
            @if ($teks)<p class="pf-guide__text">{{ $teks }}</p>@endif
            @if ($langkah)
                <ol class="pf-guide__steps">
                    @foreach ($langkah as $l)<li>{{ $l }}</li>@endforeach
                </ol>
            @endif
        </div>
    </div>
@endif
