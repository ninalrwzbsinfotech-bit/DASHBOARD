<?php
$conn = new mysqli("127.0.0.1", "root", "", "qc_wastemanagement", 3307);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
mysqli_report(MYSQLI_REPORT_OFF);

$result = $conn->query("SELECT DISTINCT assigned_vehicle, barangay, qc_district FROM waste_records WHERE assigned_vehicle <> ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehicle Management - Quezon City Waste Management</title>
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
    table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 24px; font-size: 14px; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
    th { background: #eee; }
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
        <a href="binmanagement.php"><li>Bin Management <span>›</span></li></a>
        <a href="zones.php"><li>Zone &amp; Area <span>›</span></li></a>
        <a href="vehiclemanagement.php"><li class="active">Vehicle Management <span>›</span></li></a>
      </ul>
    </nav>
  </div>

  <div class="main">
    <div class="topbar">Super Admin</div>
    <div class="content">
      <div class="header">
        <h2>Vehicle Management &amp; Assignments</h2>
      </div>

      <table>
        <tr>
          <th>Assigned Vehicle</th>
          <th>Barangay Route</th>
          <th>District</th>
        </tr>
        <?php if ($result) { while ($r = $result->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $r["assigned_vehicle"]; ?></td>
          <td><?php echo $r["barangay"]; ?></td>
          <td><?php echo $r["qc_district"]; ?></td>
        </tr>
        <?php } } ?>
      </table>
    </div>
  </div>
</body>
</html>