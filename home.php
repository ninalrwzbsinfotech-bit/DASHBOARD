<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "qc_wastemanagement";

$conn = new mysqli("127.0.0.1", "root", "", "qc_wastemanagement", 3307);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_OFF);

// Initialize form variables
$isEdit = false;
$record_id = "";
$record_date = date('Y-m-d');
$barangay = "";
$qc_district = "District 1";
$bin_capacity_kg = 100;
$trash_fill_level_pct = 0;
$collected_waste_kg = 0;
$bin_status = "Normal";
$priority_level = "Low";
$optimizer_action = "No Action";
$assigned_vehicle = "";

// 1. Handle Edit Request
if (isset($_GET["edit"])) {
    $record_id = $_GET["edit"];
    $result = $conn->query("SELECT * FROM waste_records WHERE record_id = '$record_id'");
    if ($row = $result->fetch_assoc()) {
        $isEdit = true;
        $record_id = $row["record_id"];
        $record_date = $row["record_date"];
        $barangay = $row["barangay"];
        $qc_district = $row["qc_district"];
        $bin_capacity_kg = $row["bin_capacity_kg"];
        $trash_fill_level_pct = $row["trash_fill_level_pct"];
        $collected_waste_kg = $row["collected_waste_kg"];
        $bin_status = $row["bin_status"];
        $priority_level = $row["priority_level"];
        $optimizer_action = $row["optimizer_action"];
        $assigned_vehicle = $row["assigned_vehicle"];
    }
}

