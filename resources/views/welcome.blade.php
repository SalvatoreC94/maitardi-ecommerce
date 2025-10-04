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
            border: 1px solid #170909;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #5b1818;
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

    <div id="productButtons"></div>
    <button onclick="clearCart()">Svuota Carrello</button>
    <a id="goCheckout" href="/checkout"><button style="margin-left:10px" disabled>Vai al checkout</button></a>


    <h2>Carrello Attuale</h2>
    <div id="cart"></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        async function fetchCart() {
            const res = await fetch('/cart', {
                credentials: 'same-origin'
            });
            const data = await res.json();
            console.log('CART', data); // debug
            renderCart(data);
            toggleCheckoutButton(data);
        }

        async function fetchProducts() {
            const res = await fetch('/dev-products', {
                credentials: 'same-origin'
            });
            const prods = await res.json();

            const wrap = document.querySelector('#productButtons');
            wrap.innerHTML = '';
            prods.forEach(p => {
                const btn = document.createElement('button');
                btn.textContent = `Aggiungi: ${p.name} (ID ${p.id})`;
                btn.style.marginRight = '10px';
                btn.onclick = () => addToCart(p.id);
                wrap.appendChild(btn);
            });
        }

        async function addToCart(productId) {
            const res = await fetch(`/cart/add/${productId}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    quantity: 1
                })
            });
            if (!res.ok) {
                const txt = await res.text();
                alert(`Errore addToCart (${res.status}): ${txt}`);
                return;
            }
            fetchCart();
        }

        async function removeItem(itemId) {
            const res = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) {
                alert('Errore removeItem');
                return;
            }
            fetchCart();
        }

        async function clearCart() {
            const res = await fetch('/cart/clear', {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) {
                alert('Errore clearCart');
                return;
            }
            fetchCart();
        }

        function toggleCheckoutButton(cart) {
            const btn = document.querySelector('#goCheckout button');
            const hasItems = cart.items && cart.items.length > 0;
            btn.disabled = !hasItems;
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
                const tot = (item.quantity * item.unit_price).toFixed(2);
                html += `
        <tr>
          <td>${item.product?.name ?? '(prodotto)'}</td>
          <td>${item.quantity}</td>
          <td>€${Number(item.unit_price).toFixed(2)}</td>
          <td>€${tot}</td>
          <td><button onclick="removeItem(${item.id})">Rimuovi</button></td>
        </tr>
      `;
            });
            html += `</table>
      <p><strong>Subtotale:</strong> €${Number(cart.totals.subtotal).toFixed(2)}</p>
      <p><strong>Spedizione:</strong> €${Number(cart.totals.shipping).toFixed(2)}</p>
      <p><strong>Totale:</strong> €${Number(cart.totals.total).toFixed(2)}</p>
    `;
            document.getElementById('cart').innerHTML = html;
        }

        // genera i bottoni dai prodotti reali + carica carrello
        fetchProducts().then(fetchCart);
    </script>


</body>


</html>
