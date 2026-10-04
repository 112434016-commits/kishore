<?php require_once "config.php"; require_login(); ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Menu - Tiffin Express</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>Tiffin Express</h1>
<nav>Hi, <?= e($_SESSION['name']) ?><a href="menu.php">Menu</a><a href="myorders.php">My orders</a><a href="logout.php">Log out</a></nav></header>
<main>
<h2>Today's menu</h2>
<p>Tap a picture to add it to your order.</p>
<div class="grid">
<?php foreach ($FOODS as $id => [$name, $price, $emoji, $avail]): ?>
  <div class="food <?= $avail ? '' : 'out' ?>" id="f<?= $id ?>" data-id="<?= $id ?>" data-price="<?= $price ?>" data-name="<?= e($name) ?>" data-avail="<?= $avail ? 1 : 0 ?>">
    <img src="<?= food_img($emoji) ?>" alt="<?= e($name) ?>" onclick="toggleItem(<?= $id ?>)">
    <h3><?= e($name) ?></h3>
    <div>₹<?= $price ?></div>
    <span class="tag <?= $avail ? 'in' : 'no' ?>"><?= $avail ? 'Available' : 'Sold out' ?></span>
    <div class="qty"><button type="button" onclick="chg(<?= $id ?>,-1)">-</button><b id="q<?= $id ?>">1</b><button type="button" onclick="chg(<?= $id ?>,1)">+</button></div>
  </div>
<?php endforeach; ?>
</div>

<form class="bill" method="post" action="place_order.php" id="orderForm">
  <div>Total: ₹<span id="total">0</span> <small id="count"></small></div>
  <input type="hidden" name="cart" id="cart">
  <button type="submit" class="alt" id="orderBtn" disabled>Place order</button>
</form>
</main>
<script>
const cart = {};   // id -> qty
function toggleItem(id){
  const el = document.getElementById('f'+id);
  if (el.dataset.avail !== '1') return;
  if (cart[id]) { delete cart[id]; el.classList.remove('sel'); }
  else { cart[id] = 1; el.classList.add('sel'); document.getElementById('q'+id).textContent = 1; }
  render();
}
function chg(id,d){
  cart[id] = Math.min(10, Math.max(1, cart[id] + d));
  document.getElementById('q'+id).textContent = cart[id];
  render();
}
function render(){
  let total = 0, n = 0;
  for (const id in cart){ total += cart[id] * +document.getElementById('f'+id).dataset.price; n += cart[id]; }
  document.getElementById('total').textContent = total;
  document.getElementById('count').textContent = n ? '(' + n + ' items)' : '';
  document.getElementById('cart').value = JSON.stringify(cart);
  document.getElementById('orderBtn').disabled = n === 0;
}
</script>
</body></html>
