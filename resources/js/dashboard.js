import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    // 🕒 Realtime Clock
    function updateClock() {
        const now = new Date();
        const clockElement = document.getElementById('liveClock');
        if (clockElement) {
            clockElement.innerText = now.toLocaleTimeString();
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 📈 Chart.js - Sales Week
    const ctxSalesElement = document.getElementById('salesWeekChart');
    if (ctxSalesElement && window.salesChartData) {
        new Chart(ctxSalesElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: window.salesChartData.map(item => item.date),
                datasets: [{
                    label: 'Ventes (HTG)',
                    data: window.salesChartData.map(item => item.total),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    // 🍩 Chart.js - Order Status Doughnut
    const ctxStatusElement = document.getElementById('orderStatusChart');
    if (ctxStatusElement && window.orderStatsData) {
        new Chart(ctxStatusElement.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Prêtes', 'En Préparation', 'Nouvelles'],
                datasets: [{
                    data: [
                        window.orderStatsData.completed, 
                        window.orderStatsData.preparing, 
                        window.orderStatsData.new
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 🔔 Pusher & Laravel Echo Real-time Notifications Setup
    const sound = document.getElementById('notifSound');
    let unlocked = false;

    document.body.addEventListener('click', () => {
        unlocked = true;
        if (sound) {
            sound.play().then(() => { sound.pause(); sound.currentTime = 0; }).catch(()=>{});
        }
    }, { once: true });

    function playSound() {
        if (!unlocked || !sound) return;
        sound.currentTime = 0;
        sound.play().catch(()=>{});
    }

    function initEcho() {
        if (!window.Echo) {
            setTimeout(initEcho, 500);
            return;
        }

        const channel = window.Echo.channel('kitchen');

        channel.listen('.new-order', (e) => {
            playSound();
            const tableBody = document.getElementById('order-list-table');
            if (!tableBody) return;

            const newRow = `
                <tr id="order-row-${e.commande.id}" style="background: #eff6ff; transition: background 1s;">
                    <td><strong>#${e.commande.id}</strong></td>
                    <td>Table ${e.commande.table ? e.commande.table.numero : 'N/A'}</td>
                    <td>${e.commande.client || 'Client standard'}</td>
                    <td><strong>${parseFloat(e.commande.total).toFixed(2)} HTG</strong></td>
                    <td><span class="badge-status new">Nouvelle</span></td>
                    <td>A l'instant</td>
                    <td>
                        <a href="/facture/${e.commande.id}" class="btn-view">👁 Voir Facture</a>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('afterbegin', newRow);
        });

        channel.listen('.ready', (e) => {
            playSound();
            const row = document.getElementById('order-row-' + e.commande.id);
            if (row) {
                const badge = row.querySelector('.badge-status');
                if(badge) {
                    badge.className = 'badge-status ready';
                    badge.innerText = 'Prête';
                }
            }
        });

        channel.listen('.accepted', (e) => {
            playSound();
            const row = document.getElementById('order-row-' + e.commande.id);
            if (row) {
                const badge = row.querySelector('.badge-status');
                if(badge) {
                    badge.className = 'badge-status prep';
                    badge.innerText = 'En préparation';
                }
            }
        });
    }

    initEcho();

    // ☀️ Real-time Weather - Les Cayes
    const weatherTemp = document.getElementById('weatherTemp');
    if (weatherTemp) {
        fetch('https://api.open-meteo.com/v1/forecast?latitude=18.1965&longitude=-73.7442&current=temperature_2m')
            .then(response => response.json())
            .then(data => {
                if (data && data.current) {
                    const temp = Math.round(data.current.temperature_2m);
                    weatherTemp.innerText = temp + '°C';
                }
            })
            .catch(error => console.error('Erreur météo:', error));
    }
});