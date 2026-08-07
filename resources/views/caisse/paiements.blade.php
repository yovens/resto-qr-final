@extends('caisse.layouts.app')

@section('content')


<div style="
background:white;
padding:30px;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
">


<h2 style="margin-bottom:25px;">
    💳 Historique des paiements
</h2>



<div style="overflow-x:auto;">


<table style="
width:100%;
border-collapse:collapse;
">


<thead>

<tr style="
background:#1f2937;
color:white;
">


<th style="padding:15px;">
Facture
</th>


<th style="padding:15px;">
Commande
</th>


<th style="padding:15px;">
Montant
</th>


<th style="padding:15px;">
Mode
</th>


<th style="padding:15px;">
Caissier
</th>


<th style="padding:15px;">
Date
</th>


</tr>

</thead>



<tbody>



@forelse($paiements as $paiement)



<tr style="border-bottom:1px solid #eee;">



<td style="padding:15px;">

{{ $paiement->numero_facture ?? 'FAC-'.$paiement->id }}

</td>




<td style="padding:15px;">

#{{ $paiement->commande_id }}

</td>




<td style="padding:15px;color:#059669;font-weight:bold;">

{{ number_format($paiement->montant,2) }} HTG

</td>




<td style="padding:15px;">

{{ $paiement->mode_paiement }}

</td>




<td style="padding:15px;">

{{ $paiement->caissier ?? 'Admin' }}

</td>




<td style="padding:15px;">

{{ $paiement->created_at->format('d/m/Y H:i') }}

</td>



</tr>



@empty



<tr>

<td colspan="6" style="padding:40px;text-align:center;">

Aucun paiement enregistré.

</td>

</tr>



@endforelse



</tbody>


</table>


</div>


<div style="margin-top:20px;">

{{ $paiements->links() }}

</div>



</div>



@endsection