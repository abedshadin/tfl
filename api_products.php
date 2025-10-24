<?php include 'auth_check.php'; ?>
<?php include 'ip_blocker.php'; ?>


<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

// ===== CONFIGURE if your schema differs =====
$table      = 'products';     // your products table
$col_id     = 'id';           // primary key column
$col_brand  = 'brand';        // brand column
$col_name   = 'name';         // product name column
// ===========================================

// Helpers
function json_out($arr){ echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit; }

$method = $_SERVER['REQUEST_METHOD'];

// GET: list products by brand (and optional search q)
if ($method === 'GET') {
  $brand = $_GET['brand'] ?? '';
  $q = trim($_GET['q'] ?? '');

  if ($brand === '') {
    http_response_code(400);
    json_out(['ok'=>false, 'error'=>'Missing brand']);
  }

  if ($q !== '') {
    $stmt = $pdo->prepare("SELECT {$col_id} AS id, {$col_name} AS name
                           FROM {$table}
                           WHERE {$col_brand} = ? AND {$col_name} LIKE ?
                           ORDER BY {$col_name} ASC");
    $stmt->execute([$brand, '%' . $q . '%']);
  } else {
    $stmt = $pdo->prepare("SELECT {$col_id} AS id, {$col_name} AS name
                           FROM {$table}
                           WHERE {$col_brand} = ?
                           ORDER BY {$col_name} ASC");
    $stmt->execute([$brand]);
  }
  $rows = $stmt->fetchAll();
  json_out(['ok'=>true, 'brand'=>$brand, 'count'=>count($rows), 'products'=>$rows]);
}

// POST: add new product (brand + name). If exists, return existing id.
if ($method === 'POST') {
  $input = $_POST;
  $brand = trim($input['brand'] ?? '');
  $name  = trim($input['name'] ?? '');

  if ($brand === '' || $name === '') {
    http_response_code(400);
    json_out(['ok'=>false, 'error'=>'Missing brand or name']);
  }

  // check exists
  $chk = $pdo->prepare("SELECT {$col_id} AS id FROM {$table} WHERE {$col_brand}=? AND {$col_name}=?");
  $chk->execute([$brand, $name]);
  $row = $chk->fetch();
  if ($row) {
    json_out(['ok'=>true, 'id'=>(int)$row['id'], 'created'=>false]);
  }

  // insert
  $ins = $pdo->prepare("INSERT INTO {$table} ({$col_brand}, {$col_name}) VALUES (?, ?)");
  $ins->execute([$brand, $name]);
  $id = (int)$pdo->lastInsertId();
  json_out(['ok'=>true, 'id'=>$id, 'created'=>true]);
}

http_response_code(405);
json_out(['ok'=>false, 'error'=>'Method not allowed']);
