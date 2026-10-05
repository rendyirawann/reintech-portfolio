@extends('layouts.developer')

@section('title', 'Tech Stack')

@push('page-styles')
<style>
    .ts-baris { display: grid; grid-template-columns: 36px 1.4fr 1fr 90px 1fr 70px auto; gap: .6rem; align-items: center; padding: .6rem 0; border-bottom: 1px solid var(--dev-border); }
    .ts-baris .dev-input { padding: .45rem .6rem; }
    .ts-ikon { width: 28px; height: 28px; display: grid; place-items: center; border-radius: 8px; background: color-mix(in srgb, var(--c) 18%, transparent); }
    .ts-ikon i { width: 18px; height: 18px; background: var(--c); -webkit-mask: var(--m) center / contain no-repeat; mask: var(--m) center / contain no-repeat; }
    .ts-ikon b { font-size: .7rem; color: var(--c); }
    .ts-kepala { font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--dev-text-muted); border-bottom: 0; }
    .ts-aksi { display: flex; gap: .35rem; }
    .ts-aksi .dev-btn { padding: .35rem .6rem; font-size: .72rem; }
    .redup { opacity: .45; }
    input[type=color].dev-input { padding: .15rem; height: 36px; }
    @media (max-width: 900px) { .ts-baris { grid-template-columns: 36px 1fr 1fr; } .ts-kepala { display: none; } }
</style>
@endpush

@section('content')
@include('components.panduan', ['kunci' => 'tech-stacks'])

<div class="pf-layout">
<div class="dev-card" style="min-width:0;">
    <div class="ts-baris ts-kepala"><span></span><span>Nama</span><span>Kode ikon</span><span>Warna</span><span>Kelompok</span><span>Urut</span><span></span></div>

    @foreach ($stacks as $t)
        <form action="{{ route('admin.tech-stacks.update', $t->id) }}" method="POST" class="ts-baris {{ $t->is_visible ? '' : 'redup' }}">
            @csrf @method('PUT')
            <span class="ts-ikon" style="--c: {{ $t->color ?: '#22d3ee' }}; @if ($t->ikon_url) --m: url('{{ $t->ikon_url }}') @endif">
                @if ($t->ikon_url)<i></i>@else<b>{{ mb_substr($t->name, 0, 2) }}</b>@endif
            </span>
            <input name="name" class="dev-input" value="{{ $t->name }}" maxlength="60" required aria-label="Nama">
            <input name="slug" class="dev-input" value="{{ $t->slug }}" maxlength="60" aria-label="Kode ikon">
            <input type="color" name="color" class="dev-input" value="{{ $t->color ?: '#22d3ee' }}" aria-label="Warna">
            <select name="group" class="dev-input" aria-label="Kelompok">
                @foreach (\App\Models\TechStack::KELOMPOK as $g)<option @selected($t->group === $g)>{{ $g }}</option>@endforeach
            </select>
            <input type="number" name="sort_order" class="dev-input" value="{{ $t->sort_order }}" aria-label="Urutan">
            <span class="ts-aksi">
                <button class="dev-btn" type="submit">Simpan</button>
                <button class="dev-btn" type="submit" formaction="{{ route('admin.tech-stacks.toggle', $t->id) }}" formmethod="POST" name="_method" value="POST">{{ $t->is_visible ? 'Sembunyikan' : 'Tampilkan' }}</button>
                <button class="dev-btn dev-btn-danger" type="submit" formaction="{{ route('admin.tech-stacks.destroy', $t->id) }}" name="_method" value="DELETE" onclick="return confirm('Hapus {{ $t->name }}?');">×</button>
            </span>
        </form>
    @endforeach

    <h3 style="margin:1.5rem 0 .5rem;">Tambah teknologi</h3>
    <form action="{{ route('admin.tech-stacks.store') }}" method="POST" class="ts-baris" style="border-bottom:0;">
        @csrf
        <span></span>
        <input name="name" class="dev-input" placeholder="mis. Vue.js" maxlength="60" required>
        <input name="slug" class="dev-input" placeholder="vuedotjs (opsional)" maxlength="60">
        <input type="color" name="color" class="dev-input" value="#22d3ee">
        <select name="group" class="dev-input">@foreach (\App\Models\TechStack::KELOMPOK as $g)<option>{{ $g }}</option>@endforeach</select>
        <input type="number" name="sort_order" class="dev-input" value="{{ ($stacks->max('sort_order') ?? 0) + 1 }}">
        <span class="ts-aksi"><button class="dev-btn" type="submit">Tambah</button></span>
    </form>
</div>

@include('components.preview', ['target' => '#stack'])
</div>
@endsection
