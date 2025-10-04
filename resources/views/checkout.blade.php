<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }

        .row {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .col {
            flex: 1;
        }

        label {
            display: block;
            margin-top: .5rem;
        }

        input,
        select {
            width: 100%;
            padding: .5rem;
            margin-top: .25rem;
        }

        .error {
            color: #b00;
            margin-bottom: 1rem;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #f8f8f8;
        }

        button {
            margin-top: 1rem;
            padding: .6rem 1rem;
            cursor: pointer;
        }

        .muted {
            color: #666;
            font-size: .95rem;
        }
    </style>
</head>

<body>
    <h1>Checkout</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <h2>Dati cliente</h2>
            <form method="POST" action="{{ route('checkout.place') }}">
                @csrf

                <label>Email
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" required>
                </label>

                <label>Nome e cognome
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required>
                </label>

                <label>Telefono (opzionale)
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}">
                </label>

                <h3>Fatturazione</h3>
                <label>Via
                    <input type="text" name="billing[via]" value="{{ old('billing.via') }}" required>
                </label>
                <label>Città
                    <input type="text" name="billing[citta]" value="{{ old('billing.citta') }}" required>
                </label>
                <label>CAP
                    <input type="text" name="billing[cap]" value="{{ old('billing.cap') }}" required>
                </label>

                <h3>Spedizione</h3>
                <label>Via
                    <input type="text" name="shipping[via]" value="{{ old('shipping.via') }}" required>
                </label>
                <label>Città
                    <input type="text" name="shipping[citta]" value="{{ old('shipping.citta') }}" required>
                </label>
                <label>CAP
                    <input type="text" name="shipping[cap]" value="{{ old('shipping.cap') }}" required>
                </label>

                <h3>Metodo di spedizione</h3>
                <select name="shipping_method_id" required>
                    @foreach ($shippingMethods as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->name }} — €{{ number_format($m->rate, 2) }}
                        </option>
                    @endforeach
                </select>

                <label>Note (opzionale)
                    <input type="text" name="notes" value="{{ old('notes') }}">
                </label>

                <button type="submit">Conferma Ordine</button>
            </form>
        </div>

        <div class="col">
            <h2>Riepilogo carrello</h2>
            @if (empty($cart['items']) || count($cart['items']) === 0)
                <p>Il carrello è vuoto.</p>
            @else
                <table>
                    <tr>
                        <th>Prodotto</th>
                        <th>Q.tà</th>
                        <th>Prezzo</th>
                        <th>Totale</th>
                    </tr>
                    @foreach ($cart['items'] as $item)
                        <tr>
                            <td>{{ $item['product']['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>€{{ number_format($item['unit_price'], 2) }}</td>
                            <td>€{{ number_format($item['quantity'] * $item['unit_price'], 2) }}</td>
                        </tr>
                    @endforeach
                </table>

                <p><strong>Subtotale:</strong> €{{ number_format($cart['totals']['subtotal'], 2) }}</p>
                <p class="muted">La spedizione verrà calcolata in base al metodo selezionato.</p>
                <p class="muted"><em>Il totale definitivo sarà mostrato dopo la conferma.</em></p>
            @endif
        </div>
    </div>
</body>

</html>
