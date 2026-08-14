@extends('caisse.layouts.app')

@section('title','Tableau de bord')

@section('content')

<div class="welcome-card">
    <div>
        <h2>Bonjour {{ auth()->user()->name }}</h2>
        <p>Bienvenue dans votre espace de caisse. Consultez les commandes prêtes et encaissez les paiements en toute simplicité.</p>
    </div>
    <div class="welcome-icon"><i class="fa-solid fa-cash-register"></i></div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card revenue ticket-perforated">
        <div>
            <span>Chiffre d'affaires</span>
            <h2 id="caCounter">{{ number_format($chiffreAffairesJour,2) }}</h2>
            <small>Aujourd'hui</small>
        </div>
        <div class="icon"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    <div class="stat-card orders">
        <div>
            <span>Commandes prêtes</span>
            <h2 id="readyCounter">{{ $countPretes }}</h2>
            <small>À encaisser</small>
        </div>
        <div class="icon"><i class="fa-solid fa-bell-concierge"></i></div>
    </div>
    <div class="stat-card attente">
        <div>
            <span>En attente</span>
            <h2>{{ $countEnAttente }}</h2>
            <small>Cuisine</small>
        </div>
        <div class="icon"><i class="fa-solid fa-clock"></i></div>
    </div>
    <div class="stat-card paiement">
        <div>
            <span>Paiements</span>
            <h2>{{ $countPayeesJour }}</h2>
            <small>Aujourd'hui</small>
        </div>
        <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
    </div>
</div>

<!-- Charts -->
<div class="analytics-grid">
    <div class="chart-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-chart-line"></i> Évolution du chiffre d'affaires</h3>
        </div>
        <canvas id="salesChart" height="120"></canvas>
    </div>
    <div class="mini-stats">
        <div class="mini-card success"><div><small>Espèces</small><h2>{{ $cashPercent ?? 0 }}%</h2></div><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="mini-card primary"><div><small>Carte bancaire</small><h2>{{ $cardPercent ?? 0 }}%</h2></div><i class="fa-solid fa-credit-card"></i></div>
        <div class="mini-card warning"><div><small>MonCash</small><h2>{{ $moncashPercent ?? 0 }}%</h2></div><i class="fa-solid fa-mobile-screen-button"></i></div>
        <div class="mini-card purple"><div><small>NatCash</small><h2>{{ $natcashPercent ?? 0 }}%</h2></div><i class="fa-solid fa-wallet"></i></div>
    </div>
</div>

<div class="analytics-grid two">
    <div class="chart-card">
        <div class="card-header"><h3><i class="fa-solid fa-chart-pie"></i> Répartition des paiements</h3></div>
        <canvas id="paymentChart" height="180"></canvas>
    </div>
    <div class="chart-card">
        <div class="card-header"><h3><i class="fa-solid fa-chart-column"></i> Commandes encaissées</h3></div>
        <canvas id="ordersChart" height="180"></canvas>
    </div>
</div>

<!-- Commandes prêtes -->
<div class="table-card" style="margin-top:10px;">
    <div class="card-header">
        <div>
            <h3><i class="fa-solid fa-receipt"></i> Commandes prêtes à encaisser</h3>
            <small>Toutes les commandes terminées par la cuisine.</small>
        </div>
        <span class="count-badge">{{ $countPretes }} commande(s)</span>
    </div>
    <div class="table-responsive">
        <table class="premium-table">
            <thead>
                <tr><th>#</th><th>Table</th><th>Montant</th><th>Statut</th><th>Heure</th><th>Mode</th><th>Action</th></tr>
            </thead>
            <tbody>
            @forelse($commandesPretes as $commande)
                <tr>
                    <td><strong>#{{ $commande->id }}</strong></td>
                    <td><span class="table-number">🍽️ Table {{ $commande->restaurant_table_id }}</span></td>
                    <td><strong class="amount">{{ number_format($commande->total,2) }} HTG</strong></td>
                    <td><span class="badge-ready"><i class="fa-solid fa-circle-check"></i> Prête</span></td>
                    <td>{{ \Carbon\Carbon::parse($commande->created_at)->format('H:i') }}</td>
                    <td><span class="badge-mode">À définir</span></td>
                    <td>
                    <a href="{{ route('caisse.encaisser', $commande->id) }}"
   class="btn-pay">

    <i class="fa-solid fa-cash-register"></i>

    Encaisser

