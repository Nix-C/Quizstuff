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
:root {
  --font-color: white;
  /*--bg-color: rgb(0, 70, 102);*/
  --bg-color: rgb(13, 18, 28);
  --alt-bg-color: rgb(7, 9, 12);
  --card-bg-color: #171318;
  --site-width: 1000px;
  --ul-height: 18rem;
  --header-height: 3rem;
}

/** Imports **/

/* Fonts */
@font-face {
  font-family: "Lato";
  src: url("./assets/Lato/Lato-Regular.ttf");
  font-weight: normal;
}

@font-face {
  font-family: "Lato";
  src: url("./assets/fonts/Lato/Lato-Bold.ttf");
  font-weight: bold 700;
}

/** Global Styles **/

* {
  font-family: "Lato", sans-serif;
}

body {
  margin: 0;
  padding: 0;
  position: relative;
  overflow-x: hidden;
  background-color: var(--bg-color);
  color: var(--font-color);
  /* background-image: url("rm218-bb-07.jpg"); */
  background-size: 100% 100%;
  background-position: 0;
  background-repeat: no-repeat;
  background-attachment: fixed;
}
    /* Background canvas */
#canvas {
  position: fixed;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;
  z-index: -1;
}

/* Texture blending */
#canvas:after {
  content: "";
  position: fixed;
  width: 100vw;
  height: 100vh;
  top: 0;
  left: 0;

  background-image: url("./assets/images/binding-dark.png");
  mix-blend-mode: color-burn;
  opacity: 50%;
  z-index: 0;
}

/* Radial gradients */
[id^="radial-"] {
  position: fixed;
  --size: 100px; /* Default size*/
  width: var(--size);
  height: var(--size);
}

#radial-1 {
  opacity: 0.8;
  --size: 150vh;
  top: -75vh;
  left: -75vh;

  background: radial-gradient(circle, #004b78 5%, transparent 60%);
}

#radial-2 {
  opacity: 0.8;
  --size: 150vh;
  right: -75vh;
  bottom: -75vh;

  background: radial-gradient(circle, #431235 5%, transparent 60%);
}
    h1 {
      text-align: center;
      margin: 32px 0 24px 0;
      font-weight: 600;
      color: #7fd7ff;
      letter-spacing: 1px;
      text-shadow: 0 2px 8px #23272b88;
    }
    table {
      border-collapse: collapse;
      width: 96%;
      margin: 0 auto 40px auto;
      font-size: 1em;
      background: #23272b;
      box-shadow: 0 4px 24px #0008;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      border: 1px solid #2d3238;
      padding: 10px 14px;
      text-align: left;
    }
    th {
      background: #23272b;
      color: #7fd7ff;
      font-weight: 600;
      font-size: 1.05em;
      border-bottom: 2px solid #7fd7ff;
    }
    tr:nth-child(even) {
      background: #202328;
    }
    tr:nth-child(odd) {
      background: #23272b;
    }
    tr:hover {
      background: #2d3238;
      transition: background 0.2s;
    }
    .pad-list {
      font-size: 0.98em;
    }
    ul {
      margin: 0;
      padding-left: 18px;
    }
    @media (max-width: 1100px) {
      table, th, td { font-size: 0.92em; }
      th, td { padding: 7px 6px; }
    }
    @media (max-width: 800px) {
      table, th, td { font-size: 0.85em; }
      th, td { padding: 5px 3px; }
      h1 { font-size: 1.2em; }
    }
  </style>
