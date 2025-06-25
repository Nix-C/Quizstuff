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
    if (!is_numeric($reg_id) || $reg_id === null || $reg_id === '') {
      continue; // Skip rows with missing or invalid id
    }
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
    }
    @media (max-width: 800px) {
      table, th, td { font-size: 0.85em; }
      th, td { padding: 5px 3px; }
      h1 { font-size: 1.2em; }
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
        <th id="name-header" style="cursor:pointer; user-select:none;">Name &#8597;</th>
        <th>Contact</th>
        <th id="district-header" style="cursor:pointer; user-select:none;">District &#8597;</th>
        <th id="laptop-header" style="cursor:pointer; user-select:none;">Laptop &#8597;</th>
        <th id="interface-header" style="cursor:pointer; user-select:none;">Interface Box &#8597;</th>
        <th id="pads-header" style="cursor:pointer; user-select:none;">Pads &#128308; <div id="horizontal"></div></th>
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
            <?php if ($reg['phone']) echo '<strong>Phone: </strong>' . htmlspecialchars($reg['phone']) . '<br>'; ?>
            <?php if ($reg['email']) echo '<strong>Email: </strong>' . htmlspecialchars($reg['email']); ?>
          </td>
          <td><?= htmlspecialchars($reg['district']) ?></td>
          <td>
            <?php if ($reg['laptop_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['laptop_brand']) . '<br>'; ?>
            <?php if ($reg['laptop_os']) echo '<strong>OS:</strong> ' . htmlspecialchars($reg['laptop_os']) . '<br>'; ?>
            <?php if ($reg['laptop_parallel_port']) echo '<strong>Parallel:</strong> ' . htmlspecialchars($reg['laptop_parallel_port']) . '<br>'; ?>
            <?php if ($reg['laptop_qm_version']) echo '<strong>QM Ver:</strong> ' . htmlspecialchars($reg['laptop_qm_version']) . '<br>'; ?>
            <?php if ($reg['laptop_username']) echo '<strong>User:</strong> ' . htmlspecialchars($reg['laptop_username']) . '<br>'; ?>
            <?php if ($reg['laptop_password']) echo '<strong>Pass:</strong> ' . htmlspecialchars($reg['laptop_password']); ?>
          </td>
          <td>
            <?php if ($reg['interface_type']) echo '<strong>Type:</strong> ' . htmlspecialchars($reg['interface_type']) . '<br>'; ?>
            <?php if ($reg['interface_qty'] !== null && $reg['interface_qty'] !== '') echo '<strong>Qty:</strong> ' . htmlspecialchars($reg['interface_qty']); ?>
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
            <?php if ($reg['monitor_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['monitor_brand']) . '<br>'; ?>
            <?php if ($reg['monitor_size']) echo '<strong>Size:</strong> ' . htmlspecialchars($reg['monitor_size']) . '<br>'; ?>
            <?php if ($reg['monitor_resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($reg['monitor_resolution']); ?>
          </td>
          <td>
            <?php if ($reg['projector_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['projector_brand']) . '<br>'; ?>
            <?php if ($reg['projector_lumens'] !== null && $reg['projector_lumens'] !== '') echo '<strong>Lumens:</strong> ' . htmlspecialchars($reg['projector_lumens']) . '<br>'; ?>
            <?php if ($reg['projector_resolution']) echo '<strong>Res:</strong> ' . htmlspecialchars($reg['projector_resolution']) . '<br>'; ?>
            <?php if ($reg['projector_qty'] !== null && $reg['projector_qty'] !== '') echo '<strong>Qty:</strong> ' . htmlspecialchars($reg['projector_qty']); ?>
          </td>
          <td>
            <?php if ($reg['powerstrip_make']) echo '<strong>Make:</strong> ' . htmlspecialchars($reg['powerstrip_make']) . '<br>'; ?>
            <?php if ($reg['powerstrip_model']) echo '<strong>Model:</strong> ' . htmlspecialchars($reg['powerstrip_model']) . '<br>'; ?>
            <?php if ($reg['powerstrip_color']) echo '<strong>Color:</strong> ' . htmlspecialchars($reg['powerstrip_color']) . '<br>'; ?>
            <?php if ($reg['powerstrip_outlets'] !== null && $reg['powerstrip_outlets'] !== '') echo '<strong>Plugs:</strong> ' . htmlspecialchars($reg['powerstrip_outlets']); ?>
          </td>
          <td>
            <?php if ($reg['extension_color']) echo '<strong>Color:</strong> ' . htmlspecialchars($reg['extension_color']) . '<br>'; ?>
            <?php if ($reg['extension_length'] !== null && $reg['extension_length'] !== '') echo '<strong>Length:</strong> ' . htmlspecialchars($reg['extension_length']); ?>
          </td>
          <td>
            <?php if ($reg['mic_type']) echo '<strong>Type:</strong> ' . htmlspecialchars($reg['mic_type']) . '<br>'; ?>
            <?php if ($reg['mic_brand']) echo '<strong>Brand:</strong> ' . htmlspecialchars($reg['mic_brand']) . '<br>'; ?>
            <?php if ($reg['mic_model']) echo '<strong>Model:</strong> ' . htmlspecialchars($reg['mic_model']) . '<br>'; ?>
            <?php if ($reg['mic_qty'] !== null && $reg['mic_qty'] !== '') echo '<strong>Qty:</strong> ' . htmlspecialchars($reg['mic_qty']); ?>
          </td>
          <td>
            <?php if ($reg['other_desc']) echo '<strong>Desc:</strong> ' . nl2br(htmlspecialchars($reg['other_desc'])) . '<br>'; ?>
            <?php if ($reg['other_qty'] !== null && $reg['other_qty'] !== '') echo '<strong>Qty:</strong> ' . htmlspecialchars($reg['other_qty']); ?>
          </td>
        </tr>
      <?php endforeach; ?>
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
      padsHeader.horizontal.innerHTML = 'Pads ' + padColors.map(pc => `<button style="margin-left:2px; font-size:1em; background:none; border:none; color:inherit; cursor:pointer;" data-color="${pc.color}">${pc.icon}</button>`).join('');
      padColors.forEach(pc => {
        padsHeader.querySelector(`button[data-color='${pc.color}']`).addEventListener('click', function(e) {
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
        nameHeader.innerHTML = 'Name &#8597;';
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
        nameHeader.innerHTML = ascName ? 'Name &#8593;' : 'Name &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // District header click event
      districtHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const distA = a.children[3].textContent.trim().toLowerCase();
          const distB = b.children[3].textContent.trim().toLowerCase();
          if (distA < distB) return ascDistrict ? -1 : 1;
          if (distA > distB) return ascDistrict ? 1 : -1;
          return 0;
        });
        ascDistrict = !ascDistrict;
        rows.forEach(row => tbody.appendChild(row));
        districtHeader.innerHTML = ascDistrict ? 'District &#8593;' : 'District &#8595;';
        idHeader.innerHTML = 'ID &#8597;';
        nameHeader.innerHTML = 'Name &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
      // Laptop header click event
      laptopHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Laptop brand is in the first line of the 5th cell (index 4)
          const getBrand = (row) => {
            const cell = row.children[4];
            const match = cell.innerHTML.match(/Brand: ([^<]*)/i);
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
        nameHeader.innerHTML = 'Name &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
      });
      // Interface header click event
      interfaceHeader.addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          // Interface qty is in the 6th cell (index 5), look for Qty: <number>
          const getQty = (row) => {
            const cell = row.children[5];
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
        nameHeader.innerHTML = 'Name &#8597;';
        districtHeader.innerHTML = 'District &#8597;';
        laptopHeader.innerHTML = 'Laptop &#8597;';
      });
    });
  </script>
</body>
</html>
