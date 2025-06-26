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

// Fetch all per-item statuses and notes
$status_map = [];
$notes_map = [];
$status_result = $conn->query("SELECT itemkey, status, notes FROM equipment_item_status");
if ($status_result && $status_result->num_rows > 0) {
  while ($row = $status_result->fetch_assoc()) {
    $status_map[$row['itemkey']] = $row['status'];
    $notes_map[$row['itemkey']] = $row['notes'];
  }
}

// Build a multi-dimensional array to group pads by registration
$registrations = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $reg_id = $row['id'];
    if (!is_numeric($reg_id) || $reg_id === null || $reg_id === '') {
      continue; // Skip rows with missing or invalid id
    }
    if (!isset($registrations[$reg_id])) {
      // Only set all fields the first time
      $registrations[$reg_id] = $row;
      $registrations[$reg_id]['pads'] = [];
      $registrations[$reg_id]['notes'] = isset($row['notes']) ? $row['notes'] : '';
    }
    // Only add pads, do not overwrite any other fields
    if ($row['pad_color'] !== null) {
      $registrations[$reg_id]['pads'][] = [
        'pad_color' => $row['pad_color'],
        'pad_qty' => $row['pad_qty']
      ];
    }
  }
  // After building, ensure notes is set for all registrations (in case of missing notes in some join rows)
  foreach ($registrations as $id => &$reg) {
    if (!isset($reg['notes'])) {
      $reg['notes'] = '';
    }
  }
  unset($reg);
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
      font-size: 1em;
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
      // Helper to output a row for a single item, now with per-row status and notes
      function output_item_row($reg, $item_type, $item_data, $show_notes_status = false, $item_index = 0) {
        global $status_map, $notes_map;
        $item_key = $reg['id'] . '_' . $item_type . '_' . $item_index;
        echo "<tr>";
        // ID
        echo '<td>' . htmlspecialchars($reg['id']) . '</td>';
        // Contact
        echo '<td><strong>Name:</strong> ' . htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) . '<br>';
        if ($reg['phone']) echo '<strong>Phone: </strong>' . htmlspecialchars($reg['phone']) . '<br>';
        if ($reg['email']) echo '<strong>Email: </strong>' . htmlspecialchars($reg['email']);
        echo '</td>';
        // District
        echo '<td>' . htmlspecialchars($reg['district']) . '</td>';
        // Laptop
        echo '<td>';
        if ($item_type === 'laptop') {
          if ($reg['laptop_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['laptop_brand']) . '<br><br>';
          if ($reg['laptop_os']) echo '<strong>OS:</strong> ' . htmlspecialchars($reg['laptop_os']) . '<br><br>';
          if ($reg['laptop_parallel_port']) echo '<strong>Parallel:</strong> ' . htmlspecialchars($reg['laptop_parallel_port']) . '<br><br>';
          if ($reg['laptop_qm_version']) echo '<strong>QM Ver:</strong> ' . htmlspecialchars($reg['laptop_qm_version']) . '<br><br>';
          if ($reg['laptop_username']) echo '<strong>User:</strong> ' . htmlspecialchars($reg['laptop_username']) . '<br><br>';
          if ($reg['laptop_password']) echo '<strong>Pass:</strong> ' . htmlspecialchars($reg['laptop_password']);
        }
        echo '</td>';
        // Interface Box
        echo '<td>';
        if ($item_type === 'interface') {
          if ($reg['interface_type']) echo '<strong>Type:</strong> ' . htmlspecialchars($reg['interface_type']) . '<br><br>';
          if ($item_data !== null) echo '<strong>Qty:</strong> 1';
        }
        echo '</td>';
        // Pads
        echo '<td class="pad-list">';
        if ($item_type === 'pad' && $item_data) {
          echo '<ul style="margin:0; padding-left:18px;"><li>' . htmlspecialchars($item_data['pad_color']) . ' (1)</li></ul>';
        }
        echo '</td>';
        // Monitor
        echo '<td>';
        if ($item_type === 'monitor') {
          if ($reg['monitor_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['monitor_brand']) . '<br><br>';
          if ($reg['monitor_size']) echo '<strong>Size:</strong> ' . htmlspecialchars($reg['monitor_size']) . '<br><br>';
          if ($reg['monitor_resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($reg['monitor_resolution']);
        }
        echo '</td>';
        // Projector
        echo '<td>';
        if ($item_type === 'projector') {
          if ($reg['projector_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['projector_brand']) . '<br><br>';
          if ($reg['projector_lumens'] !== null && $reg['projector_lumens'] !== '') echo '<strong>Lumens:</strong> ' . htmlspecialchars($reg['projector_lumens']) . '<br><br>';
          if ($reg['projector_resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($reg['projector_resolution']) . '<br><br>';
        }
        echo '</td>';
        // Powerstrip
        echo '<td>';
        if ($item_type === 'powerstrip') {
          if ($reg['powerstrip_make']) echo '<strong>Make:</strong> ' . htmlspecialchars($reg['powerstrip_make']) . '<br><br>';
          if ($reg['powerstrip_model']) echo '<strong>Model:</strong> ' . htmlspecialchars($reg['powerstrip_model']) . '<br><br>';
          if ($reg['powerstrip_color']) echo '<strong>Color:</strong> ' . htmlspecialchars($reg['powerstrip_color']) . '<br><br>';
          if ($reg['powerstrip_outlets'] !== null && $reg['powerstrip_outlets'] !== '') echo '<strong>Plugs:</strong> ' . htmlspecialchars($reg['powerstrip_outlets']);
        }
        echo '</td>';
        // Extension Cord
        echo '<td>';
        if ($item_type === 'extension') {
          if ($reg['extension_color']) echo '<strong>Color:</strong> ' . htmlspecialchars($reg['extension_color']) . '<br><br>';
          if ($reg['extension_length'] !== null && $reg['extension_length'] !== '') echo '<strong>Length:</strong> ' . htmlspecialchars($reg['extension_length']);
        }
        echo '</td>';
        // Microphone/Recorder
        echo '<td>';
        if ($item_type === 'mic') {
          if ($reg['mic_type']) echo '<strong>Type:</strong> ' . htmlspecialchars($reg['mic_type']) . '<br><br>';
          if ($reg['mic_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['mic_brand']) . '<br><br>';
          if ($reg['mic_model']) echo '<strong>Model:</strong> ' . htmlspecialchars($reg['mic_model']) . '<br><br>';
        }
        echo '</td>';
        // Other
        echo '<td>';
        if ($item_type === 'other') {
          if ($reg['other_desc']) echo '<strong>Desc:</strong> ' . nl2br(htmlspecialchars($reg['other_desc'])) . '<br><br>';
        }
        echo '</td>';
        // Status (per row)
        echo '<td>';
        $statuses = [
          '',
          'In Room',
          'In Inventory',
          'Used in Tech Room',
          'Broken (In Inventory)',
          'Other'
        ];
        // Use per-item status if available
        $currentStatus = isset($status_map[$item_key]) ? $status_map[$item_key] : '';
        echo '<select class="status-dropdown" data-itemkey="' . htmlspecialchars($item_key) . '" style="width: 150px; background: #181c22; color: #fff; border: 1px solid #444; border-radius: 4px;">';
        foreach ($statuses as $status) {
          $selected = ($currentStatus === $status) ? 'selected' : '';
          $label = $status === '' ? '-- Select --' : $status;
          echo '<option value="' . htmlspecialchars($status) . '" ' . $selected . '>' . $label . '</option>';
        }
        echo '</select>';
        echo '<span class="status-save-msg" style="font-size:0.9em; margin-left:6px;"></span>';
        echo '</td>';
        // Notes (now on every row, per itemkey)
        echo '<td>';
        $currentNotes = isset($notes_map[$item_key]) ? $notes_map[$item_key] : '';
        echo '<textarea style="width: 160px; min-height: 40px; background: #181c22; color: #fff; border: 1px solid #444; border-radius: 4px; resize: vertical;" data-itemkey="' . htmlspecialchars($item_key) . '">' . htmlspecialchars($currentNotes) . '</textarea>';
        echo '<button class="save-notes-btn" data-itemkey="' . htmlspecialchars($item_key) . '" style="margin-top: 4px; background: #23272b; color: #7fd7ff; border: 1px solid #7fd7ff; border-radius: 4px; cursor: pointer;">Save</button>';
        echo '<span class="notes-status" style="font-size:0.9em; margin-left:6px;"></span>';
        echo '</td>';
        echo "</tr>\n";
      }
      foreach ($registrations as $reg) {
        $output_any = false;
        $item_index = 0;
        // Laptops (1 row if present)
        if ($reg['laptop_brand'] || $reg['laptop_os'] || $reg['laptop_parallel_port'] || $reg['laptop_qm_version'] || $reg['laptop_username'] || $reg['laptop_password']) {
          output_item_row($reg, 'laptop', null, !$output_any, $item_index++);
          $output_any = true;
        }
        // Interface boxes (one row per quantity)
        $interface_qty = isset($reg['interface_qty']) ? intval($reg['interface_qty']) : 0;
        if ($interface_qty > 0) {
          for ($i = 0; $i < $interface_qty; $i++) {
            output_item_row($reg, 'interface', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Pads (one row per pad, per quantity)
        if (!empty($reg['pads'])) {
          foreach ($reg['pads'] as $pad) {
            $pad_qty = isset($pad['pad_qty']) ? intval($pad['pad_qty']) : 0;
            for ($i = 0; $i < $pad_qty; $i++) {
              output_item_row($reg, 'pad', $pad, !$output_any, $item_index++);
              $output_any = true;
            }
          }
        }
        // Monitors (one row per quantity)
        $monitor_qty = isset($reg['monitor_qty']) ? intval($reg['monitor_qty']) : 0;
        if ($monitor_qty > 0) {
          for ($i = 0; $i < $monitor_qty; $i++) {
            output_item_row($reg, 'monitor', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Projectors (one row per quantity)
        $projector_qty = isset($reg['projector_qty']) ? intval($reg['projector_qty']) : 0;
        if ($projector_qty > 0) {
          for ($i = 0; $i < $projector_qty; $i++) {
            output_item_row($reg, 'projector', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Powerstrips (one row per quantity)
        $powerstrip_qty = isset($reg['powerstrip_qty']) ? intval($reg['powerstrip_qty']) : 0;
        if ($powerstrip_qty > 0) {
          for ($i = 0; $i < $powerstrip_qty; $i++) {
            output_item_row($reg, 'powerstrip', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Extension cords (one row per quantity)
        $extension_qty = isset($reg['extension_qty']) ? intval($reg['extension_qty']) : 0;
        if ($extension_qty > 0) {
          for ($i = 0; $i < $extension_qty; $i++) {
            output_item_row($reg, 'extension', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Microphone/Recorder (one row per quantity)
        $mic_qty = isset($reg['mic_qty']) ? intval($reg['mic_qty']) : 0;
        if ($mic_qty > 0) {
          for ($i = 0; $i < $mic_qty; $i++) {
            output_item_row($reg, 'mic', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // Other (one row per quantity)
        $other_qty = isset($reg['other_qty']) ? intval($reg['other_qty']) : 0;
        if ($other_qty > 0) {
          for ($i = 0; $i < $other_qty; $i++) {
            output_item_row($reg, 'other', 1, !$output_any, $item_index++);
            $output_any = true;
          }
        }
        // If no items at all, output a blank row with notes/status
        if (!$output_any) {
          output_item_row($reg, '', null, true, $item_index++);
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
        // Separate rows with at least one pad of the color, and those with none/blank
        const withColor = [];
        const withoutColor = [];
        rows.forEach(row => {
          const padCell = row.children[6];
          let found = false;
          padCell.querySelectorAll('li').forEach(li => {
            if (li.textContent.trim().toLowerCase().startsWith(color.toLowerCase())) {
              found = true;
            }
          });
          if (found) {
            withColor.push(row);
          } else {
            withoutColor.push(row);
          }
        });
        // Re-append rows: withColor first, then withoutColor
        withColor.forEach(row => tbody.appendChild(row));
        withoutColor.forEach(row => tbody.appendChild(row));
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
    // Notes save AJAX (update to use itemkey)
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
