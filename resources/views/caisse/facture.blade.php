@extends('caisse.layouts.app')


@section('title', 'Facture #' . ($paiement->numero_facture ?? $paiement->id))

@section('content')

<div class="facture-page">





{{-- FACTURE --}}
<div class="facture-card">

    {{-- HEADER --}}
    <div class="facture-header">

        <div class="restaurant-info">

            <div class="restaurant-logo">
                🍽️
            </div>

            <div>
                <h1>Resto Kay-Y</h1>

                <p>
                    Cuisine haïtienne traditionnelle
                </p>

                <small>
                    Merci de votre confiance
                </small>
            </div>

        </div>


        <div class="facture-number">

            <span>FACTURE</span>

            <strong>
                {{ $paiement->numero_facture ?? 'FAC-' . str_pad($paiement->id, 5, '0', STR_PAD_LEFT) }}
            </strong>

            <small>
                {{ $paiement->created_at->format('d/m/Y H:i') }}
            </small>

        </div>

    </div>


    <div class="facture-divider"></div>


    {{-- INFORMATIONS --}}
    <div class="facture-info">

        <div class="info-box">

            <span>
                <i class="fa-solid fa-receipt"></i>
                Commande
            </span>

            <strong>
                #{{ $paiement->commande_id }}
            </strong>

        </div>


        <div class="info-box">

            <span>
                <i class="fa-solid fa-chair"></i>
                Table
            </span>

            <strong>
                {{ $paiement->commande->restaurant_table_id ?? 'N/A' }}
            </strong>

        </div>


        <div class="info-box">

            <span>
                <i class="fa-solid fa-user"></i>
                Caissier
            </span>

            <strong>
                {{ $paiement->caissier ?? auth()->user()->name ?? 'N/A' }}
            </strong>

        </div>


        <div class="info-box">

            <span>
                <i class="fa-solid fa-calendar"></i>
                Date
            </span>

            <strong>
                {{ $paiement->created_at->format('d/m/Y') }}
            </strong>

        </div>

    </div>


    {{-- ARTICLES --}}
    <div class="facture-section-title">

        <h3>
            <i class="fa-solid fa-utensils"></i>
            Détails de la commande
        </h3>

    </div>


    <div class="facture-table-wrapper">

        <table class="facture-table">

            <thead>

                <tr>

                    <th>Article</th>

                    <th>Qté</th>

                    <th>Prix unitaire</th>

                    <th>Total</th>

                </tr>

            </thead>


            <tbody>

                @forelse($paiement->commande->items ?? [] as $item)

                    <tr>

                        <td>

                            <div class="article-name">

                                <span class="article-icon">
                                    🍽️
                                </span>

                                <strong>
                                    {{ $item->plat->nom ?? 'Article supprimé' }}
                                </strong>

                            </div>

                        </td>


                        <td>

                            <span class="quantity">
                                {{ $item->quantite }}
                            </span>

                        </td>


                        <td>

                            {{ number_format($item->prix, 2) }}
                            HTG

                        </td>


                        <td>

                            <strong>

                                {{ number_format($item->prix * $item->quantite, 2) }}

                                HTG

                            </strong>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="empty-facture">

                            <i class="fa-solid fa-box-open"></i>

                            Aucun article trouvé pour cette commande.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- TOTAL --}}
    <div class="facture-bottom">

        <div class="payment-information">

            <div class="payment-label">
                <i class="fa-solid fa-credit-card"></i>
                Mode de paiement
            </div>

            <div class="payment-method">

                @switch($paiement->mode_paiement)

                    @case('Espèces')
                        💵 Espèces
                        @break

                    @case('Carte')
                        💳 Carte bancaire
                        @break

                    @case('MonCash')
                        📱 MonCash
                        @break

                    @case('NatCash')
                        👛 NatCash
                        @break

                    @case('Virement')
                        🏦 Virement
                        @break

                    @default
                        {{ $paiement->mode_paiement }}

                @endswitch

            </div>

        </div>


        <div class="total-box">

            <span>
                TOTAL À PAYER
            </span>

            <strong>

                {{ number_format($paiement->montant, 2) }}

                <small>HTG</small>

            </strong>

        </div>

    </div>


    {{-- STATUT --}}
    <div class="payment-success">

        <i class="fa-solid fa-circle-check"></i>

        <div>

            <strong>
                Paiement confirmé
            </strong>

            <span>
                Cette transaction a été enregistrée avec succès.
            </span>

        </div>

    </div>


    {{-- FOOTER --}}
    <div class="facture-footer">

        <div>

            <strong>
                Resto Kay-Y
            </strong>

            <p>
                Cuisine haïtienne traditionnelle
            </p>

            <small>
                Merci de votre visite ❤️
            </small>

        </div>


        <div class="footer-actions">

            <a href="{{ url('/caisse/dashboard') }}" class="btn-secondary">

                <i class="fa-solid fa-house"></i>

                Caisse

            </a>


            <button
                onclick="window.print()"
                class="btn-pay">

                <i class="fa-solid fa-print"></i>

                Imprimer

            </button>

        </div>

    </div>

</div>


</div>

@endsection

@push('styles')



@endpush
