<?php
require_once "config.php";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $phone    = $_POST['phone'] ?? '';
  $city     = $_POST['city'] ?? '';
  $cuisArr  = $_POST['cuisines'] ?? [];
  $gender   = $_POST['gender'] ?? '';
  $terms    = isset($_POST['terms']);

  // Server-side re-check (never trust the browser only)
  if (!preg_match('/^[A-Za-z]{1,30}$/', $username))       $error = "Invalid username.";
  elseif (!preg_match('/^[A-Za-z0-9]{1,8}$/', $password)) $error = "Invalid password.";
  elseif (!preg_match('/^[0-9]{10}$/', $phone))           $error = "Invalid phone number.";
  elseif ($city === '' || !$cuisArr || !in_array($gender, ['Male','Female','Other'], true) || !$terms)
                                                          $error = "Please complete all fields.";
  else {
    $cuisines = implode(", ", array_map('strip_tags', $cuisArr));
    // Existing user with right password -> log in
    $st = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $st->bind_param("s", $username);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if ($row) {
      if (password_verify($password, $row['password'])) {
        $_SESSION['uid'] = $row['id']; $_SESSION['name'] = $username;
        header("Location: menu.php"); exit;
      } else $error = "Username already exists. Wrong password.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $st = $conn->prepare("INSERT INTO users (username,password,phone,city,cuisines,gender) VALUES (?,?,?,?,?,?)");
      $st->bind_param("ssssss", $username, $hash, $phone, $city, $cuisines, $gender);
      $st->execute();
      $_SESSION['uid'] = $conn->insert_id; $_SESSION['name'] = $username;
      header("Location: menu.php"); exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in - Tiffin Express</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>Tiffin Express</h1></header>
<main><div class="card" style="max-width:480px;margin:auto">
<h2>Sign in to order</h2>
<?php if ($error) echo "<div class='msg'>".e($error)."</div>"; ?>
<form id="signForm" method="post" novalidate>
  <label for="username">Username</label>
  <input type="text" id="username" name="username" maxlength="30" autocomplete="username">
  <div class="err" id="e_username"></div>

  <label for="password">Password</label>
  <div class="pw">
    <input type="password" id="password" name="password" maxlength="8" autocomplete="current-password">
    <button type="button" id="toggle" class="grey">Show</button>
  </div>
  <div class="err" id="e_password"></div>

  <label for="phone">Phone number</label>
  <input type="text" id="phone" name="phone" maxlength="10" inputmode="numeric">
  <div class="err" id="e_phone"></div>

  <label for="city">City</label>
  <select id="city" name="city">
    <option value="">-- Select city --</option>
    <option>Chennai</option><option>Bengaluru</option><option>Hyderabad</option><option>Mumbai</option><option>Delhi</option>
  </select>
  <div class="err" id="e_city"></div>

  <label for="cuisines">Favourite cuisines (hold Ctrl to pick more)</label>
  <select id="cuisines" name="cuisines[]" multiple>
    <option>South Indian</option><option>North Indian</option><option>Chinese</option><option>Italian</option><option>Fast Food</option>
  </select>
  <div class="err" id="e_cuisines"></div>

  <label>Gender</label>
  <div class="inline">
    <label><input type="radio" name="gender" value="Male"> Male</label>
    <label><input type="radio" name="gender" value="Female"> Female</label>
    <label><input type="radio" name="gender" value="Other"> Other</label>
  </div>
  <div class="err" id="e_gender"></div>

  <label class="inline" style="margin-top:14px"><input type="checkbox" id="terms" name="terms"> I agree to the terms and delivery policy</label>
  <div class="err" id="e_terms"></div>

  <button type="submit">Continue to menu</button>
</form></div></main>
<script src="script.js"></script>
</body></html>
