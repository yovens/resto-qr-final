@extends('caisse.layouts.app')

@section('title', 'Historique des paiements')

@section('content')

<div class="table-card">


<div class="card-header">

    <div>
        <h3>
            <i class="fa-solid fa-clock-rotate-left"></i>
            Historique des paiements
        </h3>

        <small>
            Consultez tous les paiements enregistrés et leurs factures.
        </small>
    </div>

    <span class="count-badge">
        {{ $paiements->total() }} paiement(s)
    </span>

</div>


<div class="table-responsive">

    <table class="premium-table">

        <thead>

            <tr>
                <th>Facture</th>
                <th>Commande</th>
                <th>Table</th>
                <th>Montant</th>
                <th>Mode</th>
                <th>Date</th>
                <th>Action</th>
            </tr>

        </thead>


        <tbody>

            @forelse($paiements as $paiement)

                <tr>

                    {{-- FACTURE --}}
                    <td>

                        <strong>
                            {{ $paiement->numero_facture
                                ?? 'FAC-' . str_pad(
                                    $paiement->id,
                                    5,
                                    '0',
                                    STR_PAD_LEFT
                                )
                            }}
                        </strong>

                    </td>


                    {{-- COMMANDE --}}
                    <td>

                        <strong>
                            #{{ $paiement->commande_id }}
                        </strong>

                    </td>


                    {{-- TABLE --}}
                    <td>

                        🍽️

                        @if($paiement->commande)

                            Table
                            {{ $paiement->commande->restaurant_table_id }}

                        @else

                            —

                        @endif

                    </td>


                    {{-- MONTANT --}}
                    <td>

                        <strong class="amount">

                            {{ number_format(
                                $paiement->montant,
                                2
                            ) }}

                            HTG

                        </strong>

                    </td>


                    {{-- MODE PAIEMENT --}}
                    <td>

                        @switch($paiement->mode_paiement)

                            @case('Espèces')

                                <span class="badge badge-success">
                                    💵 Espèces
                                </span>

                                @break


                            @case('Carte')

                                <span class="badge badge-primary">
                                    💳 Carte
                                </span>

                                @break


                            @case('MonCash')

                                <span class="badge badge-warning">
                                    📱 MonCash
                                </span>

                                @break


                            @case('NatCash')

                                <span class="badge badge-purple">
                                    👛 NatCash
                                </span>

                                @break


                            @case('Virement')

                                <span class="badge">
                                    🏦 Virement
                                </span>

                                @break


                            @default

                                <span class="badge">
                                    {{ $paiement->mode_paiement }}
                                </span>

                        @endswitch

                    </td>


                    {{-- DATE --}}
                    <td>

                        {{ $paiement->created_at->format('d/m/Y H:i') }}

                    </td>


                    {{-- ACTION --}}
                    <td>

                        <a
                            href="{{ route('caisse.facture', $paiement->id) }}"
                            class="btn-pay"
                            title="Voir la facture"
                        >

                            <i class="fa-solid fa-file-invoice"></i>

                            Facture

                        </a>

                    </td>

                </tr>


            @empty

                <tr>

                    <td colspan="7" class="empty">

                        <i
                            class="fa-solid fa-receipt"
                            style="
                                font-size:45px;
                                margin-bottom:15px;
                                display:block;
                            "
                        ></i>

                        Aucun paiement enregistré.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{-- PAGINATION --}}

@if($paiements->hasPages())

    <div style="padding:20px;">

        {{ $paiements->links() }}

    </div>

@endif


</div>

@endsection
