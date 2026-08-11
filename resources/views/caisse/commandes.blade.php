@extends('caisse.layouts.app')

@section('title', 'Commandes')

@section('content')

<div class="table-card">

```
{{-- HEADER --}}
<div class="card-header">

    <div>
        <h3>
            <i class="fa-solid fa-receipt"></i>
            Toutes les commandes
        </h3>

        <small>
            Suivi des commandes et accès rapide à l'encaissement.
        </small>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">

        <span class="count-badge">
            {{ $commandes->count() }} commande(s)
        </span>

    </div>

</div>


{{-- TABLE --}}
<div class="table-responsive">

    <table class="premium-table">

        <thead>

            <tr>
                <th>#</th>
                <th>Table</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
            </tr>

        </thead>


        <tbody>

            @forelse($commandes as $commande)

                <tr>

                    {{-- ID --}}
                    <td>

                        <strong>
                            #{{ $commande->id }}
                        </strong>

                    </td>


                    {{-- TABLE --}}
                    <td>

                        <span class="table-number">

                            🍽️ Table
                            {{ $commande->restaurant_table_id }}

                        </span>

                    </td>


                    {{-- TOTAL --}}
                    <td>

                        <strong class="amount">

                            {{ number_format(
                                $commande->total,
                                2
                            ) }}

                            HTG

                        </strong>

                    </td>


                    {{-- STATUT --}}
                    <td>

                        @if($commande->statut === 'prete')

                            <span class="badge-ready">

                                <i class="fa-solid fa-circle-check"></i>

                                Prête à encaisser

                            </span>


                        @elseif($commande->statut === 'nouvelle')

                            <span class="badge badge-info">

                                <i class="fa-solid fa-bell"></i>

                                Nouvelle

                            </span>


                        @elseif($commande->statut === 'en_preparation')

                            <span class="badge badge-warning">

                                <i class="fa-solid fa-fire-burner"></i>

                                En préparation

                            </span>


                        @elseif($commande->statut === 'preparation')

                            <span class="badge badge-warning">

                                <i class="fa-solid fa-kitchen-set"></i>

                                En cuisine

                            </span>


                        @elseif($commande->statut === 'payee')

                            <span class="badge badge-success">

                                <i class="fa-solid fa-circle-check"></i>

                                Payée

                            </span>


                        @else

                            <span class="badge">

                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $commande->statut
                                    )
                                ) }}

                            </span>

                        @endif

                    </td>


                    {{-- DATE --}}
                    <td>

                        {{ $commande->created_at->format(
                            'd/m/Y H:i'
                        ) }}

                    </td>


                    {{-- ACTION --}}
                    <td>

                        @if($commande->statut === 'prete')

                            {{-- ENCAISSER --}}

                            <a
                                href="{{ route(
                                    'caisse.encaisser',
                                    $commande->id
                                ) }}"
                                class="btn-pay"
                            >

                                <i class="fa-solid fa-cash-register"></i>

                                Encaisser

                            </a>


                        @elseif($commande->statut === 'payee')

                            {{-- FACTURE --}}

                            @php

                                $paiement = $commande->paiements
                                    ->sortByDesc('created_at')
                                    ->first();

                            @endphp


                            @if($paiement)

                                <a
                                    href="{{ route(
                                        'caisse.facture',
                                        $paiement->id
                                    ) }}"
                                    class="btn-pay"
                                    style="background:#4a7c59;"
                                >

                                    <i class="fa-solid fa-file-invoice"></i>

                                    Voir facture

                                </a>

                            @else

                                <span class="badge badge-success">

                                    <i class="fa-solid fa-check"></i>

                                    Payée

                                </span>

                            @endif


                        @else

                            <span
                                style="
                                    color:#94a3b8;
                                    font-size:18px;
                                "
                            >
                                —

                            </span>

                        @endif

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="6"
                        class="empty"
                    >

                        <i
                            class="fa-solid fa-receipt"
                            style="
                                font-size:45px;
                                display:block;
                                margin-bottom:15px;
                            "
                        ></i>

                        <strong>
                            Aucune commande trouvée.
                        </strong>

                        <br>

                        <small>
                            Les commandes apparaîtront ici.
                        </small>

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>
```

</div>

{{-- STYLE LOCAL --}}

<style>

.badge-info {
    background: #dbeafe;
    color: #2563eb;
}

.btn-pay {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}

.table-number {
    font-weight: 600;
}

.amount {
    white-space: nowrap;
}

</style>

@endsection
