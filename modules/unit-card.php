<?php
// modules/unit-card.php

// ---- Buffer and then clean on AJAX so JSON stays pure ----
ob_start();

// 0) Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1) DB connection
if (!isset($link)) {
    require_once __DIR__ . '/../layouts/config.php';
}

// 2) Unit ID
$unit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ---- AJAX update handler (runs before any HTML) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();
    header('Content-Type: application/json');

    $payload = json_decode(file_get_contents('php://input'), true);
    $id      = isset($payload['id']) ? (int)$payload['id'] : 0;
    if ($id !== $unit_id || $unit_id < 1) {
        echo json_encode(['success'=>false,'error'=>'Invalid unit ID']);
        exit;
    }

    $allowed = [
        'project_title','unit_status','client_name',
        'project_site','phase','block','lot','lot_class',
        'lot_area','price_per_sqm','date_of_reservation',
        'total_contract_price','additional_misc_fee','reservation_fee',
        'interest','net_selling_price','total_amount_payable',
        'monthly_amortization','amortization_start_date',
        'payment_terms','balance_payable'
    ];
    $numericFields = [
        'lot_area','price_per_sqm','total_contract_price',
        'additional_misc_fee','reservation_fee','interest',
        'net_selling_price','total_amount_payable',
        'monthly_amortization','balance_payable'
    ];

    $sets = []; $params = []; $types = '';
    foreach ($allowed as $field) {
        if (array_key_exists($field, $payload)) {
            $val = $payload[$field];
            if (in_array($field, $numericFields, true)) {
                $val = (float)str_replace(',', '', $val);
                $types .= 'd';
            } elseif ($field === 'payment_terms') {
                $val = (int)$val;
                $types .= 'i';
            } else {
                $val = trim((string)$val);
                $types .= 's';
            }
            $sets[]   = "`{$field}` = ?";
            $params[] = $val;
        }
    }

    if (empty($sets)) {
        echo json_encode(['success'=>false,'error'=>'No valid fields']);
        exit;
    }

    $sql      = "UPDATE `units` SET " . implode(', ', $sets) . " WHERE `id` = ?";
    $types   .= 'i';
    $params[] = $unit_id;

    $stmt = $link->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'error'=>$stmt->error]);
    }
    exit;
}

// ---- GET: render card ----
if ($unit_id < 1) {
    echo '<div class="alert alert-danger">Invalid unit ID.</div>';
    exit;
}

$stmt = $link->prepare("
  SELECT
    unit_status, project_title, project_site, phase,
    block, lot, lot_class, lot_area, price_per_sqm,
    date_of_reservation, total_contract_price,
    additional_misc_fee, reservation_fee, interest,
    net_selling_price, total_amount_payable,
    monthly_amortization, amortization_start_date,
    payment_terms, client_name, balance_payable,
    view_360_link
  FROM units
  WHERE id = ?
");
$stmt->bind_param('i',$unit_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo '<div class="alert alert-danger">Unit not found.</div>';
    exit;
}
$u = $res->fetch_assoc();
$stmt->close();
?>

<div class="card mx-auto" style="max-width:600px;">
  <div class="text-center mt-3">
    <i class="bi bi-house-fill" style="font-size:2rem"></i>
  </div>
  <div class="card-body">
    <h4 class="card-title mb-1">
      <span class="editable" data-field="project_title">
        <?= htmlspecialchars($u['project_title'])?:'&ndash;' ?>
      </span>
    </h4>
    <h6 class="card-subtitle text-muted mb-3">
      Status:
      <span class="editable" data-field="unit_status">
        <?= htmlspecialchars($u['unit_status'])?:'&ndash;' ?>
      </span>
      <?php if ($u['client_name']): ?>
        &nbsp;|&nbsp; Client:
        <span class="editable" data-field="client_name">
          <?= htmlspecialchars($u['client_name']) ?>
        </span>
      <?php endif; ?>
    </h6>

    <ul class="list-group list-group-flush text-start">
      <?php
      $fields = [
        'Project Site'=>'project_site',
        'Phase'       =>'phase',
        'Block'       =>'block',
        'Lot'         =>'lot',
        'Lot Class'   =>'lot_class',
        'Area (sqm)'  =>'lot_area',
        'Price / sqm' =>'price_per_sqm',
        'Reservation Date'=>'date_of_reservation',
        'Contract Price'=>'total_contract_price',
        'Misc Fee'    =>'additional_misc_fee',
        'Reservation Fee'=>'reservation_fee',
        'Interest'    =>'interest',
        'Net Selling Price'=>'net_selling_price',
        'Total Payable'=>'total_amount_payable',
        'Monthly Amortization'=>'monthly_amortization',
        'Amortization Start'=>'amortization_start_date',
        'Payment Terms (mo)'=>'payment_terms',
        'Balance Payable'=>'balance_payable',
      ];
      foreach ($fields as $label=>$field):
        $val = $u[$field];
        if (in_array($field, ['lot_area','price_per_sqm','total_contract_price','additional_misc_fee','reservation_fee','interest','net_selling_price','total_amount_payable','monthly_amortization','balance_payable'],true)) {
          $display = number_format($val,2);
        } elseif ($field==='payment_terms') {
          $display = intval($val);
        } else {
          $display = htmlspecialchars($val?:'&ndash;');
        }
      ?>
        <li class="list-group-item">
          <strong><?= $label ?>:</strong>
          <span class="editable" data-field="<?= $field ?>">
            <?= $display ?>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-3 d-flex justify-content-between align-items-center">
      <?php if ($u['view_360_link']): ?>
        <a href="<?= htmlspecialchars($u['view_360_link']) ?>"
           target="_blank" class="btn btn-info">
          View 360°
        </a>
      <?php else: ?>
        <div></div>
      <?php endif; ?>
      <button id="saveButton" class="btn btn-success" style="display:none">
        Save Changes
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const editables = document.querySelectorAll('.editable');
  const saveBtn   = document.getElementById('saveButton');
  let hasChanges  = false;

  editables.forEach(el => {
    el.addEventListener('click', () => {
      el.contentEditable = 'true';
      el.classList.add('border','rounded','px-1');
      el.focus();
    });
    el.addEventListener('input', () => {
      if (!hasChanges) {
        hasChanges = true;
        saveBtn.style.display = 'inline-block';
      }
    });
    el.addEventListener('blur', () => {
      el.classList.remove('border','rounded','px-1');
    });
  });

  saveBtn.addEventListener('click', () => {
    const data = { id: <?= $unit_id ?> };
    editables.forEach(el => {
      data[el.dataset.field] = el.innerText.trim();
    });

    // **Relative** path (no leading slash)
    fetch(`modules/unit-card.php?id=<?= $unit_id ?>`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(data),
    })
    .then(res => res.text().then(txt => {
      try {
        return JSON.parse(txt);
      } catch(e) {
        console.error('Bad JSON:', txt);
        throw new Error('Unexpected server response');
      }
    }))
    .then(json => {
      if (json.success) {
        editables.forEach(e => e.contentEditable = 'false');
        saveBtn.style.display = 'none';
        hasChanges = false;
      } else {
        alert('Save error: ' + json.error);
      }
    })
    .catch(err => {
      console.error('Save failed:', err);
      alert('Error saving changes; see console for details.');
    });
  });
});
</script>
