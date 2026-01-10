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

<nav class="navbar bg-primary text-white px-3 d-flex justify-content-between">
  <span class="fw-semibold">Dolores POS</span>
  <div>
    <a href="admin/products.php" class="btn btn-warning btn-sm me-2">Admin</a>
    <a href="reports/analytics.php" class="btn btn-success btn-sm me-2">Reports</a>
    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container-fluid mt-3">
<div class="row">

<div class="col-md-4 mb-3">
  <button class="btn btn-primary w-100 mb-2" onclick="startScanner()">📷 Scan Barcode</button>
  <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add Item</button>
  <div id="reader" class="mt-2"></div>
</div>

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

<!-- PAYMENT MODAL -->
<div class="modal fade" id="paymentToast">
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
let scanLock = false;

const cartEl = document.getElementById("cart");

/* BEEP */
function beep() {
  const ctx = new (window.AudioContext || window.webkitAudioContext)();
  const osc = ctx.createOscillator();
  osc.frequency.value = 800;
  osc.connect(ctx.destination);
  osc.start();
  osc.stop(ctx.currentTime + 0.1);
}

/* ADD TO CART */
function addToCart(item) {
  const existing = cart.find(i => i.id === item.id);
  if (existing) existing.qty += item.qty;
  else cart.push(item);
  render();
}

/* MANUAL ADD */
function addManual() {
  const name = m_name.value.trim();
  const price = parseFloat(m_price.value);
  const qty = parseInt(m_qty.value);

  if (!name || isNaN(price) || price <= 0 || isNaN(qty) || qty <= 0) {
    alert("Fill out all item fields correctly.");
    return;
  }

  addToCart({ id: "manual-" + name.toLowerCase(), name, price, qty });
  beep();

  m_name.value = "";
  m_price.value = "";
  m_qty.value = 1;
  bootstrap.Modal.getInstance(addModal).hide();
}

/* RENDER */
function render() {
  let html = "";
  let total = 0;

  cart.forEach((i, index) => {
    const sub = i.price * i.qty;
    total += sub;

    html += `
      <tr>
        <td>${i.name}</td>
        <td>
          <input type="number" min="1" value="${i.qty}"
            class="form-control form-control-sm"
            onchange="updateQty(${index}, this.value)">
        </td>
        <td>₱${i.price.toFixed(2)}</td>
        <td>₱${sub.toFixed(2)}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="removeItem(${index})">X</button>
        </td>
      </tr>`;
  });

  cartEl.innerHTML = html;
  document.getElementById("total").innerText = total.toFixed(2);
}

function updateQty(index, value) {
  const qty = parseInt(value);
  if (qty > 0) {
    cart[index].qty = qty;
    render();
  }
}

function removeItem(index) {
  cart.splice(index, 1);
  render();
}

/* SCANNER (RELIABLE CONFIG) */
function startScanner() {
  if (scannerInstance) scannerInstance.stop().catch(()=>{});

  scannerInstance = new Html5Qrcode("reader");

  scannerInstance.start(
    { facingMode: "environment" },
    {
      fps: 15,
      qrbox: { width: 300, height: 150 },
      formatsToSupport: [
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.CODE_128
      ]
    },
    (code) => {
      if (scanLock) return;
      scanLock = true;

      fetch("scan.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: "barcode=" + encodeURIComponent(code)
      })
      .then(r => r.json())
      .then(d => {
        if (!d.success) {
          alert("Item not found");
          return;
        }

        addToCart({
          id: d.product.id,
          name: d.product.name,
          price: parseFloat(d.product.price),
          qty: 1
        });

        beep();
      })
      .finally(() => {
        setTimeout(() => {
          scanLock = false;
        }, 600);
      });
    }
  );
}

/* PAY */
function pay() {
  const cashVal = parseFloat(cash.value);
  if (!cart.length) return alert("Cart empty");
  if (isNaN(cashVal)) return alert("Invalid cash");

  let total = cart.reduce((s,i)=>s+i.price*i.qty,0);
  if (cashVal < total) return alert("Insufficient cash");

  fetch("pay.php", {
    method:"POST",
    headers:{ "Content-Type":"application/json" },
    body: JSON.stringify({ cart, cash: cashVal })
  })
  .then(r=>r.json())
  .then(res=>{
    if(!res.success) return;
    toast_total.innerText=res.total;
    toast_cash.innerText=res.cash;
    toast_change.innerText=res.change;
    new bootstrap.Modal(paymentToast,{backdrop:"static"}).show();
  });
}

function closePaymentToast() {
  cart=[];
  render();
  cash.value="";
  bootstrap.Modal.getInstance(paymentToast).hide();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
