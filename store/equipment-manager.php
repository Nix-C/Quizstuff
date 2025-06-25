<?php
include 'config/database.php';
$page_title = "Equipment Registration Overview";

// Fetch all registrations with details and pads
$sql = "SELECT er.*, ed.*, p.pad_color, p.pad_qty
FROM equipment_registration er
LEFT JOIN equipment_details ed ON er.id = ed.registration_id
LEFT JOIN pads p ON er.id = p.registration_id
ORDER BY er.id DESC, p.pad_color";
$result = $conn->query($sql);

// Build a multi-dimensional array to group pads by registration
$registrations = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $reg_id = $row['id'];
    if (!isset($registrations[$reg_id])) {
      $registrations[$reg_id] = $row;
      $registrations[$reg_id]['pads'] = [];
    }
    if ($row['pad_color'] !== null) {
      $registrations[$reg_id]['pads'][] = [
        'pad_color' => $row['pad_color'],
        'pad_qty' => $row['pad_qty']
      ];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <style>
    table { border-collapse: collapse; width: 100%; font-size: 0.95em; }
    th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
    th { background: #f0f0f0; }
    tr:nth-child(even) { background: #fafafa; }
    .pad-list { font-size: 0.95em; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($page_title) ?></h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>District</th>
        <th>Laptop</th>
        <th>Interface Box</th>
        <th>Pads</th>
        <th>Monitor</th>
        <th>Projector</th>
        <th>Powerstrip</th>
        <th>Extension Cord</th>
        <th>Microphone/Recorder</th>
        <th>Other</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($registrations as $reg): ?>
        <tr>
          <td><?= htmlspecialchars($reg['id']) ?></td>
          <td><?= htmlspecialchars($reg['first_name']) ?></td>
          <td><?= htmlspecialchars($reg['last_name']) ?></td>
          <td><?= htmlspecialchars($reg['phone']) ?></td>
          <td><?= htmlspecialchars($reg['email']) ?></td>
          <td><?= htmlspecialchars($reg['district']) ?></td>
          <td>
            Brand: <?= htmlspecialchars($reg['laptop_brand']) ?><br>
            OS: <?= htmlspecialchars($reg['laptop_os']) ?><br>
            Parallel: <?= htmlspecialchars($reg['laptop_parallel_port']) ?><br>
            QM Ver: <?= htmlspecialchars($reg['laptop_qm_version']) ?><br>
            User: <?= htmlspecialchars($reg['laptop_username']) ?><br>
            Pass: <?= htmlspecialchars($reg['laptop_password']) ?>
          </td>
          <td>
            Type: <?= htmlspecialchars($reg['interface_type']) ?><br>
            Qty: <?= htmlspecialchars($reg['interface_qty']) ?>
          </td>
          <td class="pad-list">
            <?php if (!empty($reg['pads'])): ?>
              <ul style="margin:0; padding-left:18px;">
                <?php foreach ($reg['pads'] as $pad): ?>
                  <li><?= htmlspecialchars($pad['pad_color']) ?> (<?= htmlspecialchars($pad['pad_qty']) ?>)</li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td>
            Brand: <?= htmlspecialchars($reg['monitor_brand']) ?><br>
            Size: <?= htmlspecialchars($reg['monitor_size']) ?><br>
            Res: <?= htmlspecialchars($reg['monitor_resolution']) ?>
          </td>
          <td>
            Brand: <?= htmlspecialchars($reg['projector_brand']) ?><br>
            Lumens: <?= htmlspecialchars($reg['projector_lumens']) ?><br>
            Res: <?= htmlspecialchars($reg['projector_resolution']) ?><br>
            Qty: <?= htmlspecialchars($reg['projector_qty']) ?>
          </td>
          <td>
            Make: <?= htmlspecialchars($reg['powerstrip_make']) ?><br>
            Model: <?= htmlspecialchars($reg['powerstrip_model']) ?><br>
            Color: <?= htmlspecialchars($reg['powerstrip_color']) ?><br>
            Plugs: <?= htmlspecialchars($reg['powerstrip_outlets']) ?>
          </td>
          <td>
            Color: <?= htmlspecialchars($reg['extension_color']) ?><br>
            Length: <?= htmlspecialchars($reg['extension_length']) ?>
          </td>
          <td>
            Type: <?= htmlspecialchars($reg['mic_type']) ?><br>
            Brand: <?= htmlspecialchars($reg['mic_brand']) ?><br>
            Model: <?= htmlspecialchars($reg['mic_model']) ?><br>
            Qty: <?= htmlspecialchars($reg['mic_qty']) ?>
          </td>
          <td>
            Desc: <?= nl2br(htmlspecialchars($reg['other_desc'])) ?><br>
            Qty: <?= htmlspecialchars($reg['other_qty']) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
