<?php
$conn = new mysqli("127.0.0.1", "root", "", "qc_wastemanagement", 3307);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
mysqli_report(MYSQLI_REPORT_OFF);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date     = $conn->real_escape_string($_POST['record_date']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $district = $conn->real_escape_string($_POST['qc_district']);
    $capacity = (float)$_POST['bin_capacity_kg'];
    $fill     = (float)$_POST['trash_fill_level_pct'];
    $collected= (float)$_POST['collected_waste_kg'];
    $status   = $conn->real_escape_string($_POST['bin_status']);
    $priority = $conn->real_escape_string($_POST['priority_level']);
    $action   = $conn->real_escape_string($_POST['optimizer_action']);
    $vehicle  = $conn->real_escape_string($_POST['assigned_vehicle']);

    $sql = "INSERT INTO waste_records (record_date, barangay, qc_district, bin_capacity_kg, trash_fill_level_pct, collected_waste_kg, bin_status, priority_level, optimizer_action, assigned_vehicle) 
            VALUES ('$date', '$barangay', '$district', $capacity, $fill, $collected, '$status', '$priority', '$action', '$vehicle')";
    
    if ($conn->query($sql)) {
        header("Location: home.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Waste - Quezon City Waste Management</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; display: flex; font-family: Arial, sans-serif; background: #f5f5f5; color: #111; }
    .sidebar { width: 250px; min-height: 100vh; background: #fff; border-right: 1px solid #ddd; padding: 16px; }
    .sidebar h1 { font-size: 20px; margin: 0 0 16px; }
    .search { border: 1px solid #ddd; border-radius: 8px; padding: 10px; color: #777; font-size: 14px; margin-bottom: 16px; display: flex; justify-content: space-between; }
    .search input { border: 0; outline: 0; background: transparent; font-size: 14px; width: 100%; }
    nav ul { list-style: none; margin: 0; padding: 0; }
    nav a { text-decoration: none; color: #111; }
    nav li { display: flex; justify-content: space-between; padding: 11px 12px; border-radius: 8px; font-size: 14px; font-weight: 500; }
    nav li:hover { background: #eee; }
    nav li.active { background: #eee; font-weight: bold; }
    .main { flex: 1; }
    .topbar { height: 56px; background: #fff; border-bottom: 1px solid #ddd; display: flex; justify-content: flex-end; align-items: center; padding: 0 24px; font-size: 14px; }
    .content { padding: 24px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .header h2 { margin: 0; font-size: 28px; }
    button { background: #222; color: #fff; border: 0; border-radius: 8px; padding: 12px 18px; font-size: 15px; cursor: pointer; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 14px; font-weight: bold; margin-bottom: 5px; }
    .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h1>Waste Management</h1>
    <div class="search"><input type="text" placeholder="Search..."></div>
    <nav>
      <ul>
        <a href="home.php"><li>Dashboard</li></a>
        <a href="wastecollection.php"><li>Waste Collection <span>›</span></li></a>
        <a href="bincollection.php"><li>Bin Management <span>›</span></li></a>
        <a href="zone&area.php"><li>Zone &amp; Area <span>›</span></li></a>
        <a href="vehiclemanagement.php"><li>Vehicle Management <span>›</span></li></a>
      </ul>
    </nav>
  </div>

  <div class="main">
    <div class="topbar">Super Admin</div>
    <div class="content">
      <div class="header">
        <h2>Create New Waste Record</h2>
        <a href="home.php"><button type="button" style="background:#666;">Back</button></a>
      </div>

      <form method="POST" style="background:#fff; padding:24px; border:1px solid #ddd; border-radius:14px; max-width:600px;">
        <div class="form-group">
          <label>Record Date</label>
          <input type="date" name="record_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
          <label>Barangay</label>
          <input type="text" name="barangay" required>
        </div>
        <div class="form-group">
          <label>District</label>
          <select name="qc_district">
            <option value="District 1">District 1</option>
            <option value="District 2">District 2</option>
            <option value="District 3">District 3</option>
            <option value="District 4">District 4</option>
            <option value="District 5">District 5</option>
            <option value="District 6">District 6</option>
          </select>
        </div>
        <div class="form-group">
          <label>Bin Capacity (kg)</label>
          <input type="number" step="0.01" name="bin_capacity_kg" value="100" required>
        </div>
        <div class="form-group">
          <label>Fill Level (%)</label>
          <input type="number" step="0.1" name="trash_fill_level_pct" value="0" required>
        </div>
        <div class="form-group">
          <label>Collected Waste (kg)</label>
          <input type="number" step="0.01" name="collected_waste_kg" value="0" required>
        </div>
        <div class="form-group">
          <label>Bin Status</label>
          <select name="bin_status">
            <option value="Normal">Normal</option>
            <option value="Full">Full</option>
            <option value="Overflowing">Overflowing</option>
          </select>
        </div>
        <div class="form-group">
          <label>Priority Level</label>
          <select name="priority_level">
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
          </select>
        </div>
        <div class="form-group">
          <label>Optimizer Action</label>
          <select name="optimizer_action">
            <option value="No Action">No Action</option>
            <option value="Scheduled Pickup">Scheduled Pickup</option>
          </select>
        </div>
        <div class="form-group">
          <label>Assigned Vehicle</label>
          <input type="text" name="assigned_vehicle" placeholder="e.g. Truck 1">
        </div>
        <button type="submit">Submit Record</button>
      </form>
    </div>
  </div>
</body>
</html>