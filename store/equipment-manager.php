<?php
include 'config/database.php';
$page_title = "Equipment Registration Overview";

// Fetch all registrations
$registrations = [];
$sql = "SELECT * FROM equipment_registration ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $reg_id = $row['id'];
    $registrations[$reg_id] = $row;
    $registrations[$reg_id]['pads'] = [];
    $registrations[$reg_id]['notes'] = isset($row['notes']) ? $row['notes'] : '';
  }
}

// Fetch all equipment_details and merge into registrations
$sql = "SELECT * FROM equipment_details";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $reg_id = $row['registration_id'];
    if (!isset($registrations[$reg_id])) continue;
    // Aggregate interface boxes
    if (isset($row['interface_type']) && $row['interface_type']) {
      if (!isset($registrations[$reg_id]['interface_boxes'])) $registrations[$reg_id]['interface_boxes'] = [];
      $registrations[$reg_id]['interface_boxes'][] = [
        'type' => $row['interface_type'],
        'qty' => isset($row['interface_qty']) ? (int)$row['interface_qty'] : 0
      ];
    }
    // Merge other fields as before
    foreach ($row as $key => $val) {
      if ($key !== 'registration_id' && $val !== null && $key !== 'interface_type' && $key !== 'interface_qty') {
        $registrations[$reg_id][$key] = $val;
      }
    }
  }
}

