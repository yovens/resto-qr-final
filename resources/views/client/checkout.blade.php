<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>

<h2>🧾 Validation commande</h2>

<button onclick="checkout()">
    Confirmer commande
</button>

<script>

function checkout()
{
    fetch('/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            table_id: {{ $tableId ?? 1 }}
        })
    })
    .then(res => res.json())
    .then(data => {

        alert("🧾 " + data.message);

        console.log("COMMANDE:", data);

        // redirection propre
        window.location.href = "/menu/{{ $tableId ?? 1 }}";

    })
    .catch(error => {
        console.error("Erreur:", error);
        alert("Erreur commande");
    });
}

</script>

</body>
</html>

