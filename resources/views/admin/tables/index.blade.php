@extends('admin.layouts.layout')

@section('title', 'Tables & QR')

@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    // Adresse utilisée dans les QR codes.
    // À définir dans .env : QR_BASE_URL=http://10.110.219.32:8000  (voir config/app.php)
    $qrBase = rtrim(config('app.qr_url') ?: config('app.url'), '/');
@endphp

@push('styles')
<style>
    .qr-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
    .qr-card { display: flex; flex-direction: column; }
    .qr-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px 0; }
    .qr-card-head h2 { margin: 0; font-size: 18px; font-weight: 800; }
    .qr-card-head small { color: var(--text-3); font-size: 12px; font-weight: 600; }
    .qr-img { margin: 14px 16px 0; padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); display: grid; place-items: center; background: #fff; }
    .qr-img svg { width: 100%; max-width: 180px; height: auto; display: block; }
    .qr-url { margin: 10px 16px 0; font-size: 12px; color: var(--text-3); font-family: var(--mono, monospace); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .qr-actions { display: flex; gap: 4px; padding: 12px 12px 12px 16px; margin-top: auto; border-top: 1px solid var(--border); margin-top: 14px; }
    .qr-actions .link { margin-right: auto; display: inline-flex; align-items: center; gap: 6px; }
    .qr-actions .link svg.lucide { width: 15px; height: 15px; }

    .add-table { display: flex; gap: 10px; align-items: flex-start; flex-wrap: wrap; }
    .add-table .input { width: 160px; }

    .notice {
        display: flex; gap: 10px; align-items: flex-start;
        padding: 12px 16px; margin-bottom: 16px; border-radius: var(--radius-sm);
        background: #fdf3e6; border: 1px solid #f3dab4; color: #7a4a06; font-size: 13px; font-weight: 600;
    }
    .notice code { font-family: var(--mono, monospace); background: rgba(0,0,0,.05); padding: 1px 5px; border-radius: 4px; }

    /* Impression : uniquement les QR, prêts à découper */
    @media print {
        .sidebar, .topbar, .page-head, .no-print, .qr-actions, .qr-url, .flash { display: none !important; }
        .main { margin: 0 !important; }
        .content { padding: 0 !important; }
        body { background: #fff; }
        .qr-grid { grid-template-columns: repeat(3, 1fr); gap: 0; }
        .qr-card { border: 1px dashed #bbb !important; border-radius: 0 !important; break-inside: avoid; padding: 18px 0; }
        .qr-card-head { justify-content: center; padding: 0; }
        .qr-card-head small { display: none; }
        .qr-img { border: 0; }
        .qr-card::after { content: "Scannez pour voir le menu"; text-align: center; font-size: 12px; color: #555; margin-top: 6px; }
    }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Tables & QR</h1>
        <p>{{ $tables->count() }} tables · chaque QR ouvre le menu de sa table.</p>
    </div>
    <div class="page-actions">
        @if($tables->isNotEmpty())
            <button type="button" class="btn" onclick="window.print()"><i data-lucide="printer"></i> Imprimer tous les QR</button>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif

@if(\Illuminate\Support\Str::contains($qrBase, ['localhost', '127.0.0.1']))
    <div class="notice no-print">
        <i data-lucide="alert-triangle"></i>
        <span>Les QR pointent vers <code>{{ $qrBase }}</code> : les téléphones des clients ne pourront pas l'ouvrir.
        Définissez <code>QR_BASE_URL</code> dans le fichier <code>.env</code> avec l'adresse du serveur sur le réseau.</span>
    </div>
@endif

<div class="card no-print" style="margin-bottom: 16px">
    <div class="form-section">
        <form method="POST" action="/admin/tables" class="add-table">
            @csrf
            <div>
                <input type="number" name="numero" min="1" class="input num @error('numero') is-invalid @enderror"
                       value="{{ old('numero', ($tables->max('numero') ?? 0) + 1) }}" placeholder="N° de table" required>
                @error('numero')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary"><i data-lucide="plus"></i> Ajouter la table</button>
        </form>
    </div>
</div>

@if($tables->isEmpty())
    <div class="card empty">Aucune table. Ajoutez la première pour générer son QR code.</div>
@else
    <div class="qr-grid">
        @foreach($tables->sortBy('numero') as $table)
            @php $link = $qrBase.'/menu/'.$table->id; @endphp

            <article class="card qr-card" data-table="{{ $table->numero }}" data-link="{{ $link }}">
                <div class="qr-card-head">
                    <h2>Table {{ $table->numero }}</h2>
                    <small>ID {{ $table->id }}</small>
                </div>

                <div class="qr-img">
                    {!! QrCode::format('svg')->size(220)->margin(1)->errorCorrection('M')->generate($link) !!}
                </div>

                <div class="qr-url" title="{{ $link }}">{{ $link }}</div>

                <div class="qr-actions">
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="link"><i data-lucide="external-link"></i> Ouvrir</a>
                    <button type="button" class="icon-action" title="Copier le lien" data-copy><i data-lucide="copy"></i></button>
                    <button type="button" class="icon-action" title="Télécharger le QR (SVG)" data-download><i data-lucide="download"></i></button>
                    <button type="button" class="icon-action" title="Imprimer ce QR" data-print><i data-lucide="printer"></i></button>
                </div>
            </article>
        @endforeach
    </div>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const qrSvg = (card) => {
        const svg = card.querySelector('.qr-img svg').cloneNode(true);
        svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        return svg.outerHTML;
    };

    document.querySelectorAll('.qr-card').forEach(card => {
        const table = card.dataset.table;
        const link  = card.dataset.link;

        card.querySelector('[data-copy]').addEventListener('click', async (e) => {
            const btn = e.currentTarget;
            try {
                await navigator.clipboard.writeText(link);
                btn.title = 'Lien copié';
                btn.style.color = 'var(--st-ready)';
                setTimeout(() => { btn.title = 'Copier le lien'; btn.style.color = ''; }, 1500);
            } catch (err) { prompt('Copiez le lien :', link); }
        });

        card.querySelector('[data-download]').addEventListener('click', () => {
            const blob = new Blob([qrSvg(card)], { type: 'image/svg+xml' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'qr-table-' + table + '.svg';
            a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 1000);
        });

        card.querySelector('[data-print]').addEventListener('click', () => {
            const w = window.open('', '_blank', 'width=480,height=640');
            if (!w) return;
            w.document.write(`<!DOCTYPE html><html><head><title>Table ${table}</title>
                <style>
                    body{margin:0;font-family:system-ui,sans-serif;display:grid;place-items:center;min-height:100vh;text-align:center}
                    h1{margin:0 0 8px;font-size:28px} p{margin:8px 0 0;color:#555}
                    svg{width:260px;height:260px}
                </style></head>
                <body><div><h1>Table ${table}</h1>${qrSvg(card)}<p>Scannez pour voir le menu</p></div></body></html>`);
            w.document.close();
            w.focus();
            setTimeout(() => { w.print(); w.close(); }, 250);
        });
    });
})();
</script>
@endpush