// Fetch all pads and merge into registrations
$sql = "SELECT * FROM pads";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $reg_id = $row['registration_id'];
    if (!isset($registrations[$reg_id])) continue;
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
      width: 98vw;
      max-width: 98vw;
      min-width: 98vw;
      margin: 0 auto 40px auto;
      font-size: 0.8em;
      background: #23272b;
      box-shadow: 0 4px 24px #0008;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      border: 1px solid rgb(104, 135, 173);
      padding: 10px 14px;
      text-align: left;
    }
    td {
      line-height: 1.5;
    }
    /* Make Laptop and Other columns wider */
    th#laptop-header, td:nth-child(4) {
      min-width: 220px;
      max-width: 320px;
      width: 260px;
      word-break: break-word;
    }
    th:nth-child(12), td:nth-child(12) {
      min-width: 180px;
      max-width: 260px;
      width: 210px;
      word-break: break-word;
    }
    th {
      background: #23272b;
      color: #7fd7ff;
      font-weight: 600;
      font-size: 1.05em;
      border-bottom: 2px solid #7fd7ff;
      /* display: flex; */
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
      table { width: 98vw; max-width: 98vw; min-width: 98vw; }
    }
    @media (max-width: 800px) {
      table, th, td { font-size: 0.85em; }
      th, td { padding: 5px 3px; }
      h1 { font-size: 1.2em; }
      table { width: 98vw; max-width: 98vw; min-width: 98vw; }
    }
    #horizontal {display: flex;}
    button{display: block;}
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
        <th id="productid-header">Product ID</th>
        <th id="name-header" style="cursor:pointer; user-select:none;">Contact &#8597;</th>
        <th id="district-header" style="cursor:pointer; user-select:none;">District &#8597;</th>
        <th id="laptop-header" style="cursor:pointer; user-select:none;">Laptop &#8597;</th>
        <th id="interface-header" style="cursor:pointer; user-select:none;">Interface Box &#8597;</th>
        <th id="pads-header" style="cursor:pointer; user-select:none;">Pads <div id="horizontal"> </div></th>
        <th>Monitor</th>
        <th>Projector</th>
        <th>Powerstrip</th>
        <th>Extension Cord</th>
        <th>Microphone/Recorder</th>
        <th>Other</th>
        <th id="status-header" style="cursor:pointer; user-select:none; position:relative;">Status
          <button id="status-filter-btn" title="Group by Status" style="margin-left:6px; font-size:1em; background:none; border:none; color:#7fd7ff; cursor:pointer; vertical-align:middle;"></button>
        </th>
        <th>Notes</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Helper to output a row for a single item
      function output_item_row($reg, $item_type, $item_data, $statuses) {
        // $item_type: string, e.g. 'laptop', 'interface', 'pad', etc.
        // $item_data: array of fields for the item, or null for empty
        // Output a <tr> with only the relevant item column filled
        $row_index = isset($item_data['row_index']) ? $item_data['row_index'] : 0;
        // Generate a unique product ID for each item
        $product_id = $reg['id'] . '_' . $item_type . '_' . $row_index;
        if ($item_type === 'pad' && isset($item_data['color'])) {
          $product_id = $reg['id'] . '_pad_' . strtolower($item_data['color']) . '_' . $row_index;
        } else if ($item_type === 'interface' && isset($item_data['type'])) {
          $product_id = $reg['id'] . '_interface_' . strtolower($item_data['type']) . '_' . $row_index;
        } else if ($item_type === 'monitor' && isset($item_data['brand'])) {
          $product_id = $reg['id'] . '_monitor_' . strtolower($item_data['brand']) . '_' . $row_index;
        } else if ($item_type === 'laptop' && isset($item_data['brand'])) {
          $product_id = $reg['id'] . '_laptop_' . strtolower($item_data['brand']) . '_' . $row_index;
        } else if ($item_type === 'projector' && isset($item_data['brand'])) {
          $product_id = $reg['id'] . '_projector_' . strtolower($item_data['brand']) . '_' . $row_index;
        } else if ($item_type === 'powerstrip' && isset($item_data['make'])) {
          $product_id = $reg['id'] . '_powerstrip_' . strtolower($item_data['make']) . '_' . $row_index;
        } else if ($item_type === 'extension' && isset($item_data['color'])) {
          $product_id = $reg['id'] . '_extension_' . strtolower($item_data['color']) . '_' . $row_index;
        } else if ($item_type === 'mic' && isset($item_data['type'])) {
          $product_id = $reg['id'] . '_mic_' . strtolower($item_data['type']) . '_' . $row_index;
        } else if ($item_type === 'other' && isset($item_data['desc'])) {
          $product_id = $reg['id'] . '_other_' . $row_index;
        }
        echo "<tr>\n";
        // ID
        echo "  <td>" . htmlspecialchars($reg['id']) . "</td>\n";
        // Product ID
        echo "  <td>" . htmlspecialchars($product_id) . "</td>\n";
        // Contact
        echo "  <td><strong>Name:</strong> " . htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) . "<br>";
        if ($reg['phone']) echo '<strong>Phone: </strong>' . htmlspecialchars($reg['phone']) . '<br>';
        if ($reg['email']) echo '<strong>Email: </strong>' . htmlspecialchars($reg['email']);
        echo "</td>\n";
        // District
        echo "  <td>" . htmlspecialchars($reg['district']) . "</td>\n";
        // Laptop
        echo "  <td>";
        if ($item_type === 'laptop' && $item_data) {
          if ($item_data['brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($item_data['brand']) . '<br><br>';
          if ($item_data['os']) echo '<strong>OS:</strong> ' . htmlspecialchars($item_data['os']) . '<br><br>';
          if ($item_data['parallel_port']) echo '<strong>Parallel:</strong> ' . htmlspecialchars($item_data['parallel_port']) . '<br><br>';
          if ($item_data['qm_version']) echo '<strong>QM Ver:</strong> ' . htmlspecialchars($item_data['qm_version']) . '<br><br>';
          if ($item_data['username']) echo '<strong>User:</strong> ' . htmlspecialchars($item_data['username']) . '<br><br>';
          if ($item_data['password']) echo '<strong>Pass:</strong> ' . htmlspecialchars($item_data['password']);
        }
        echo "</td>\n";
        // Interface Box
        echo "  <td>";
        if ($item_type === 'interface' && $item_data) {
          if ($item_data['type']) echo '<strong>Type:</strong> ' . htmlspecialchars($item_data['type']) . '<br><br>';
          if ($item_data['qty'] !== null && $item_data['qty'] !== '') echo '<strong>Qty:</strong> ' . htmlspecialchars($item_data['qty']);
        }
        echo "</td>\n";
        // Pads
        echo "  <td class=\"pad-list\">";
        if ($item_type === 'pad' && $item_data) {
          // Map color names to CSS color values
          $colorMap = [
            'red' => 'red',
            'green' => 'green',
            'blue' => 'blue',
            'yellow' => '#e6e600',
            'orange' => 'orange',
            'purple' => 'purple',
            'black' => 'black',
            'white' => 'white',
            // Add more as needed
          ];
          $padColor = strtolower(trim($item_data['color']));
          $fontColor = isset($colorMap[$padColor]) ? $colorMap[$padColor] : 'inherit';
          echo '<ul style="margin:0; padding-left:18px;"><li style="color:' . htmlspecialchars($fontColor) . '; font-weight:bold;">' . htmlspecialchars($item_data['color']) . ' (1)</li></ul>';
        } else {
          echo '—';
        }
        echo "</td>\n";
        // Monitor
        echo "  <td>";
        if ($item_type === 'monitor' && $item_data) {
          if ($item_data['brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($item_data['brand']) . '<br><br>';
          if ($item_data['size']) echo '<strong>Size:</strong> ' . htmlspecialchars($item_data['size']) . '<br><br>';
          if ($item_data['resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($item_data['resolution']) . '<br><br>';
          if ($item_data['connection']) echo '<strong>Connection:</strong> ' . htmlspecialchars($item_data['connection']);
        }
        echo "</td>\n";
        // Projector
        echo "  <td>";
        if ($item_type === 'projector' && $item_data) {
          if ($item_data['brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($item_data['brand']) . '<br><br>';
          if ($item_data['lumens'] !== null && $item_data['lumens'] !== '') echo '<strong>Lumens:</strong> ' . htmlspecialchars($item_data['lumens']) . '<br><br>';
          if ($item_data['resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($item_data['resolution']) . '<br><br>';
          if ($item_data['qty'] !== null && $item_data['qty'] !== '') echo '<strong>Qty:</strong> 1';
        }
        echo "</td>\n";
        // Powerstrip
        echo "  <td>";
        if ($item_type === 'powerstrip' && $item_data) {
          if ($item_data['make']) echo '<strong>Make:</strong> ' . htmlspecialchars($item_data['make']) . '<br><br>';
          if ($item_data['model']) echo '<strong>Model:</strong> ' . htmlspecialchars($item_data['model']) . '<br><br>';
          if ($item_data['color']) echo '<strong>Color:</strong> ' . htmlspecialchars($item_data['color']) . '<br><br>';
          if ($item_data['outlets'] !== null && $item_data['outlets'] !== '') echo '<strong>Plugs:</strong> ' . htmlspecialchars($item_data['outlets']);
        }
        echo "</td>\n";
        // Extension Cord
        echo "  <td>";
        if ($item_type === 'extension' && $item_data) {
          if ($item_data['color']) echo '<strong>Color:</strong> ' . htmlspecialchars($item_data['color']) . '<br><br>';
          if ($item_data['length'] !== null && $item_data['length'] !== '') echo '<strong>Length:</strong> ' . htmlspecialchars($item_data['length']);
        }
        echo "</td>\n";
        // Microphone/Recorder
        echo "  <td>";
        if ($item_type === 'mic' && $item_data) {
          if ($item_data['type']) echo '<strong>Type:</strong> ' . htmlspecialchars($item_data['type']) . '<br><br>';
          if ($item_data['brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($item_data['brand']) . '<br><br>';
          if ($item_data['model']) echo '<strong>Model:</strong> ' . htmlspecialchars($item_data['model']) . '<br><br>';
          if ($item_data['qty'] !== null && $item_data['qty'] !== '') echo '<strong>Qty:</strong> 1';
        }
        echo "</td>\n";
        // Other
        echo "  <td>";
        if ($item_type === 'other' && $item_data) {
          if ($item_data['desc']) echo '<strong>Desc:</strong> ' . nl2br(htmlspecialchars($item_data['desc'])) . '<br><br>';
          if ($item_data['qty'] !== null && $item_data['qty'] !== '') echo '<strong>Qty:</strong> 1';
        }
        echo "</td>\n";
        // Status
        echo "  <td>";
        $item_key = $reg['id'] . '_' . $item_type . '_' . ($item_data['row_index'] ?? 0);
        $currentStatus = isset($reg['status']) ? $reg['status'] : '';
        echo '<select class="status-dropdown" data-itemkey="' . htmlspecialchars($item_key) . '" style="width: 150px; background: #181c22; color: #fff; border: 1px solid #444; border-radius: 4px;">';
        foreach ($statuses as $status) {
          $selected = ($currentStatus === $status) ? 'selected' : '';
          $label = $status === '' ? '-- Select --' : $status;
          echo "<option value=\"" . htmlspecialchars($status) . "\" $selected>$label</option>";
        }
        echo '</select>';
        echo '<span class="status-save-msg" style="font-size:0.9em; margin-left:6px;"></span>';
        echo "</td>\n";
        // Notes
        echo "  <td>";
        echo '<textarea style="width: 160px; min-height: 40px; background: #181c22; color: #fff; border: 1px solid #444; border-radius: 4px; resize: vertical;" data-itemkey="' . htmlspecialchars($item_key) . '">' . (isset($reg['notes']) ? htmlspecialchars($reg['notes']) : '') . '</textarea>';
        echo '<button class="save-notes-btn" data-itemkey="' . htmlspecialchars($item_key) . '" style="margin-top: 4px; background: #23272b; color: #7fd7ff; border: 1px solid #7fd7ff; border-radius: 4px; cursor: pointer;">Save</button>';
        echo '<span class="notes-status" style="font-size:0.9em; margin-left:6px;"></span>';
        echo "</td>\n";
        echo "</tr>\n";
      }
      $statuses = [
        '',
        'In Room',
        'In Inventory',
        'Used in Tech Room',
        'Broken (In Inventory)',
        'Other'
      ];
      foreach (
        $registrations as $reg
      ) {
        $hasEquipment = false;
        // Debug: Output interface box values to browser console
        if (isset($reg['interface_boxes'])) {
          foreach ($reg['interface_boxes'] as $box) {
            echo "<script>console.log('ID: ", htmlspecialchars($reg['id']), ", interface_type: ", htmlspecialchars($box['type']), ", interface_qty: ", htmlspecialchars($box['qty']), "');</script>\n";
          }
        } else {
          echo "<script>console.log('ID: ", htmlspecialchars($reg['id']), ", interface_type: none, interface_qty: none');</script>\n";
        }
        // Laptops (always 1 row if present)
        if (
          (isset($reg['laptop_brand']) && $reg['laptop_brand']) || 
          (isset($reg['laptop_os']) && $reg['laptop_os']) || 
          (isset($reg['laptop_parallel_port']) && $reg['laptop_parallel_port']) || 
          (isset($reg['laptop_qm_version']) && $reg['laptop_qm_version']) || 
          (isset($reg['laptop_username']) && $reg['laptop_username']) || 
          (isset($reg['laptop_password']) && $reg['laptop_password'])
        ) {
          $hasEquipment = true;
          output_item_row($reg, 'laptop', [
            'brand' => isset($reg['laptop_brand']) ? $reg['laptop_brand'] : '',
            'os' => isset($reg['laptop_os']) ? $reg['laptop_os'] : '',
            'parallel_port' => isset($reg['laptop_parallel_port']) ? $reg['laptop_parallel_port'] : '',
            'qm_version' => isset($reg['laptop_qm_version']) ? $reg['laptop_qm_version'] : '',
            'username' => isset($reg['laptop_username']) ? $reg['laptop_username'] : '',
            'password' => isset($reg['laptop_password']) ? $reg['laptop_password'] : '',
          ], $statuses);
        }
        // Interface boxes (multiple types/qty)
        if (isset($reg['interface_boxes']) && is_array($reg['interface_boxes'])) {
          $interface_row_index = 0;
          foreach ($reg['interface_boxes'] as $box) {
            if ($box['type'] && $box['qty'] > 0) {
              $hasEquipment = true;
              for ($i = 0; $i < $box['qty']; $i++) {
                output_item_row($reg, 'interface', [
                  'type' => $box['type'],
                  'qty' => 1,
                  'row_index' => $interface_row_index++
                ], $statuses);
              }
            } elseif ($box['type']) {
              $hasEquipment = true;
              output_item_row($reg, 'interface', [
                'type' => $box['type'],
                'qty' => $box['qty'],
                'row_index' => $interface_row_index++
              ], $statuses);
            }
          }
        }
        // Pads (each pad color/qty as separate rows)
        if (!empty($reg['pads'])) {
          foreach ($reg['pads'] as $pad) {
            $pad_qty = (int)(isset($pad['pad_qty']) ? $pad['pad_qty'] : 0);
            for ($i = 0; $i < $pad_qty; $i++) {
              $hasEquipment = true;
              output_item_row($reg, 'pad', [
                'color' => isset($pad['pad_color']) ? $pad['pad_color'] : ''
              ], $statuses);
            }
          }
        }
        // Monitor (qty rows)
        if (
          (isset($reg['monitor_brand']) && $reg['monitor_brand']) ||
          (isset($reg['monitor_size']) && $reg['monitor_size']) ||
          (isset($reg['monitor_resolution']) && $reg['monitor_resolution']) ||
          (isset($reg['monitor_connection']) && $reg['monitor_connection'])
        ) {
          $hasEquipment = true;
          output_item_row($reg, 'monitor', [
            'brand' => isset($reg['monitor_brand']) ? $reg['monitor_brand'] : '',
            'size' => isset($reg['monitor_size']) ? $reg['monitor_size'] : '',
            'resolution' => isset($reg['monitor_resolution']) ? $reg['monitor_resolution'] : '',
            'connection' => isset($reg['monitor_connection']) ? $reg['monitor_connection'] : ''
          ], $statuses);
        }
        // Projector (qty rows)
        $projector_qty = (int)(isset($reg['projector_qty']) ? $reg['projector_qty'] : 0);
        if (isset($reg['projector_brand']) && $reg['projector_brand'] && $projector_qty > 0) {
          $hasEquipment = true;
          for ($i = 0; $i < $projector_qty; $i++) {
            output_item_row($reg, 'projector', [
              'brand' => $reg['projector_brand'],
              'lumens' => isset($reg['projector_lumens']) ? $reg['projector_lumens'] : '',
              'resolution' => isset($reg['projector_resolution']) ? $reg['projector_resolution'] : '',
              'qty' => 1
            ], $statuses);
          }
        } elseif (isset($reg['projector_brand']) && $reg['projector_brand']) {
          $hasEquipment = true;
          output_item_row($reg, 'projector', [
            'brand' => $reg['projector_brand'],
            'lumens' => isset($reg['projector_lumens']) ? $reg['projector_lumens'] : '',
            'resolution' => isset($reg['projector_resolution']) ? $reg['projector_resolution'] : '',
            'qty' => isset($reg['projector_qty']) ? $reg['projector_qty'] : ''
          ], $statuses);
        }
        // Powerstrip (always 1 row if present)
        if (
          (isset($reg['powerstrip_make']) && $reg['powerstrip_make']) ||
          (isset($reg['powerstrip_model']) && $reg['powerstrip_model']) ||
          (isset($reg['powerstrip_color']) && $reg['powerstrip_color']) ||
          (isset($reg['powerstrip_outlets']) && $reg['powerstrip_outlets'])
        ) {
          $hasEquipment = true;
          output_item_row($reg, 'powerstrip', [
            'make' => isset($reg['powerstrip_make']) ? $reg['powerstrip_make'] : '',
            'model' => isset($reg['powerstrip_model']) ? $reg['powerstrip_model'] : '',
            'color' => isset($reg['powerstrip_color']) ? $reg['powerstrip_color'] : '',
            'outlets' => isset($reg['powerstrip_outlets']) ? $reg['powerstrip_outlets'] : ''
          ], $statuses);
        }
        // Extension cord (always 1 row if present)
        if (
          (isset($reg['extension_color']) && $reg['extension_color']) ||
          (isset($reg['extension_length']) && $reg['extension_length'])
        ) {
          $hasEquipment = true;
          output_item_row($reg, 'extension', [
            'color' => isset($reg['extension_color']) ? $reg['extension_color'] : '',
            'length' => isset($reg['extension_length']) ? $reg['extension_length'] : ''
          ], $statuses);
        }
        // Microphone/Recorder (qty rows)
        $mic_qty = (int)(isset($reg['mic_qty']) ? $reg['mic_qty'] : 0);
        if (isset($reg['mic_type']) && $reg['mic_type'] && $mic_qty > 0) {
          $hasEquipment = true;
          for ($i = 0; $i < $mic_qty; $i++) {
            output_item_row($reg, 'mic', [
              'type' => $reg['mic_type'],
              'brand' => isset($reg['mic_brand']) ? $reg['mic_brand'] : '',
              'model' => isset($reg['mic_model']) ? $reg['mic_model'] : '',
              'qty' => 1
            ], $statuses);
          }
        } elseif (isset($reg['mic_type']) && $reg['mic_type']) {
          $hasEquipment = true;
          output_item_row($reg, 'mic', [
            'type' => $reg['mic_type'],
            'brand' => isset($reg['mic_brand']) ? $reg['mic_brand'] : '',
            'model' => isset($reg['mic_model']) ? $reg['mic_model'] : '',
            'qty' => isset($reg['mic_qty']) ? $reg['mic_qty'] : ''
          ], $statuses);
        }
        // Other (qty rows)
        $other_qty = (int)(isset($reg['other_qty']) ? $reg['other_qty'] : 0);
        if (isset($reg['other_desc']) && $reg['other_desc'] && $other_qty > 0) {
          $hasEquipment = true;
          for ($i = 0; $i < $other_qty; $i++) {
            output_item_row($reg, 'other', [
              'desc' => $reg['other_desc'],
              'qty' => 1
            ], $statuses);
          }
        } elseif (isset($reg['other_desc']) && $reg['other_desc']) {
          $hasEquipment = true;
          output_item_row($reg, 'other', [
            'desc' => $reg['other_desc'],
            'qty' => isset($reg['other_qty']) ? $reg['other_qty'] : ''
          ], $statuses);
        }
        // If no equipment or pads, show a blank row for this registration
        if (!$hasEquipment) {
          output_item_row($reg, '', [], $statuses);
        }
      }
      ?>
    </tbody>
  </table>
  <script>
    // Table sort by ID (ascending/descending) and Name (A-Z/Z-A)
    document.addEventListener('DOMContentLoaded', function() {
      const table = document.getElementById('equipment-table');
      const idHeader = document.getElementById('id-header');
      const nameHeader = document.getElementById('name-header');
      const districtHeader = document.getElementById('district-header');
      const laptopHeader = document.getElementById('laptop-header');
      const interfaceHeader = document.getElementById('interface-header');
      const padsHeader = document.getElementById('pads-header');
      const statusHeader = document.getElementById('status-header');
      const horizontal = document.getElementById('horizontal');
      let ascId = false;
      let ascName = false;
      let ascDistrict = false;
      let ascLaptop = false;
      let ascInterface = false;
      // Pads filter state
      const padColors = [
        {color: 'Red', icon: '\uD83D\uDD34'},
        {color: 'Green', icon: '\uD83D\uDFE2'},
        {color: 'Blue', icon: '\uD83D\uDD35'},
        {color: 'Yellow', icon: '\uD83D\uDFE1'}
      ];
      // Only filter by one color at a time, no cycling
      function filterPadsByColor(color) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        // Sort rows: those with the color first (alphabetically by color), then the rest (alphabetically by color or blank)
        rows.sort((a, b) => {
          const getPadColor = (row) => {
            const padCell = row.children[5]; // Pads column is index 5
            const li = padCell.querySelector('li');
            if (!li) return '';
            // Extract color name before first space or parenthesis
            const match = li.textContent.trim().match(/^([^( ]+)/);
            return match ? match[1].toLowerCase() : '';
          };
          const aColor = getPadColor(a);
          const bColor = getPadColor(b);
          const aMatch = aColor === color.toLowerCase();
          const bMatch = bColor === color.toLowerCase();
          if (aMatch && !bMatch) return -1;
          if (!aMatch && bMatch) return 1;
          // If both match or both don't, sort alphabetically by color
          if (aColor < bColor) return -1;
          if (aColor > bColor) return 1;
          return 0;
        });
        rows.forEach(row => tbody.appendChild(row));
      }
      // Add filter buttons for each color
      horizontal.innerHTML = padColors.map(pc => `<button style="margin-left:2px; font-size:1em; background:none; border:none; color:inherit; cursor:pointer;" data-color="${pc.color}">${pc.icon}</button>`).join('');
      padColors.forEach(pc => {
        horizontal.querySelector(`button[data-color='${pc.color}']`).addEventListener('click', function(e) {
          e.stopPropagation();
          filterPadsByColor(pc.color);
        });
      });
      // ID header click event
      idHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const idA = parseInt(a.children[0].textContent, 10);
          const idB = parseInt(b.children[0].textContent, 10);
          return ascId ? idA - idB : idB - idA;
        });
        ascId = !ascId;
        rows.forEach(row => tbody.appendChild(row));
        idHeader.innerHTML = ascId ? 'ID &#8593;' : 'ID &#8595;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // Name header click event
      nameHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const nameA = a.children[1].textContent.trim().toLowerCase();
          const nameB = b.children[1].textContent.trim().toLowerCase();
          if (nameA < nameB) return ascName ? -1 : 1;
          if (nameA > nameB) return ascName ? 1 : -1;
          return 0;
        });
        ascName = !ascName;
        rows.forEach(row => tbody.appendChild(row));
        nameHeader.innerHTML = ascName ? 'Contact &#8593;' : 'Contact &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // District header click event
      districtHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const distA = a.children[2].textContent.trim().toLowerCase();
          const distB = b.children[2].textContent.trim().toLowerCase();
          if (distA < distB) return ascDistrict ? -1 : 1;
          if (distA > distB) return ascDistrict ? 1 : -1;
          return 0;
        });
        ascDistrict = !ascDistrict;
        rows.forEach(row => tbody.appendChild(row));
        districtHeader.innerHTML = ascDistrict ? 'District &#8593;' : 'District &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // Laptop header click event
      laptopHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Laptop brand is in the first line of the 5th cell (index 4)
          const getBrand = (row) => {
            const cell = row.children[3];
            // Extract the brand from the first <strong>Brand:</strong> ...<br><br> or ...
            const html = cell.innerHTML;
            const match = html.match(/<strong>Brand:<\/strong>\s*([^<]*)/i);
            return match ? match[1].trim().toLowerCase() : '';
          };
          const brandA = getBrand(a);
          const brandB = getBrand(b);
          // Place empty values after non-empty values
          if (!brandA && brandB) return 1;
          if (brandA && !brandB) return -1;
          if (!brandA && !brandB) return 0;
          if (brandA < brandB) return ascLaptop ? -1 : 1;
          if (brandA > brandB) return ascLaptop ? 1 : -1;
          return 0;
        });
        ascLaptop = !ascLaptop;
        rows.forEach(row => tbody.appendChild(row));
        laptopHeader.innerHTML = ascLaptop ? 'Laptop &#8593;' : 'Laptop &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
      });
      // Interface header click event
      interfaceHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Interface qty is in the 6th cell (index 5), look for Qty: <number>
          const getQty = (row) => {
            const cell = row.children[4];
            const match = cell.innerHTML.match(/<strong>Qty:<\/strong>\s*(\d+)/i);
            return match ? parseInt(match[1], 10) : null;
          };
          const qtyA = getQty(a);
          const qtyB = getQty(b);
          // Place empty values after non-empty values
          if ((qtyA === null || isNaN(qtyA)) && (qtyB !== null && !isNaN(qtyB))) return 1;
          if ((qtyB === null || isNaN(qtyB)) && (qtyA !== null && !isNaN(qtyA))) return -1;
          if ((qtyA === null || isNaN(qtyA)) && (qtyB === null || isNaN(qtyB))) return 0;
          return ascInterface ? qtyA - qtyB : qtyB - qtyA;
        });
        ascInterface = !ascInterface;
        rows.forEach(row => tbody.appendChild(row));
        interfaceHeader.innerHTML = ascInterface ? 'Interface Box &#8593;' : 'Interface Box &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // Monitor header click event
      const monitorHeader = document.querySelector('th:nth-child(7)');
      let ascMonitor = false;
      monitorHeader.style.cursor = 'pointer';
      monitorHeader.style.userSelect = 'none';
      monitorHeader.innerHTML = 'Monitor &#8597;';
      monitorHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Monitor size is in the 8th cell (index 7), look for Size: <number>
          const getSize = (row) => {
            const cell = row.children[6];
            const match = cell.innerHTML.match(/<strong>Size:<\/strong>\s*([\d.]+)/i);
            return match ? parseFloat(match[1]) : null;
          };
          const sizeA = getSize(a);
          const sizeB = getSize(b);
          // Place empty values after non-empty values
          if ((sizeA === null || isNaN(sizeA)) && (sizeB !== null && !isNaN(sizeB))) return 1;
          if ((sizeB === null || isNaN(sizeB)) && (sizeA !== null && !isNaN(sizeA))) return -1;
          if ((sizeA === null || isNaN(sizeA)) && (sizeB === null || isNaN(sizeB))) return 0;
          return ascMonitor ? sizeA - sizeB : sizeB - sizeA;
        });
        ascMonitor = !ascMonitor;
        rows.forEach(row => tbody.appendChild(row));
        monitorHeader.innerHTML = ascMonitor ? 'Monitor &#8593;' : 'Monitor &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // Projector header click event
      const projectorHeader = document.querySelector('th:nth-child(8)');
      let ascProjectorRes = false;
      projectorHeader.style.cursor = 'pointer';
      projectorHeader.style.userSelect = 'none';
      projectorHeader.innerHTML = 'Projector &#8597;';
      projectorHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Projector resolution is in the 9th cell (index 8), look for Res: <value>
          const getRes = (row) => {
            const cell = row.children[7];
            const match = cell.innerHTML.match(/<strong>Res:<\/strong>\s*([^<]*)/i);
            return match ? match[1].trim().toLowerCase() : '';
          };
          const resA = getRes(a);
          const resB = getRes(b);
          // Place empty values after non-empty values
          if (!resA && resB) return 1;
          if (resA && !resB) return -1;
          if (!resA && !resB) return 0;
          if (resA < resB) return ascProjectorRes ? -1 : 1;
          if (resA > resB) return ascProjectorRes ? 1 : -1;
          return 0;
        });
        ascProjectorRes = !ascProjectorRes;
        rows.forEach(row => tbody.appendChild(row));
        projectorHeader.innerHTML = ascProjectorRes ? 'Projector &#8593;' : 'Projector &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
        monitorHeader.innerHTML = 'Monitor &#8597;';
      });
      // Powerstrip header click event
      const powerstripHeader = document.querySelector('th:nth-child(9)');
      let ascPowerstripPlugs = false;
      powerstripHeader.style.cursor = 'pointer';
      powerstripHeader.style.userSelect = 'none';
      powerstripHeader.innerHTML = 'Powerstrip &#8597;';
      powerstripHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Powerstrip plugs is in the 10th cell (index 9), look for Plugs: <number>
          const getPlugs = (row) => {
            const cell = row.children[8];
            const match = cell.innerHTML.match(/<strong>Plugs:<\/strong>\s*(\d+)/i);
            return match ? parseInt(match[1], 10) : null;
          };
          const plugsA = getPlugs(a);
          const plugsB = getPlugs(b);
          // Place empty values after non-empty values
          if ((plugsA === null || isNaN(plugsA)) && (plugsB !== null && !isNaN(plugsB))) return 1;
          if ((plugsB === null || isNaN(plugsB)) && (plugsA !== null && !isNaN(plugsA))) return -1;
          if ((plugsA === null || isNaN(plugsA)) && (plugsB === null || isNaN(plugsB))) return 0;
          return ascPowerstripPlugs ? plugsA - plugsB : plugsB - plugsA;
        });
        ascPowerstripPlugs = !ascPowerstripPlugs;
        rows.forEach(row => tbody.appendChild(row));
        powerstripHeader.innerHTML = ascPowerstripPlugs ? 'Powerstrip &#8593;' : 'Powerstrip &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
        monitorHeader.innerHTML = 'Monitor &#8597;';
        projectorHeader.innerHTML = 'Projector &#8597;';
      });
      // Extension Cord header click event
      const extensionHeader = document.querySelector('th:nth-child(10)');
      let ascExtensionLength = false;
      extensionHeader.style.cursor = 'pointer';
      extensionHeader.style.userSelect = 'none';
      extensionHeader.innerHTML = 'Extension Cord &#8597;';
      extensionHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Extension length is in the 11th cell (index 10), look for Length: <number>
          const getLength = (row) => {
            const cell = row.children[9];
            const match = cell.innerHTML.match(/<strong>Length:<\/strong>\s*([\d.]+)/i);
            return match ? parseFloat(match[1]) : null;
          };
          const lenA = getLength(a);
          const lenB = getLength(b);
          // Place empty values after non-empty values
          if ((lenA === null || isNaN(lenA)) && (lenB !== null && !isNaN(lenB))) return 1;
          if ((lenB === null || isNaN(lenB)) && (lenA !== null && !isNaN(lenA))) return -1;
          if ((lenA === null || isNaN(lenA)) && (lenB === null || isNaN(lenB))) return 0;
          return ascExtensionLength ? lenA - lenB : lenB - lenA;
        });
        ascExtensionLength = !ascExtensionLength;
        rows.forEach(row => tbody.appendChild(row));
        extensionHeader.innerHTML = ascExtensionLength ? 'Extension Cord &#8593;' : 'Extension Cord &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
        monitorHeader.innerHTML = 'Monitor &#8597;';
        projectorHeader.innerHTML = 'Projector &#8597;';
        powerstripHeader.innerHTML = 'Powerstrip &#8597;';
      });
      // Microphone/Recorder header click event
      const micHeader = document.querySelector('th:nth-child(11)');
      let ascMicQty = false;
      micHeader.style.cursor = 'pointer';
      micHeader.style.userSelect = 'none';
      micHeader.innerHTML = 'Microphone/Recorder &#8595;';
      micHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Mic qty is in the 12th cell (index 11), look for Qty: <number>
          const getQty = (row) => {
            const cell = row.children[10];
            const match = cell.innerHTML.match(/<strong>Qty:<\/strong>\s*(\d+)/i);
            return match ? parseInt(match[1], 10) : null;
          };
          const qtyA = getQty(a);
          const qtyB = getQty(b);
          // Place empty values after non-empty values
          if ((qtyA === null || isNaN(qtyA)) && (qtyB !== null && !isNaN(qtyB))) return 1;
          if ((qtyB === null || isNaN(qtyB)) && (qtyA !== null && !isNaN(qtyA))) return -1;
          if ((qtyA === null || isNaN(qtyA)) && (qtyB === null || isNaN(qtyB))) return 0;
          return ascMicQty ? qtyA - qtyB : qtyB - qtyA;
        });
        ascMicQty = !ascMicQty;
        rows.forEach(row => tbody.appendChild(row));
        micHeader.innerHTML = ascMicQty ? 'Microphone/Recorder &#8593;' : 'Microphone/Recorder &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
        monitorHeader.innerHTML = 'Monitor &#8597;';
        projectorHeader.innerHTML = 'Projector &#8597;';
        powerstripHeader.innerHTML = 'Powerstrip &#8597;';
        extensionHeader.innerHTML = 'Extension Cord &#8597;';
      });
      // Other header click event
      const otherHeader = document.querySelector('th:nth-child(12)');
      let ascOtherQty = false;
      otherHeader.style.cursor = 'pointer';
      otherHeader.style.userSelect = 'none';
      otherHeader.innerHTML = 'Other &#8597;';
      otherHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Other qty is in the 13th cell (index 12), look for Qty: <number>
          const getQty = (row) => {
            const cell = row.children[11];
            const match = cell.innerHTML.match(/<strong>Qty:<\/strong>\s*(\d+)/i);
            return match ? parseInt(match[1], 10) : null;
          };
          const qtyA = getQty(a);
          const qtyB = getQty(b);
          // Place empty values after non-empty values
          if ((qtyA === null || isNaN(qtyA)) && (qtyB !== null && !isNaN(qtyB))) return 1;
          if ((qtyB === null || isNaN(qtyB)) && (qtyA !== null && !isNaN(qtyA))) return -1;
          if ((qtyA === null || isNaN(qtyA)) && (qtyB === null || isNaN(qtyB))) return 0;
          return ascOtherQty ? qtyA - qtyB : qtyB - qtyA;
        });
        ascOtherQty = !ascOtherQty;
        rows.forEach(row => tbody.appendChild(row));
        otherHeader.innerHTML = ascOtherQty ? 'Other &#8593;' : 'Other &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Contact &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
        monitorHeader.innerHTML = 'Monitor &#8597;';
        projectorHeader.innerHTML = 'Projector &#8597;';
        powerstripHeader.innerHTML = 'Powerstrip &#8597;';
        extensionHeader.innerHTML = 'Extension Cord &#8597;';
        micHeader.innerHTML = 'Microphone/Recorder &#8597;';
      });
      // Status filter logic (CLEANED UP: use only the button in HTML, do not create/append a new one)
      const statusFilterBtn = document.getElementById('status-filter-btn');
      // Status grouping order
      const statusOrder = [
        'In Inventory',
        'In Room',
        'Used in Tech Room',
        'Broken (In Inventory)',
        'Other',
        '' // No status set
      ];
      let statusGrouped = false;
      statusFilterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        // Get status for each row
        rows.forEach(row => {
          // Status cell is 13th (index 13)
          const select = row.children[13].querySelector('select');
          row._statusValue = select ? select.value.trim() : '';
        });
        if (!statusGrouped) {
          // Sort rows by status order
          rows.sort((a, b) => {
            const aStatus = a._statusValue;
            const bStatus = b._statusValue;
            const aIdx = statusOrder.indexOf(aStatus) !== -1 ? statusOrder.indexOf(aStatus) : statusOrder.length;
            const bIdx = statusOrder.indexOf(bStatus) !== -1 ? statusOrder.indexOf(bStatus) : statusOrder.length;
            return aIdx - bIdx;
          });
          statusGrouped = true;
          statusFilterBtn.style.color = '#fff';
        } else {
          // Restore to original order (by ID descending)
          rows.sort((a, b) => parseInt(b.children[0].textContent, 10) - parseInt(a.children[0].textContent, 10));
          statusGrouped = false;
          statusFilterBtn.style.color = '#7fd7ff';
        }
        rows.forEach(row => tbody.appendChild(row));
      });
    });
    // Notes save AJAX
    document.querySelectorAll('.save-notes-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const itemkey = this.getAttribute('data-itemkey');
        const textarea = this.parentElement.querySelector('textarea');
        const status = this.parentElement.querySelector('.notes-status');
        const notes = textarea.value;
        status.textContent = 'Saving...';
        fetch('save-notes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'itemkey=' + encodeURIComponent(itemkey) + '&notes=' + encodeURIComponent(notes)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            status.textContent = 'Saved!';
            setTimeout(() => { status.textContent = ''; }, 1500);
          } else {
            status.textContent = 'Error saving';
          }
        })
        .catch(() => { status.textContent = 'Error saving'; });
      });
    });
    // Status save AJAX
    document.querySelectorAll('.status-dropdown').forEach(function(drop) {
      drop.addEventListener('change', function() {
        const itemkey = this.getAttribute('data-itemkey');
        const status = this.value;
        const msg = this.parentElement.querySelector('.status-save-msg');
        msg.textContent = 'Saving...';
        fetch('save-status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'itemkey=' + encodeURIComponent(itemkey) + '&status=' + encodeURIComponent(status)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            msg.textContent = 'Saved!';
            setTimeout(() => { msg.textContent = ''; }, 1500);
          } else {
            msg.textContent = 'Error saving';
          }
        })
        .catch(() => { msg.textContent = 'Error saving'; });
      });
    });
  </script>
</body>
</html>
