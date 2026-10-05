@extends('layouts.developer')

@section('title', 'Tampilan Admin')

@push('page-styles')
<style>
    .tab-bilah { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: 1.25rem; padding: .3rem; border: 1px solid var(--dev-border); border-radius: 12px; background: var(--dev-surface); width: fit-content; max-width: 100%; }
    .tab-bilah a { padding: .5rem .95rem; border-radius: 9px; font-size: .86rem; font-weight: 600; text-decoration: none; color: var(--dev-text-muted); cursor: pointer; transition: background-color 180ms ease, color 180ms ease; }
    .tab-bilah a:hover { color: var(--dev-text); }
    .tab-bilah a[aria-current="page"] { color: var(--dev-text); background: color-mix(in srgb, var(--dev-accent) 16%, transparent); box-shadow: inset 0 -2px 0 var(--dev-accent-2); }
    .isian { margin-bottom: 1rem; }
    .isian label { display: block; margin-bottom: .4rem; font-size: .84rem; font-weight: 600; color: var(--dev-text); }
    .isian-grup { margin: 1.4rem 0 .8rem; font-family: var(--dev-mono, monospace); font-size: .7rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--dev-text-muted); }
    .saklar { display: flex; align-items: center; gap: .6rem; cursor: pointer; font-size: .88rem; }
    .saklar input { width: 18px; height: 18px; accent-color: var(--dev-accent-2); cursor: pointer; }
</style>
@endpush

@php
    // Pratinjau tiap tab: halaman login sungguhan, atau panel admin ini sendiri.
    $pratinjau = [
        'login' => ['url' => route('admin.login-preview'), 'buka' => route('admin.login-preview')],
        'admin' => ['url' => route('admin.dashboard'), 'buka' => route('admin.dashboard')],
    ][$tab];

    // Isian login dikelompokkan supaya formulir sepanjang dua puluh isian
    // tetap mudah dipindai.
    $kelompokLogin = [
        'Panel kiri'   => ['login_brand_name', 'login_tagline', 'login_kicker', 'login_headline_1', 'login_headline_2', 'login_lead', 'login_point_1', 'login_point_2', 'login_point_3'],
        'Formulir'     => ['login_card_title', 'login_card_subtitle', 'login_button', 'login_button_busy', 'login_foot'],
        'Loader'       => ['login_loader_label', 'login_loader_text'],
        'Proses masuk' => ['login_progress_1', 'login_progress_2', 'login_progress_3', 'login_progress_4'],
    ];
    $urutan = $tab === 'login' ? $kelompokLogin : ['' => array_keys($skema[$tab]['isian'])];
@endphp

@section('content')
<nav class="tab-bilah" aria-label="Bagian tampilan admin">
    @foreach ($skema as $kunciTab => $k)
        <a href="{{ route('admin.content', ['tab' => $kunciTab]) }}" @if ($tab === $kunciTab) aria-current="page" @endif>{{ $k['judul'] }}</a>
    @endforeach
</nav>

@include('components.panduan', ['kunci' => $tab])

<div class="pf-layout">
    <div class="dev-card" style="min-width:0;">
        {{-- Awalan kosong: kunci pratinjau = nama isian apa adanya, sama
             dengan atribut data-pf di halaman login dan kerangka admin. --}}
        <form action="{{ route('admin.content.update') }}" method="POST" data-preview-prefix="">
            @csrf
            <input type="hidden" name="_kelompok" value="{{ $tab }}">

            @foreach ($urutan as $judulGrup => $kunciIsian)
                @if ($judulGrup)<div class="isian-grup">{{ $judulGrup }}</div>@endif

                @foreach ($kunciIsian as $kunci)
                    @php [$label, $tipe] = $skema[$tab]['isian'][$kunci]; @endphp
                    <div class="isian">
                        @if ($tipe === 'toggle')
                            <label class="saklar">
                                <input type="checkbox" name="{{ $kunci }}" value="1" @checked(old($kunci, $nilai[$kunci] ?? '1') === '1')>
                                {{ $label }}
                            </label>
                        @else
                            <label for="f-{{ $kunci }}">{{ $label }}</label>
                            @if ($tipe === 'textarea')
                                <textarea id="f-{{ $kunci }}" name="{{ $kunci }}" class="dev-input" rows="3" maxlength="600">{{ old($kunci, $nilai[$kunci] ?? '') }}</textarea>
                            @else
                                <input id="f-{{ $kunci }}" type="{{ $tipe === 'url' ? 'url' : 'text' }}" name="{{ $kunci }}" class="dev-input"
                                       value="{{ old($kunci, $nilai[$kunci] ?? '') }}" maxlength="{{ $tipe === 'url' ? 500 : 160 }}">
                            @endif
                        @endif
                        @if (! empty($petunjuk[$kunci]))
                            <span class="pf-hint">{{ $petunjuk[$kunci] }}</span>
                        @endif
                    </div>
                @endforeach
            @endforeach

            <button type="submit" class="dev-btn" style="margin-top:.5rem;">Simpan {{ $skema[$tab]['judul'] }}</button>
        </form>
    </div>

    @include('components.preview', ['url' => $pratinjau['url'], 'bukaUrl' => $pratinjau['buka'], 'target' => ''])
</div>
@endsection
