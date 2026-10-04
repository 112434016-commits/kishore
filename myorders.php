<?php
require_once "config.php"; require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
  $oid = (int)$_POST['cancel'];
  // Only cancel while still being prepared (first 2 minutes) and not already cancelled
  $st = $conn->prepare("UPDATE orders SET status='Cancelled' WHERE id=? AND user_id=? AND status='Active' AND TIMESTAMPDIFF(SECOND, created_at, NOW()) < 120");
  $st->bind_param("ii", $oid, $_SESSION['uid']);
  $st->execute();
  header("Location: myorders.php"); exit;
}
$st = $conn->prepare("SELECT *, TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age FROM orders WHERE user_id=? ORDER BY id DESC");
$st->bind_param("i", $_SESSION['uid']);
$st->execute();
$rows = $st->get_result();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My orders</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>Tiffin Express</h1>
<nav><a href="menu.php">Menu</a><a href="myorders.php">My orders</a><a href="logout.php">Log out</a></nav></header>
<main><h2>My orders</h2>
<?php if ($rows->num_rows === 0): ?>
  <div class="card">No orders yet. <a href="menu.php">Pick something from the menu.</a></div>
<?php else: ?>
<table><tr><th>#</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th></tr>
<?php while ($o = $rows->fetch_assoc()):
  $names = array_map(fn($i) => $i['qty'] . " x " . $i['name'], json_decode($o['items'], true));
  $cancelled = $o['status'] === 'Cancelled';
  $age = (int)$o['age'];
  $label = $cancelled ? "Cancelled" : ($age < 60 ? "Order placed" : ($age < 120 ? "Preparing" : ($age < 180 ? "Out for delivery" : "Delivered")));
?>
<tr>
  <td><?= $o['id'] ?></td><td><?= e(implode(", ", $names)) ?></td><td>₹<?= $o['total'] ?></td><td><?= $label ?></td>
  <td>
    <a class="btn alt" href="track.php?id=<?= $o['id'] ?>">View</a>
    <?php if (!$cancelled && $age < 120): ?>
      <form method="post" style="display:inline" onsubmit="return confirm('Cancel this order?')">
        <input type="hidden" name="cancel" value="<?= $o['id'] ?>"><button type="submit">Cancel</button>
      </form>
    <?php endif; ?>
  </td>
</tr>
<?php endwhile; ?></table>
<?php endif; ?>
</main></body></html>
