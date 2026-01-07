<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
<title>POS</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar bg-primary text-white px-3 d-flex justify-content-between">
  <span class="fw-semibold">Simple POS</span>
  <div>
    <a href="admin/products.php" class="btn btn-warning btn-sm me-2">Admin</a>
    <a href="reports/analytics.php" class="btn btn-success btn-sm me-2">Reports</a>
    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container-fluid mt-3">
<div class="row">

<!-- LEFT -->
<div class="col-md-4 mb-3">
  <button class="btn btn-primary w-100 mb-2" onclick="startScanner()">📷 Scan Barcode</button>
  <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add Item</button>
  <div id="reader" class="mt-2"></div>
</div>

<!-- RIGHT -->
<div class="col-md-8">
  <table class="table table-bordered bg-white">
    <thead class="table-light">
      <tr>
        <th>Item</th>
        <th width="80">Qty</th>
        <th width="120">Price</th>
        <th width="120">Subtotal</th>
        <th width="60">X</th>
      </tr>
    </thead>
    <tbody id="cart"></tbody>
  </table>

  <h3>Total: ₱<span id="total">0.00</span></h3>

  <input class="form-control mb-2" id="cash" placeholder="Cash tendered" type="number">
  <button class="btn btn-success btn-lg w-100" onclick="pay()">PAY</button>
</div>

</div>
</div>

<!-- ADD ITEM MODAL -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">Add Item</div>
      <div class="modal-body">
        <input id="m_name" class="form-control mb-2" placeholder="Name">
        <input id="m_price" class="form-control mb-2" type="number" placeholder="Price">
        <input id="m_qty" class="form-control mb-2" type="number" value="1">
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" onclick="addManual()">Add</button>
      </div>
    </div>
  </div>
</div>

<!-- PAYMENT SUCCESS MODAL -->
<div class="modal fade" id="paymentToast" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Payment Done</h5>
        <button type="button" class="btn-close btn-close-white" onclick="closePaymentToast()"></button>
      </div>

      <div class="modal-body text-center">
        <h6>Total</h6>
        <h4>₱<span id="toast_total"></span></h4>

        <h6 class="mt-3">Tendered</h6>
        <h4>₱<span id="toast_cash"></span></h4>

        <h6 class="mt-3">Change</h6>
        <h2 class="text-success">₱<span id="toast_change"></span></h2>

        <hr>
        <strong class="text-success">✔ Payment Completed</strong>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success w-100" onclick="closePaymentToast()">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let cart = [];
let scannerInstance = null;

/* ===== BEEP SOUND ===== */
function beep() {
  const ctx = new (window.AudioContext || window.webkitAudioContext)();
  const osc = ctx.createOscillator();
  const gain = ctx.createGain();
  osc.frequency.value = 800;
  gain.gain.value = 0.2;
  osc.connect(gain);
  gain.connect(ctx.destination);
  osc.start();
  osc.stop(ctx.currentTime + 0.12);
}

/* ADD TO CART */
function addToCart(item) {
  const existing = cart.find(i => i.id === item.id);
  if (existing) existing.qty += item.qty;
  else cart.push(item);
  render();
}

/* MANUAL ADD (STRICT) */
function addManual() {
  const nameRaw = m_name.value.trim();
  const priceRaw = m_price.value.trim();
  const qtyRaw = m_qty.value.trim();

  const price = parseFloat(priceRaw);
  const qty = parseInt(qtyRaw);

  if (
    nameRaw === "" ||
    priceRaw === "" || isNaN(price) || price <= 0 ||
    qtyRaw === "" || isNaN(qty) || qty <= 0
  ) {
    alert("Fill out all item fields correctly.");
    return;
  }

  addToCart({
    id: "manual-" + nameRaw.toLowerCase(),
    name: nameRaw,
    price: price,
    qty: qty
  });

  beep();

  m_name.value = "";
  m_price.value = "";
  m_qty.value = 1;

  bootstrap.Modal.getInstance(addModal).hide();
}

/* RENDER CART */
function render() {
  let html = "", total = 0;

  cart.forEach((i, index) => {
    const price = isNaN(i.price) ? 0 : i.price;
    const qty = isNaN(i.qty) ? 0 : i.qty;
    const sub = price * qty;
    total += sub;

    html += `
      <tr>
        <td>${i.name}</td>
        <td>
          <input type="number" min="1" value="${qty}"
            class="form-control form-control-sm"
            onchange="cart[${index}].qty=parseInt(this.value)||1;render()">
        </td>
        <td>₱${price.toFixed(2)}</td>
        <td>₱${sub.toFixed(2)}</td>
        <td>
          <button class="btn btn-danger btn-sm"
            onclick="cart.splice(${index},1);render()">X</button>
        </td>
      </tr>`;
  });

  cartEl = document.getElementById("cart");
  cartEl.innerHTML = html;
  totalEl = document.getElementById("total");
  totalEl.innerText = total.toFixed(2);
}

/* BARCODE SCANNER */
function startScanner() {
  if (scannerInstance) scannerInstance.stop();

  scannerInstance = new Html5Qrcode("reader");
  scannerInstance.start(
    { facingMode: "environment" },
    {
      fps: 10,
      formatsToSupport: [
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.CODE_128
      ]
    },
    code => {
      scannerInstance.stop();
      fetch("scan.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "barcode=" + encodeURIComponent(code)
      })
      .then(r => r.json())
      .then(d => {
        if (!d.success) return;
        addToCart({
          id: d.product.id,
          name: d.product.name,
          price: parseFloat(d.product.price),
          qty: 1
        });
        beep();
      });
    }
  );
}

/* PAY */
function pay() {
  const cashRaw = cash.value.trim();
  const cashVal = parseFloat(cashRaw);

  if (cart.length === 0) {
    alert("Cart is empty.");
    return;
  }

  if (cashRaw === "" || isNaN(cashVal) || cashVal <= 0) {
    alert("Enter valid cash amount.");
    return;
  }

  let total = 0;
  cart.forEach(i => total += i.price * i.qty);

  if (cashVal < total) {
    alert("Insufficient cash.");
    return;
  }

  fetch("pay.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cart, cash: cashVal })
  })
  .then(r => r.json())
  .then(res => {
    if (!res.success) return;

    toast_total.innerText = res.total;
    toast_cash.innerText = res.cash;
    toast_change.innerText = res.change;

    new bootstrap.Modal(paymentToast, {
      backdrop: "static",
      keyboard: false
    }).show();
  });
}

/* CLOSE + RESET */
function closePaymentToast() {
  cart = [];
  render();
  cash.value = "";

  const modal = bootstrap.Modal.getInstance(paymentToast);
  if (modal) modal.hide();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