</a>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        <i class="fa-solid fa-circle-check" style="font-size:50px;color:var(--success);margin-bottom:15px;display:block;"></i>
                        Aucune commande en attente d'encaissement.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<br>
<br>
<!-- Derniers paiements & Répartition -->
<div class="analytics-grid two-columns">
    <div class="table-card">
        <div class="card-header">
            <div>
                <h3><i class="fa-solid fa-credit-card"></i> Derniers paiements</h3>
                <small>Historique des paiements enregistrés aujourd'hui.</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead><tr><th>Facture</th><th>Commande</th><th>Montant</th><th>Mode</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($derniersPaiements as $paiement)
                    <tr>
                        <td><strong>FAC-{{ str_pad($paiement->id,5,'0',STR_PAD_LEFT) }}</strong></td>
                        <td>#{{ $paiement->commande_id }}</td>
                        <td><strong class="amount">{{ number_format($paiement->montant,2) }} HTG</strong></td>
                        <td>
                            @switch($paiement->mode_paiement)
                                @case('Espèces')<span class="badge badge-success">💵 Espèces</span>@break
                                @case('Carte')<span class="badge badge-primary">💳 Carte</span>@break
                                @case('MonCash')<span class="badge badge-warning">📱 MonCash</span>@break
                                @case('NatCash')<span class="badge badge-purple">👛 NatCash</span>@break
                                @default<span class="badge">{{ $paiement->mode_paiement }}</span>
                            @endswitch
                        </td>
                        <td>{{ \Carbon\Carbon::parse($paiement->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Aucun paiement enregistré.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header"><h3><i class="fa-solid fa-wallet"></i> Répartition des paiements</h3></div>
        <div class="payment-summary">
            <div class="payment-item"><div class="left">💵 Espèces</div><strong>{{ $cashCount ?? 0 }}</strong></div>
            <div class="payment-item"><div class="left">💳 Carte bancaire</div><strong>{{ $cardCount ?? 0 }}</strong></div>
            <div class="payment-item"><div class="left">📱 MonCash</div><strong>{{ $moncashCount ?? 0 }}</strong></div>
            <div class="payment-item"><div class="left">👛 NatCash</div><strong>{{ $natcashCount ?? 0 }}</strong></div>
            <div class="payment-item"><div class="left">🏦 Virement</div><strong>{{ $virementCount ?? 0 }}</strong></div>
        </div>
    </div>
</div>

<div class="caisse-footer">
    <div><i class="fa-solid fa-shield-halved"></i> Système de caisse sécurisé</div>
    <div><i class="fa-solid fa-clock"></i> Mise à jour automatique</div>
    <div>Version Resto Kay-Y v1.0</div>
</div>

<div id="toastContainer"></div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* =========================================
   DASHBOARD TEMPS RÉEL — KAY-Y CAISSE
   ========================================= */

let salesChart, paymentChart, ordersChart;
let lastCommandeIds = new Set();
let isRefreshing = false;

document.addEventListener('DOMContentLoaded', function () {
    initCharts();
    startPolling();
    startClock();
    
    // Premye chajman done
    refreshDashboard();
});

/* ====== GRAPHIQUES ====== */
function initCharts(){
    const salesCtx = document.getElementById('salesChart');
    if(salesCtx){
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesLabels ?? []),
                datasets: [{
                    label: "Chiffre d'affaires",
                    data: @json($salesData ?? []),
                    borderColor: '#B87333',
                    backgroundColor: 'rgba(184, 115, 51, 0.15)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#FFF',
                    pointBorderColor: '#B87333',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(44,24,16,.05)' }, ticks: { font: { family: 'IBM Plex Mono' } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter' } } }
                }
            }
        });
    }

    const paymentCtx = document.getElementById('paymentChart');
    if(paymentCtx){
        paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Espèces', 'Carte', 'MonCash', 'NatCash'],
                datasets: [{
                    data: [{{ $cashPercent ?? 0 }}, {{ $cardPercent ?? 0 }}, {{ $moncashPercent ?? 0 }}, {{ $natcashPercent ?? 0 }}],
                    backgroundColor: ['#4A7C59', '#B87333', '#E25822', '#6F4E37'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter' }, padding: 20 } }
                }
            }
        });
    }

    const ordersCtx = document.getElementById('ordersChart');
    if(ordersCtx){
        ordersChart = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: @json($orderLabels ?? []),
                datasets: [{
                    label: 'Commandes',
                    data: @json($orderData ?? []),
                    backgroundColor: '#B87333',
                    borderRadius: 8,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(44,24,16,.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
}

/* ====== POLLING ====== */
function startPolling(){
    // Chak 10 segond
    setInterval(refreshDashboard, 10000);
}

function startClock(){
    const clockEl = document.getElementById('liveClock');
    if(!clockEl) return;
    setInterval(() => {
        const now = new Date();
        clockEl.innerHTML = now.toLocaleDateString('fr-FR', {weekday:'long', day:'2-digit', month:'long'}) + 
                           "<br><b>" + now.toLocaleTimeString('fr-FR') + "</b>";
    }, 1000);
}

/* ====== REFRESH PRINCIPAL ====== */
async function refreshDashboard(){
    if(isRefreshing) return;
    isRefreshing = true;

    try {
        const res = await fetch("{{ route('caisse.api.dashboard') }}", {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if(!res.ok) throw new Error('Erè sèvè');
        const data = await res.json();

        updateStats(data.stats);
        updateCommandesTable(data.commandesPretes);
        updatePaiementsTable(data.derniersPaiements);
        updateRepatisyon(data.repatisyon);
        
        // Joune bouton actualisation
        const footerVersion = document.querySelector('.caisse-footer div:last-child');
        if(footerVersion) footerVersion.innerHTML = '<i class="fa-solid fa-rotate"></i> Dènye mizajou: ' + data.timestamp;

    } catch(e) {
        console.error('Polling error:', e);
    } finally {
        isRefreshing = false;
    }
}

/* ====== MIZAJOU STATS ====== */
function updateStats(stats){
    animateValue('caCounter', parseFloat(document.getElementById('caCounter')?.innerText.replace(/\s/g,'').replace(',','.') || 0), parseFloat(stats.chiffre), 1000, true);
    animateValue('readyCounter', parseInt(document.getElementById('readyCounter')?.innerText || 0), stats.pretes, 800);
    
    // Attente ak peman yo pa gen ID inik nan HTML ou a, ajoute yo si ou vle
    // Sinon nou ka jis mete ajou si eleman yo egziste
    const attenteEl = document.querySelector('.stat-card.attente h2');
    if(attenteEl) animateValueDirect(attenteEl, parseInt(attenteEl.innerText||0), stats.attente, 800);
    
    const payeesEl = document.querySelector('.stat-card.paiement h2');
    if(payeesEl) animateValueDirect(payeesEl, parseInt(payeesEl.innerText||0), stats.payees, 800);
}

function animateValue(id, start, end, duration, isFloat=false){
    const el = document.getElementById(id);
    if(!el) return;
    const range = end - start;
    if(range === 0) return;
    const startTime = performance.now();
    
    function step(now){
        const progress = Math.min((now - startTime) / duration, 1);
        const val = start + (range * progress);
        el.innerText = isFloat ? val.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) : Math.floor(val).toLocaleString('fr-FR');
        if(progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

function animateValueDirect(el, start, end, duration){
    const range = end - start;
    if(range === 0) return;
    const startTime = performance.now();
    function step(now){
        const progress = Math.min((now - startTime) / duration, 1);
        el.innerText = Math.floor(start + (range * progress)).toLocaleString('fr-FR');
        if(progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

/* ====== MIZAJOU TAB KÒMAND PRET ====== */
function updateCommandesTable(commandes){
    const tbody = document.querySelector('.premium-table tbody');
    if(!tbody) return;
    
    let newArrivals = [];
    
    // Tcheke nouvo kòmand
    commandes.forEach(cmd => {
        if(!lastCommandeIds.has(cmd.id)){
            lastCommandeIds.add(cmd.id);
            newArrivals.push(cmd);
        }
    });
    
    if(commandes.length === 0){
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty">
                    <i class="fa-solid fa-circle-check" style="font-size:50px;color:var(--success);margin-bottom:15px;display:block;"></i>
                    Aucune commande en attente d'encaissement.
                </td>
            </tr>`;
        document.querySelector('.count-badge')?.setAttribute('style', 'display:none;');
        return;
    }
    
    // Montre badge kantite a
    const badge = document.querySelector('.count-badge');
    if(badge){
        badge.style.display = 'inline-block';
        badge.innerText = commandes.length + ' commande(s)';
    }
    
    let html = '';
    commandes.forEach(cmd => {
        html += `
        <tr data-id="${cmd.id}">
            <td><strong>#${cmd.id}</strong></td>
            <td><span class="table-number">🍽️ Table ${cmd.restaurant_table_id}</span></td>
            <td><strong class="amount">${cmd.total} HTG</strong></td>
            <td><span class="badge-ready"><i class="fa-solid fa-circle-check"></i> Prête</span></td>
            <td>${cmd.created_at}</td>
            <td><span class="badge-mode">À définir</span></td>
            <td>
                <a href="/caisse/encaisser/${cmd.id}" class="btn-pay">
                    <i class="fa-solid fa-cash-register"></i> Encaisser
                </a>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
    
    // Notifikasyon pou nouvo kòmand
    newArrivals.forEach(cmd => {
        showToast(`🍽️ Nouvo kòmand #${cmd.id} — Table ${cmd.restaurant_table_id}`, 'success');
        playNotifSound();
    });
}

/* ====== MIZAJOU TAB PEMAN ====== */
function updatePaiementsTable(paiements){
    const tbody = document.querySelectorAll('.premium-table')[1]?.querySelector('tbody');
    if(!tbody) return;
    
    if(paiements.length === 0){
        tbody.innerHTML = '<tr><td colspan="5" class="empty">Aucun paiement enregistré.</td></tr>';
        return;
    }
    
    let html = '';
    const modeBadges = {
        'Espèces': ['badge-success', '💵 Espèces'],
        'Carte': ['badge-primary', '💳 Carte'],
        'MonCash': ['badge-warning', '📱 MonCash'],
        'NatCash': ['badge-purple', '👛 NatCash']
    };
    
    paiements.forEach(p => {
        const badge = modeBadges[p.mode_paiement] || ['badge', p.mode_paiement];
        html += `
        <tr>
            <td><strong>FAC-${String(p.id).padStart(5,'0')}</strong></td>
            <td>#${p.commande_id}</td>
            <td><strong class="amount">${p.montant} HTG</strong></td>
            <td><span class="badge ${badge[0]}">${badge[1]}</span></td>
            <td>${p.created_at}</td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

/* ====== MIZAJOU REPARTISYON ====== */
function updateRepatisyon(data){
    const items = document.querySelectorAll('.payment-item strong');
    const labels = ['cashCount', 'cardCount', 'moncashCount', 'natcashCount', 'virementCount'];
    const map = ['cashCount','cardCount','moncashCount','natcashCount','virementCount'];
    
    items.forEach((el, i) => {
        if(map[i] && data[map[i]] !== undefined){
            const val = parseInt(el.innerText) || 0;
            if(val !== data[map[i]]){
                animateValueDirect(el, val, data[map[i]], 600);
            }
        }
    });
}

/* ====== TOAST ====== */
function showToast(message, type='success'){
    const container = document.getElementById('toastContainer');
    if(!container) return;
    let toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = '<i class="fa-solid fa-bell"></i> ' + message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

/* ====== SON NOTIFIKASYON ====== */
function playNotifSound(){
    const audio = document.getElementById('notifSound');
    if(audio){
        audio.currentTime = 0;
        audio.play().catch(e => {}); // Ignorer erè autoplay
    }
}

/* ====== SESYON FLASH ====== */
@if(session('success')) showToast("{{ session('success') }}"); @endif
@if(session('error')) showToast("{{ session('error') }}", 'error'); @endif

/* ====== INISYALIZE SET KÒMAND KI TE LA ====== */
document.querySelectorAll('.premium-table tbody tr[data-id]').forEach(tr => {
    lastCommandeIds.add(parseInt(tr.dataset.id));
});

</script>
@endpush