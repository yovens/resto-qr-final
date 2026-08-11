
@extends('caisse.layouts.app')

@section('title', 'Commandes')

@section('content')

<div class="table-card">

    {{-- HEADER --}}
    <div class="card-header">

        <div>
            <h3>
                <i class="fa-solid fa-receipt"></i>
                Commandes
            </h3>

            <small>
                Suivi des commandes et accès à l'encaissement.
            </small>
        </div>

        <span class="count-badge">
            {{ $commandes->count() }} commande(s)
        </span>

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

                            @elseif(
                                $commande->statut === 'nouvelle'
                                ||
                                $commande->statut === 'en_preparation'
                                ||
                                $commande->statut === 'preparation'
                            )

                                <span
                                    style="
                                        color:#94a3b8;
                                        font-size:14px;
                                    "
                                >
                                    En attente
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
                                Aucune commande en cours.
                            </strong>

                            <br>

                            <small>
                                Les nouvelles commandes apparaîtront ici.
                            </small>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
