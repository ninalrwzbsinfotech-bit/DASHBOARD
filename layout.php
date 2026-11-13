<?php
$conn = new mysqli("127.0.0.1", "root", "", "qc_wastemanagement", 3307);
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }
mysqli_report(MYSQLI_REPORT_OFF);

function h($v) { return htmlspecialchars((string)$v); }

function page_start($title, $active) {
  $links = [
    "home.php" => "Dashboard",
    "wastecollection.php" => "Waste Collection",
    "binmanagement.php" => "Bin Management",
    "zones.php" => "Zone &amp; Area",
    "vehiclemanagement.php" => "Vehicle Management",
  ];
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo h($title); ?> - Quezon City Waste Management</title>
<style>
  * { box-sizing: border-box; }
  body { margin: 0; display: flex; font-family: Arial, sans-serif; background: #f5f5f5; color: #111; }
  .sidebar { width: 250px; min-height: 100vh; background: #fff; border-right: 1px solid #ddd; padding: 16px; }
  .sidebar h1 { font-size: 20px; margin: 0 0 16px; }
  nav ul { list-style: none; margin: 0; padding: 0; }
  nav a { text-decoration: none; color: #111; }
  nav li { display: flex; justify-content: space-between; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; }
  nav li:hover, nav li.active { background: #eee; }
  nav li.active { font-weight: bold; }
  .main { flex: 1; }
  .topbar { height: 56px; background: #fff; border-bottom: 1px solid #ddd; display: flex; justify-content: flex-end; align-items: center; padding: 0 24px; font-size: 14px; }
  .content { padding: 24px; }
  .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
  .header h2 { margin: 0; font-size: 28px; }
  button, .btn { background: #222; color: #fff; border: 0; border-radius: 8px; padding: 12px 18px; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-block; }
  .cards { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }
  .card { flex: 1; min-width: 200px; background: #fff; border: 1px solid #ddd; border-radius: 14px; padding: 20px; color: #111; text-decoration: none; display: block; }
  a.card:hover { background: #eee; }
  .card.selected { border: 2px solid #222; }
  .card .title { font-size: 15px; font-weight: 500; }
  .card .value { font-size: 28px; font-weight: bold; margin: 12px 0; }
  .card .value small { font-size: 12px; font-weight: normal; color: #777; }
  .card .sub { color: #777; font-size: 14px; }
  .toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin: 20px 0 0; }
  .toolbar input, .toolbar select { border: 1px solid #ddd; border-radius: 8px; padding: 10px; font-size: 14px; background: #fff; }
  .pill { border: 1px solid #ddd; border-radius: 20px; padding: 8px 14px; font-size: 14px; color: #111; text-decoration: none; background: #fff; }
  .pill:hover, .pill.active { background: #222; color: #fff; }
  table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 16px; font-size: 14px; }
  th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
  th { background: #eee; }
  table.sortable th { cursor: pointer; user-select: none; }
  table.sortable th:hover { background: #ddd; }
  tbody tr:hover { background: #f5f5f5; }
  td a { color: #111; }
  h3 { margin: 28px 0 0; }
  .bar { background: #eee; border: 1px solid #ccc; border-radius: 6px; height: 12px; width: 100px; display: inline-block; vertical-align: middle; }
  .bar span { display: block; height: 100%; background: #555; border-radius: 5px; }
  .muted { color: #777; }
</style>
</head>
<body>
  <div class="sidebar">
    <h1>Waste Management</h1>
    <nav>
      <ul>
        <?php foreach ($links as $file => $label) { ?>
        <a href="<?php echo $file; ?>"><li<?php echo $file === $active ? ' class="active"' : ''; ?>><?php echo $label; ?><?php echo $file === "home.php" ? "" : " <span>›</span>"; ?></li></a>
        <?php } ?>
      </ul>
    </nav>
  </div>
  <div class="main">
    <div class="topbar">Super Admin</div>
    <div class="content">
<?php
}

function page_end() {
  ?>
    </div>
  </div>
<script>
  // Live search: <input data-filter="tableId">
  document.querySelectorAll("input[data-filter]").forEach(function (input) {
    input.addEventListener("input", function () {
      var q = input.value.toLowerCase();
      document.querySelectorAll("#" + input.dataset.filter + " tbody tr").forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
      });
    });
  });

  // Click a column header to sort: <table class="sortable">
  document.querySelectorAll("table.sortable").forEach(function (table) {
    table.querySelectorAll("th").forEach(function (th, i) {
      var asc = true;
      th.addEventListener("click", function () {
        var tbody = table.tBodies[0];
        var rows = Array.from(tbody.rows);
        rows.sort(function (a, b) {
          var x = a.cells[i].textContent.trim(), y = b.cells[i].textContent.trim();
          var nx = parseFloat(x), ny = parseFloat(y);
          var cmp = (!isNaN(nx) && !isNaN(ny)) ? nx - ny : x.localeCompare(y);
          return asc ? cmp : -cmp;
        });
        rows.forEach(function (r) { tbody.appendChild(r); });
        asc = !asc;
      });
    });
  });
</script>
</body>
</html>
<?php
}