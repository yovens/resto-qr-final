@extends('admin.layouts.app')

@section('title', 'Facture #' . $commande->id)

@section('content')

<style>
    .facture-container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 40px;
    }
    .facture-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #f59e0b;
    }
    .facture-header h1 {
        font-size: 2rem;
        color: #1f2937;
        margin: 0;
    }
    .facture-header .badge {
        background: #f59e0b;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    .info-box {
        background: #f9fafb;
        padding: 15px;
        border-radius: 10px;
    }
    .info-box strong {
        color: #6b7280;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    .info-box p {
        margin: 5px 0 0;
        font-size: 1.1rem;
        color: #111;
        font-weight: 600;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .items-table th {
        background: #1f2937;
        color: white;
        padding: 12px;
        text-align: left;
    }
    .items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .items-table tr:last-child td {
        border-bottom: 2px solid #1f2937;
    }
    .total-section {
        text-align: right;
        margin-top: 20px;
    }
    .total-section .grand-total {
        font-size: 1.8rem;
        color: #f59e0b;
        font-weight: bold;
    }
    .actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 30px;
    }
    .btn-print, .btn-back, .btn-pdf {
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-print { background: #1f2937; color: white; }
    .btn-back { background: #e5e7eb; color: #374151; }
    .btn-pdf { background: #dc2626; color: white; }
    
    @media print {
        .actions, .btn-back, .btn-print { display: none !important; }
        .facture-container { box-shadow: none; margin: 0; }
    }
</style>

<div class="facture-container">
    {{-- HEADER --}}
    <div class="facture-header">
        <div>
            <h1>🧾 FACTURE</h1>
            <p style="color: #6b7280; margin-top: 5px;">Restaurant PRO</p>
        </div>
        <div style="text-align: right;">
            <span class="badge">#{{ $commande->id }}</span>
            <p style="margin-top: 10px; color: #6b7280;">
                {{ $commande->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>

    {{-- INFO CLIENT & TABLE --}}
    <div class="info-grid">
        <div class="info-box">
            <strong>👤 Client</strong>
            <p>{{ $commande->client ?? 'Client standard' }}</p>
        </div>
        <div class="info-box">
            <strong>🪑 Table</strong>
            <p>Table {{ $commande->table->numero ?? 'N/A' }}</p>
        </div>
        <div class="info-box">
            <strong>📋 Statut</strong>
            <p>
                @if($commande->statut == 'nouvelle')
                    <span style="color: #3b82f6;">Nouvelle</span>
                @elseif($commande->statut == 'en_preparation')
                    <span style="color: #f59e0b;">En préparation</span>
                @else
                    <span style="color: #10b981;">Prête</span>
                @endif
            </p>
        </div>
        <div class="info-box">
            <strong>👨‍🍳 Servi par</strong>
            <p>{{ $commande->user->name ?? 'Staff' }}</p>
        </div>
    </div>

    {{-- DETAY PLAT YO --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Plat</th>
                <th style="text-align: center;">Qté</th>
                <th style="text-align: right;">Prix Unitaire</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->plat->nom ?? 'Plat supprimé' }}</strong>
                    @if($item->commentaire)
                        <br><small style="color: #6b7280;">Note: {{ $item->commentaire }}</small>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->quantite }}</td>
                <td style="text-align: right;">{{ number_format($item->prix_unitaire ?? $item->plat->prix ?? 0, 2) }} HTG</td>
                <td style="text-align: right;">
                    <strong>{{ number_format(($item->prix_unitaire ?? $item->plat->prix ?? 0) * $item->quantite, 2) }} HTG</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAL --}}
    <div class="total-section">
        <p style="color: #6b7280; margin-bottom: 5px;">Total à payer</p>
        <p class="grand-total">{{ number_format($commande->total, 2) }} HTG</p>
        @if($commande->remise > 0)
            <p style="color: #10b981; font-size: 0.9rem;">Remise appliquée: {{ $commande->remise }}%</p>
        @endif
    </div>

    {{-- BOUTON YO --}}
    <div class="actions">
        <a href="{{ url()->previous() }}" class="btn-back">⬅ Retour</a>
        <button onclick="window.print()" class="btn-print">🖨 Imprimer</button>
        {{-- Si ou gen dompdf enstale --}}
        {{-- <a href="{{ route('facture.download', $commande) }}" class="btn-pdf">📄 Télécharger PDF</a> --}}
    </div>
</div>

@endsection