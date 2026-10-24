@extends('admin.layouts.layout')

@section('title', 'Sauvegardes')

@php
    $taille = function ($o) {
        if ($o < 1024) return $o . ' o';
        if ($o < 1048576) return number_format($o / 1024, 0, ',', ' ') . ' Ko';
        return number_format($o / 1048576, 1, ',', ' ') . ' Mo';
    };
    $derniere = $sauvegardes->first();
    $ancienne = !$derniere || $derniere['date']->lt(now()->subDay());
@endphp

@push('styles')
<style>
    .flash.error { background: #fdf1f1; color: #9b2020; border-color: #f3c9c9; }
    .backup-state { display: flex; align-items: center; gap: 14px; padding: 18px 20px; }
    .backup-state .kpi-icon { width: 44px; height: 44px; }
    .backup-state.warn .kpi-icon { background: #fdecec; color: var(--danger); }
    .backup-state strong { display: block; font-size: 15px; }
    .backup-state span { color: var(--text-2); font-size: 13px; }
    .tag-auto { font-size: 11.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: var(--bg); color: var(--text-2); border: 1px solid var(--border); }
    .help { padding: 16px 20px; font-size: 13px; color: var(--text-2); line-height: 1.6; }
    .help code { font-family: var(--mono, monospace); font-size: 12px; background: var(--bg); padding: 1px 5px; border-radius: 4px; }
    .help ol { margin: 6px 0 0; padding-left: 18px; }
</style>
@endpush

@section('content')

<div class="page-head">
    <div>
        <h1>Sauvegardes</h1>
        <p>Copie complète de la base : menu, commandes, paiements, stock, personnel.</p>
    </div>
    <div class="page-actions">
        <form method="POST" action="{{ route('admin.backups.store') }}" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').lastChild.textContent=' Sauvegarde en cours…';">
            @csrf
            <button type="submit" class="btn btn-primary"><i data-lucide="database-backup"></i> Sauvegarder maintenant</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="flash"><i data-lucide="check-circle-2"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash error"><i data-lucide="alert-circle"></i> {{ session('error') }}</div>
@endif

<div class="card {{ $ancienne ? 'backup-state warn' : 'backup-state' }}" style="margin-bottom:16px">
    <span class="kpi-icon"><i data-lucide="{{ $ancienne ? 'alert-triangle' : 'shield-check' }}"></i></span>
    <div>
        @if($derniere)
            <strong>Dernière sauvegarde : {{ $derniere['date']->locale('fr')->diffForHumans() }}</strong>
            <span>{{ $derniere['date']->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm') }} · {{ $taille($derniere['taille']) }}</span>
        @else
            <strong>Aucune sauvegarde pour le moment</strong>
            <span>Cliquez sur « Sauvegarder maintenant ».</span>
        @endif
    </div>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-head" style="padding-bottom:12px">
        <div>
            <h2>Fichiers</h2>
            <span class="sub">Les sauvegardes automatiques (clôture de la journée, 1 fois par jour) gardent les 30 dernières.</span>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th class="right">Taille</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sauvegardes as $s)
                    <tr>
                        <td>
                            <strong>{{ $s['date']->format('d/m/Y à H:i') }}</strong>
                            <div class="muted" style="font-size:12px">{{ $s['nom'] }}</div>
                        </td>
                        <td>@if($s['auto'])<span class="tag-auto">Automatique</span>@else<span class="tag-auto" style="color:var(--brand-600);background:var(--brand-50);border-color:transparent">Manuelle</span>@endif</td>
                        <td class="right num muted">{{ $taille($s['taille']) }}</td>
                        <td class="right">
                            <div class="row-actions">
                                <a href="{{ route('admin.backups.download', $s['nom']) }}" class="icon-action" title="Télécharger"><i data-lucide="download"></i></a>
                                <form method="POST" action="{{ route('admin.backups.destroy', $s['nom']) }}" onsubmit="return confirm('Supprimer cette sauvegarde ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-action danger" title="Supprimer"><i data-lucide="trash-2"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Aucune sauvegarde.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card help">
    <strong style="color:var(--text)">Conseils</strong>
    <ol>
        <li>Téléchargez régulièrement une sauvegarde sur une <strong>clé USB</strong> ou un autre ordinateur : si le disque du serveur tombe en panne, les fichiers ci-dessus disparaissent avec lui.</li>
        <li>Pour restaurer : phpMyAdmin → base <code>{{ config('database.connections.' . config('database.default') . '.database') }}</code> → onglet <strong>Importer</strong> → choisir le fichier <code>.sql.gz</code>.</li>
        <li>Les fichiers sont rangés dans <code>{{ $dossier }}</code>.</li>
    </ol>
</div>

@endsection
