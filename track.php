<?php
require_once "config.php"; require_login();
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT *, TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age FROM orders WHERE id=? AND user_id=?");
$st->bind_param("ii", $id, $_SESSION['uid']);
$st->execute();
$o = $st->get_result()->fetch_assoc();
if (!$o) { header("Location: myorders.php"); exit; }

// Demo tracking: stage changes every 60 seconds
$stages = ["Order placed", "Preparing", "Out for delivery", "Delivered"];
$cancelled = $o['status'] === 'Cancelled';
$cur = min(3, intdiv((int)$o['age'], 60));
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Track order #<?= $id ?></title><link rel="stylesheet" href="style.css">
<?php if (!$cancelled && $cur < 3) echo '<meta http-equiv="refresh" content="15">'; ?></head>
<body>
<header><h1>Tiffin Express</h1>
<nav><a href="menu.php">Menu</a><a href="myorders.php">My orders</a><a href="logout.php">Log out</a></nav></header>
<main><div class="card">
<h2>Order #<?= $id ?> tracking</h2>
<?php if ($cancelled): ?>
  <div class="steps"><div class="step cancel">Order cancelled</div></div>
<?php else: ?>
  <div class="steps">
  <?php foreach ($stages as $i => $s) echo "<div class='step ".($i <= $cur ? 'done' : '')."'>".e($s)."</div>"; ?>
  </div>
  <p><?= $cur < 3 ? "Page refreshes every 15 seconds." : "Your food has arrived. Enjoy!" ?></p>
<?php endif; ?>
<table><tr><th>Item</th><th>Qty</th><th>Price</th></tr>
<?php foreach (json_decode($o['items'], true) as $it)
  echo "<tr><td>".e($it['name'])."</td><td>{$it['qty']}</td><td>₹".($it['price'] * $it['qty'])."</td></tr>"; ?>
<tr><th colspan="2">Total</th><th>₹<?= $o['total'] ?></th></tr></table>
<p><a class="btn" href="myorders.php">View my orders</a></p>
</div></main></body></html>
