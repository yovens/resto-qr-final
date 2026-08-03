<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Facture #{{ $commande->id }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #eef2f7; 
            padding: 40px 20px; 
            display: flex; 
            justify-content: center; 
        }

        .receipt {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 10px solid #2575fc; /* Touch ble pwofesyonèl */
        }

        h1 { color: #333; text-transform: uppercase; letter-spacing: 2px; font-size: 1.5rem; text-align: center; }

        .info-header { border-bottom: 2px dashed #eee; padding-bottom: 20px; margin-bottom: 20px; color: #555; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #888; font-weight: 600; padding: 10px 0; border-bottom: 1px solid #eee; }
        td { padding: 12px 0; color: #333; font-weight: 500; }

        .total-box {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fe;
            border-radius: 10px;
            text-align: right;
            font-size: 1.4rem;
            color: #2575fc;
            font-weight: bold;
        }

        .btn {
            display: block;
            margin-top: 30px;
            text-align: center;
            padding: 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn:hover { background: #000; transform: translateY(-2px); }
    </style>
</head>

<body>

<div class="receipt">
    <h1>🧾 Reçu de Paiement</h1>

    <div class="info-header">
        <p><strong>Commande :</strong> #{{ $commande->id }}</p>
        <p><strong>Table :</strong> {{ $commande->table->numero ?? 'N/A' }}</p>
        <p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Plat</th>
                <th>Qté</th>
                <th>Prix</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
            <tr>
                <td>{{ $item->plat->nom ?? 'Plat inconnu' }}</td>
                <td>x{{ $item->quantite }}</td>
                <td>{{ number_format($item->prix, 2) }} HTG</td>
                <td style="text-align: right;">{{ number_format($item->prix * $item->quantite, 2) }} HTG</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        TOTAL : {{ number_format($commande->total, 2) }} HTG
    </div>

    <a class="btn" href="/admin/dashboard">
        ← Retour au Dashboard
    </a>
</div>

</body>
</html>