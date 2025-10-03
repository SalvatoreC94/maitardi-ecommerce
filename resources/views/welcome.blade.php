<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Test Carrello</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }

        h1 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 60%;
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
            padding: 6px 12px;
            cursor: pointer;
            margin: 5px;
        }
    </style>
</head>

<body>
    <h1>🛒 Test Carrello</h1>

    <div>
        <button onclick="addToCart(1)">Aggiungi Prodotto ID 1</button>
        <button onclick="addToCart(2)">Aggiungi Prodotto ID 2</button>
        <button onclick="clearCart()">Svuota Carrello</button>
    </div>

    <h2>Carrello Attuale</h2>
    <div id="cart"></div>

    <script>
        async function fetchCart() {
            const res = await fetch('/cart');
            const data = await res.json();
            renderCart(data);
        }

        async function addToCart(productId) {
            await fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantity: 1
                })
            });
            fetchCart();
        }

        async function removeItem(itemId) {
            await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            fetchCart();
        }

        async function clearCart() {
            await fetch('/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            fetchCart();
        }

        function renderCart(cart) {
            if (!cart.items || cart.items.length === 0) {
                document.getElementById('cart').innerHTML = "<p>Il carrello è vuoto.</p>";
                return;
            }

            let html = `
                <table>
                    <tr>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th>Prezzo</th>
                        <th>Totale</th>
                        <th>Azioni</th>
                    </tr>
            `;

            cart.items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.product.name}</td>
                        <td>${item.quantity}</td>
                        <td>€${item.unit_price}</td>
                        <td>€${(item.quantity * item.unit_price).toFixed(2)}</td>
                        <td><button onclick="removeItem(${item.id})">Rimuovi</button></td>
                    </tr>
                `;
            });

            html += `</table>
                <p><strong>Subtotale:</strong> €${cart.totals.subtotal.toFixed(2)}</p>
                <p><strong>Spedizione:</strong> €${cart.totals.shipping.toFixed(2)}</p>
                <p><strong>Totale:</strong> €${cart.totals.total.toFixed(2)}</p>
            `;

            document.getElementById('cart').innerHTML = html;
        }

        // carica carrello all'avvio
        fetchCart();
    </script>
</body>

</html>
