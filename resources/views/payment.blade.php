<!DOCTYPE html>
<html>
<head>
    <title>Paiement</title>
</head>

<body>

<h1>💳 Paiement Commande</h1>

<p>Total: {{ $commande->total }} HTG</p>

<button onclick="pay('cash')">Cash</button>
<button onclick="pay('mobile_money')">Mobile Money</button>

<script>
function pay(method) {
    fetch('/payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            commande_id: {{ $commande->id }},
            methode: method
        })
    })
    .then(res => res.json())
    .then(data => {
        alert("Paiement effectué !");
    });
}
</script>

</body>
</html>