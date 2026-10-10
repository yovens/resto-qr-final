@extends('admin.layouts.layout')

@section('title', 'Ventes')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $moisNoms = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    $moisCourts = [1 => 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

    $mois  = (int) request('month');
    $annee = (int) request('year');

    $periode = $mois && $annee ? $moisNoms[$mois].' '.$annee
             : ($mois ? $moisNoms[$mois].' (toutes années)'
             : ($annee ? 'Année '.$annee : 'Depuis le début'));

    $isPaginated = $commandes instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $nbCommandes = $isPaginated ? $commandes->total() : $commandes->count();
    $ticket      = $nbCommandes > 0 ? $total / $nbCommandes : 0;

    // Ventes par mois sur 12 colonnes
    $parMoisMap = collect($parMois)->mapWithKeys(fn ($m) => [(int) $m->mois => (float) $m->total]);
    $maxMois    = max($parMoisMap->max() ?? 0, 1);
    $meilleur   = $parMoisMap->isNotEmpty() ? $parMoisMap->sortDesc()->keys()->first() : null;

    $statusMap = [
        'nouvelle'       => ['new',    'Nouvelle'],
        'acceptee'       => ['prep',   'Acceptée'],
        'en_preparation' => ['prep',   'En préparation'],
        'prete'          => ['ready',  'Prête'],
        'servie'         => ['served', 'Servie'],
        'payee'          => ['ready',  'Payée'],
    ];
@endphp

@section('content')

<div class="page-head">
    <div>
        <h1>Ventes</h1>
        <p>{{ $periode }}</p>
    </div>

    <form method="GET" class="filters">
        <select name="month" class="select" onchange="this.form.submit()">
            <option value="">Tous les mois</option>
            @foreach($moisNoms as $i => $nom)
                <option value="{{ $i }}" @selected($mois === $i)>{{ $nom }}</option>
            @endforeach
        </select>
        <select name="year" class="select" onchange="this.form.submit()">
            <option value="">Toutes les années</option>
            @for($y = (int) date('Y'); $y >= 2023; $y--)
                <option value="{{ $y }}" @selected($annee === $y)>{{ $y }}</option>
            @endfor
        </select>
        @if($mois || $annee)
            <a href="{{ url()->current() }}" class="btn" title="Effacer les filtres"><i data-lucide="x"></i></a>
        @endif
        <noscript><button type="submit" class="btn">Filtrer</button></noscript>
    </form>
</div>

{{-- Indicateurs --}}
<section class="kpis kpis-3">
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Chiffre d'affaires</span>
            <span class="kpi-icon"><i data-lucide="banknote"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($total) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>{{ $periode }}</span></div>
    </article>
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Commandes</span>
            <span class="kpi-icon"><i data-lucide="receipt-text"></i></span>
        </div>
        <div class="kpi-value num">{{ $nbCommandes }}</div>
        <div class="kpi-foot"><span>sur la période</span></div>
    </article>
    <article class="card kpi">
        <div class="kpi-top">
            <span class="kpi-label">Ticket moyen</span>
            <span class="kpi-icon"><i data-lucide="calculator"></i></span>
        </div>
        <div class="kpi-value num">{{ $fmt($ticket) }}<span class="unit">HTG</span></div>
        <div class="kpi-foot"><span>par commande</span></div>
    </article>
</section>

{{-- Ventes par mois --}}
<article class="card" style="margin-bottom: 16px">
    <div class="card-head">
        <div>
            <h2>Ventes par mois</h2>
            <span class="sub">
                {{ $annee ? $annee : 'Toutes années confondues' }}
                @if($meilleur) · meilleur mois : {{ $moisNoms[$meilleur] }} ({{ $fmt($parMoisMap[$meilleur]) }} HTG) @endif
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="month-bars">
            @for($i = 1; $i <= 12; $i++)
                @php $v = $parMoisMap[$i] ?? 0; @endphp
                <a href="?month={{ $i }}{{ $annee ? '&year='.$annee : '' }}"
                   class="month-bar {{ $v > 0 ? 'has' : '' }} {{ $mois === $i ? 'current' : '' }}"
                   title="{{ $moisNoms[$i] }} : {{ $fmt($v) }} HTG">
                    <span class="v num">{{ $v >= 1000 ? round($v / 1000, 1).'k' : $fmt($v) }}</span>
                    <span class="bar" style="height: {{ $v > 0 ? max(3, $v / $maxMois * 100) : 2 }}%"></span>
                    <span class="m">{{ $moisCourts[$i] }}</span>
                </a>
            @endfor
        </div>
    </div>
</article>

{{-- Détail des commandes --}}
<div class="card">
    <div class="card-head" style="padding-bottom: 12px">
        <div>
            <h2>Commandes</h2>
            <span class="sub">{{ $nbCommandes }} {{ $nbCommandes > 1 ? 'commandes' : 'commande' }}</span>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Date</th>
                    <th>Table</th>
                    <th>Statut</th>
                    <th class="right">Total</th>
                    <th class="right">Plats</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commandes as $c)
                    @php [$cls, $label] = $statusMap[$c->statut] ?? ['served', ucfirst(str_replace('_', ' ', $c->statut))]; @endphp
                    <tr>
                        <td><strong class="num">#{{ $c->id }}</strong></td>
                        <td class="num">
                            {{ $c->created_at->format('d/m/Y') }}
                            <span class="muted">{{ $c->created_at->format('H:i') }}</span>
                        </td>
                        <td>Table {{ $c->table->numero ?? '—' }}</td>
                        <td><span class="status {{ $cls }}">{{ $label }}</span></td>
                        <td class="right num"><strong>{{ $fmt($c->total) }}</strong> <span class="muted">HTG</span></td>
                        <td class="right">
                            <button type="button" class="items-toggle" aria-expanded="false" data-target="items-{{ $c->id }}">
                                {{ $c->items->sum('quantite') }} {{ $c->items->sum('quantite') > 1 ? 'articles' : 'article' }}
                                <i data-lucide="chevron-down"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="items-row" id="items-{{ $c->id }}" hidden>
                        <td colspan="6">
                            <ul class="items-list">
                                @foreach($c->items as $item)
                                    <li><b>{{ $item->quantite }}×</b>{{ $item->plat->nom ?? 'Plat supprimé' }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Aucune vente sur cette période.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (seulement si le contrôleur utilise paginate()) --}}
    @if($isPaginated && $commandes->hasPages())
        @php $commandes->appends(request()->only('month', 'year')); @endphp
        <div class="pager">
            <span>{{ $commandes->firstItem() }}–{{ $commandes->lastItem() }} sur {{ $commandes->total() }}</span>
            <div class="pager-links">
                @if($commandes->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $commandes->previousPageUrl() }}">‹</a>
                @endif
                @foreach($commandes->getUrlRange(max(1, $commandes->currentPage() - 2), min($commandes->lastPage(), $commandes->currentPage() + 2)) as $page => $url)
                    @if($page == $commandes->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($commandes->hasMorePages())
                    <a href="{{ $commandes->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.items-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = document.getElementById(btn.dataset.target);
        const open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!open));
        row.hidden = open;
    });
});
</script>
@endpush