</head>
<body>
    <div id="canvas">
      <div id="radial-1"></div>
      <div id="radial-2"></div>
    </div>
  <h1><?= htmlspecialchars($page_title) ?></h1>
  <table id="equipment-table">
    <thead>
      <tr>
        <th id="id-header" style="cursor:pointer; user-select:none;">ID &#8597;</th>
        <th>Name</th>
        <th>Contact</th>
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
      <?php foreach (
        $registrations as $reg): ?>
        <tr>
          <td><?= htmlspecialchars($reg['id']) ?></td>
          <td><?= htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) ?></td>
          <td>
            <?php if ($reg['phone']) echo 'Phone: ' . htmlspecialchars($reg['phone']) . '<br>'; ?>
            <?php if ($reg['email']) echo 'Email: ' . htmlspecialchars($reg['email']); ?>
          </td>
          <td><?= htmlspecialchars($reg['district']) ?></td>
          <td>
            <?php if ($reg['laptop_brand']) echo 'Brand: ' . htmlspecialchars($reg['laptop_brand']) . '<br>'; ?>
            <?php if ($reg['laptop_os']) echo 'OS: ' . htmlspecialchars($reg['laptop_os']) . '<br>'; ?>
            <?php if ($reg['laptop_parallel_port']) echo 'Parallel: ' . htmlspecialchars($reg['laptop_parallel_port']) . '<br>'; ?>
            <?php if ($reg['laptop_qm_version']) echo 'QM Ver: ' . htmlspecialchars($reg['laptop_qm_version']) . '<br>'; ?>
            <?php if ($reg['laptop_username']) echo 'User: ' . htmlspecialchars($reg['laptop_username']) . '<br>'; ?>
            <?php if ($reg['laptop_password']) echo 'Pass: ' . htmlspecialchars($reg['laptop_password']); ?>
          </td>
          <td>
            <?php if ($reg['interface_type']) echo 'Type: ' . htmlspecialchars($reg['interface_type']) . '<br>'; ?>
            <?php if ($reg['interface_qty'] !== null && $reg['interface_qty'] !== '') echo 'Qty: ' . htmlspecialchars($reg['interface_qty']); ?>
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
            <?php if ($reg['monitor_brand']) echo 'Brand: ' . htmlspecialchars($reg['monitor_brand']) . '<br>'; ?>
            <?php if ($reg['monitor_size']) echo 'Size: ' . htmlspecialchars($reg['monitor_size']) . '<br>'; ?>
            <?php if ($reg['monitor_resolution']) echo 'Res: ' . htmlspecialchars($reg['monitor_resolution']); ?>
          </td>
          <td>
            <?php if ($reg['projector_brand']) echo 'Brand: ' . htmlspecialchars($reg['projector_brand']) . '<br>'; ?>
            <?php if ($reg['projector_lumens'] !== null && $reg['projector_lumens'] !== '') echo 'Lumens: ' . htmlspecialchars($reg['projector_lumens']) . '<br>'; ?>
            <?php if ($reg['projector_resolution']) echo 'Res: ' . htmlspecialchars($reg['projector_resolution']) . '<br>'; ?>
            <?php if ($reg['projector_qty'] !== null && $reg['projector_qty'] !== '') echo 'Qty: ' . htmlspecialchars($reg['projector_qty']); ?>
          </td>
          <td>
            <?php if ($reg['powerstrip_make']) echo 'Make: ' . htmlspecialchars($reg['powerstrip_make']) . '<br>'; ?>
            <?php if ($reg['powerstrip_model']) echo 'Model: ' . htmlspecialchars($reg['powerstrip_model']) . '<br>'; ?>
            <?php if ($reg['powerstrip_color']) echo 'Color: ' . htmlspecialchars($reg['powerstrip_color']) . '<br>'; ?>
            <?php if ($reg['powerstrip_outlets'] !== null && $reg['powerstrip_outlets'] !== '') echo 'Plugs: ' . htmlspecialchars($reg['powerstrip_outlets']); ?>
          </td>
          <td>
            <?php if ($reg['extension_color']) echo 'Color: ' . htmlspecialchars($reg['extension_color']) . '<br>'; ?>
            <?php if ($reg['extension_length'] !== null && $reg['extension_length'] !== '') echo 'Length: ' . htmlspecialchars($reg['extension_length']); ?>
          </td>
          <td>
            <?php if ($reg['mic_type']) echo 'Type: ' . htmlspecialchars($reg['mic_type']) . '<br>'; ?>
            <?php if ($reg['mic_brand']) echo 'Brand: ' . htmlspecialchars($reg['mic_brand']) . '<br>'; ?>
            <?php if ($reg['mic_model']) echo 'Model: ' . htmlspecialchars($reg['mic_model']) . '<br>'; ?>
            <?php if ($reg['mic_qty'] !== null && $reg['mic_qty'] !== '') echo 'Qty: ' . htmlspecialchars($reg['mic_qty']); ?>
          </td>
          <td>
            <?php if ($reg['other_desc']) echo 'Desc: ' . nl2br(htmlspecialchars($reg['other_desc'])) . '<br>'; ?>
            <?php if ($reg['other_qty'] !== null && $reg['other_qty'] !== '') echo 'Qty: ' . htmlspecialchars($reg['other_qty']); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <script>
    // Table sort by ID (ascending/descending)
    document.addEventListener('DOMContentLoaded', function() {
      const table = document.getElementById('equipment-table');
      const idHeader = document.getElementById('id-header');
      let asc = false;
      idHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const idA = parseInt(a.children[0].textContent, 10);
          const idB = parseInt(b.children[0].textContent, 10);
          return asc ? idA - idB : idB - idA;
        });
        asc = !asc;
        rows.forEach(row => tbody.appendChild(row));
        idHeader.innerHTML = asc ? 'ID &#8593;' : 'ID &#8595;';
      });
    });
  </script>
</body>
</html>
