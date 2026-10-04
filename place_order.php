<?php
require_once "config.php"; require_login();
$cart = json_decode($_POST['cart'] ?? '{}', true) ?: [];
$items = []; $total = 0;
foreach ($cart as $id => $qty) {          // recompute on server, ignore browser prices
  $id = (int)$id; $qty = max(1, min(10, (int)$qty));
  if (isset($FOODS[$id]) && $FOODS[$id][3]) {
    [$name, $price] = $FOODS[$id];
    $items[] = ["name" => $name, "qty" => $qty, "price" => $price];
    $total += $price * $qty;
  }
}
if (!$items) { header("Location: menu.php"); exit; }
$json = json_encode($items);
$st = $conn->prepare("INSERT INTO orders (user_id, items, total) VALUES (?,?,?)");
$st->bind_param("isd", $_SESSION['uid'], $json, $total);
$st->execute();
header("Location: track.php?id=" . $conn->insert_id);