// 2. Handle Add Record
if (isset($_POST["btnAdd"])) {
    $conn->query("INSERT INTO waste_records (record_date, barangay, qc_district, bin_capacity_kg, trash_fill_level_pct, collected_waste_kg, bin_status, priority_level, optimizer_action, assigned_vehicle) 
                  VALUES ('$_POST[record_date]', '$_POST[barangay]', '$_POST[qc_district]', '$_POST[bin_capacity_kg]', '$_POST[trash_fill_level_pct]', '$_POST[collected_waste_kg]', '$_POST[bin_status]', '$_POST[priority_level]', '$_POST[optimizer_action]', '$_POST[assigned_vehicle]')");
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

// 3. Handle Update Record
if (isset($_POST["btnUpdate"])) {
    $conn->query("UPDATE waste_records SET record_date='$_POST[record_date]', barangay='$_POST[barangay]', qc_district='$_POST[qc_district]', bin_capacity_kg='$_POST[bin_capacity_kg]', trash_fill_level_pct='$_POST[trash_fill_level_pct]', collected_waste_kg='$_POST[collected_waste_kg]', bin_status='$_POST[bin_status]', priority_level='$_POST[priority_level]', optimizer_action='$_POST[optimizer_action]', assigned_vehicle='$_POST[assigned_vehicle]' WHERE record_id='$_POST[record_id]'");
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

// 4. Handle Delete Record
if (isset($_POST["btnDelete"])) {
    $conn->query("DELETE FROM waste_records WHERE record_id = '$_POST[record_id]'");
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

// Dashboard Statistics Queries
$wasteCollected = 0;
$activeVehicles = 0;
$totalVehicles = 20;
$activeBins = 0;
$qc_districts = 0;
$barangays = 0;

$result = $conn->query("SELECT SUM(collected_waste_kg) AS total FROM waste_records");
if ($result) { $row = $result->fetch_assoc(); $wasteCollected = $row["total"] ?? 0; }

$result = $conn->query("SELECT COUNT(DISTINCT assigned_vehicle) AS total FROM waste_records WHERE assigned_vehicle <> ''");
if ($result) { $row = $result->fetch_assoc(); $activeVehicles = $row["total"] ?? 0; }

$result = $conn->query("SELECT COUNT(*) AS total FROM waste_records WHERE optimizer_action = 'Scheduled Pickup'");
if ($result) { $row = $result->fetch_assoc(); $activeBins = $row["total"] ?? 0; }

$result = $conn->query("SELECT COUNT(DISTINCT qc_district) AS total FROM waste_records WHERE qc_district <> ''");
if ($result) { $row = $result->fetch_assoc(); $qc_districts = $row["total"] ?? 0; }

$result = $conn->query("SELECT COUNT(DISTINCT barangay) AS total FROM waste_records WHERE barangay <> ''");
if ($result) { $row = $result->fetch_assoc(); $barangays = $row["total"] ?? 0; }

$records = $conn->query("SELECT * FROM waste_records ORDER BY record_id LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quezon City Waste Management - Dashboard</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; display: flex; font-family: Arial, sans-serif; background: #f5f5f5; color: #111; }
    .sidebar { width: 250px; min-height: 100vh; background: #fff; border-right: 1px solid #ddd; padding: 16px; }
    .sidebar h1 { font-size: 20px; margin: 0 0 16px; }
    nav ul { list-style: none; margin: 0; padding: 0; }
    nav a { text-decoration: none; color: #111; }
    nav li { display: flex; justify-content: space-between; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; }
    nav li:hover { background: #eee; }
    .main { flex: 1; }
    .topbar { height: 56px; background: #fff; border-bottom: 1px solid #ddd; display: flex; justify-content: flex-end; align-items: center; padding: 0 24px; font-size: 14px; }
    .content { padding: 24px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .header h2 { margin: 0; font-size: 28px; }
    button { background: #222; color: #fff; border: 0; border-radius: 8px; padding: 10px 16px; font-size: 14px; cursor: pointer; }
    .cards { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
    .card { flex: 1; min-width: 200px; background: #fff; border: 1px solid #ddd; border-radius: 14px; padding: 20px; }
    .card .title { font-size: 15px; font-weight: 500; }
    .card .value { font-size: 28px; font-weight: bold; margin: 12px 0; }
    .card .value small { font-size: 12px; font-weight: normal; color: #777; }
    .card a { color: #777; font-size: 14px; text-decoration: underline; }
    
    .form-container { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 12px; margin-bottom: 24px; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
    .form-group label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px; }
    .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; }

    table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 12px; font-size: 14px; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
    th { background: #eee; }
    .btn-edit { display: inline-block; padding: 6px 12px; font-size: 13px; background: #007bff; color: #fff; text-decoration: none; border-radius: 6px; margin-right: 4px; }
    .btn-delete { background: #dc3545; color: #fff; border: none; border-radius: 6px; padding: 6px 12px; font-size: 13px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h1>Waste Management</h1>
    <nav>
      <ul>
        <a href="home.php"><li style="background:#eee; font-weight:bold;">Dashboard</li></a>
        <a href="wastecollection.php"><li>Waste Collection <span>›</span></li></a>
        <a href="bincollection.php"><li>Bin Management <span>›</span></li></a>
        <a href="zone&area.php"><li>Zone &amp; Area <span>›</span></li></a>
        <a href="vehiclemanagement.php"><li>Vehicle Management <span>›</span></li></a>
      </ul>
    </nav>
  </div>

  <div class="main">
    <div class="topbar">Admin</div>

    <div class="content">
      <div class="header">
        <h2>Quezon City Waste Management - Dashboard</h2>
      </div>

      <div class="cards">
        <div class="card">
          <div class="title">Waste Collected</div>
          <div class="value"><?php echo $wasteCollected; ?> <small>KG</small></div>
          <a href="wastecollection.php">view records ↗</a>
        </div>
        <div class="card">
          <div class="title">Vehicles Active</div>
          <div class="value"><?php echo $activeVehicles; ?> <small>out of <?php echo $totalVehicles; ?></small></div>
          <a href="vehiclemanagement.php">view vehicles ↗</a>
        </div>
        <div class="card">
          <div class="title">Active Bins</div>
          <div class="value"><?php echo $activeBins; ?></div>
          <a href="bincollection.php">view bins ↗</a>
        </div>
        <div class="card">
          <div class="title">District</div>
          <div class="value"><?php echo $qc_districts; ?> Districts, <?php echo $barangays; ?> Barangays</div>
          <a href="zone&area.php">view districts and barangays ↗</a>
        </div>
      </div>

      <div class="form-container">
        <h3><?php echo $isEdit ? "Edit Record #" . $record_id : "Add New Waste Record"; ?></h3>
        <form method="POST">
          <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">
          
          <div class="form-grid">
            <div class="form-group">
              <label>Date</label>
              <input type="date" name="record_date" value="<?php echo $record_date; ?>" required>
            </div>
            <div class="form-group">
              <label>Barangay</label>
              <input type="text" name="barangay" value="<?php echo $barangay; ?>" required>
            </div>
            <div class="form-group">
              <label>District</label>
              <select name="qc_district">
                <?php 
                  $districts = ["District 1", "District 2", "District 3", "District 4", "District 5", "District 6"];
                  foreach ($districts as $d) {
                      $sel = ($qc_district === $d) ? 'selected' : '';
                      echo "<option value=\"$d\" $sel>$d</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Capacity (kg)</label>
              <input type="number" step="0.01" name="bin_capacity_kg" value="<?php echo $bin_capacity_kg; ?>" required>
            </div>
            <div class="form-group">
              <label>Fill (%)</label>
              <input type="number" step="0.1" name="trash_fill_level_pct" value="<?php echo $trash_fill_level_pct; ?>" required>
            </div>
            <div class="form-group">
              <label>Collected (kg)</label>
              <input type="number" step="0.01" name="collected_waste_kg" value="<?php echo $collected_waste_kg; ?>" required>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="bin_status">
                <?php 
                  $statuses = ["Normal", "Full", "Overflowing"];
                  foreach ($statuses as $s) {
                      $sel = ($bin_status === $s) ? 'selected' : '';
                      echo "<option value=\"$s\" $sel>$s</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Priority</label>
              <select name="priority_level">
                <?php 
                  $priorities = ["Low", "Medium", "High"];
                  foreach ($priorities as $p) {
                      $sel = ($priority_level === $p) ? 'selected' : '';
                      echo "<option value=\"$p\" $sel>$sel</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Action</label>
              <select name="optimizer_action">
                <?php 
                  $actions = ["No Action", "Scheduled Pickup"];
                  foreach ($actions as $a) {
                      $sel = ($optimizer_action === $a) ? 'selected' : '';
                      echo "<option value=\"$a\" $sel>$a</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Vehicle</label>
              <input type="text" name="assigned_vehicle" value="<?php echo $assigned_vehicle; ?>" placeholder="e.g. Truck 1">
            </div>
          </div>

          <div style="margin-top: 16px;">
            <?php if ($isEdit) { ?>
                <button type="submit" name="btnUpdate" style="background:#007bff;">Update Record</button>
                <a href="home.php" style="margin-left: 10px; text-decoration: none; color: #555;">Cancel</a>
            <?php } else { ?>
                <button type="submit" name="btnAdd">Add Record</button>
            <?php } ?>
          </div>
        </form>
      </div>

      <table>
        <tr>
          <th>Record ID</th>
          <th>Date</th>
          <th>Barangay</th>
          <th>District</th>
          <th>Capacity (kg)</th>
          <th>Fill (%)</th>
          <th>Collected (kg)</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Action</th>
          <th>Vehicle</th>
          <th>Manage</th>
        </tr>
        <?php if ($records) { while ($r = $records->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $r["record_id"]; ?></td>
          <td><?php echo $r["record_date"]; ?></td>
          <td><?php echo $r["barangay"]; ?></td>
          <td><?php echo $r["qc_district"]; ?></td>
          <td><?php echo $r["bin_capacity_kg"]; ?></td>
          <td><?php echo $r["trash_fill_level_pct"]; ?></td>
          <td><?php echo $r["collected_waste_kg"]; ?></td>
          <td><?php echo $r["bin_status"]; ?></td>
          <td><?php echo $r["priority_level"]; ?></td>
          <td><?php echo $r["optimizer_action"]; ?></td>
          <td><?php echo $r["assigned_vehicle"]; ?></td>
          <td>
              <a href="home.php?edit=<?php echo urlencode($r["record_id"]); ?>" class="btn-edit">Edit</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete this record?');">
                  <input type="hidden" name="record_id" value="<?php echo $r["record_id"]; ?>">
                  <button type="submit" name="btnDelete" class="btn-delete">Delete</button>
              </form>
          </td>
        </tr>
        <?php } } ?>
      </table>
    </div>
  </div>
<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>