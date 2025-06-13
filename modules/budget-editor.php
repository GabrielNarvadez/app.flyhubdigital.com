<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$categories = ['Food', 'Transport', 'Rent', 'Utilities', 'Entertainment', 'Others'];
if (!isset($_SESSION['budgets'])) {
    $_SESSION['budgets'] = [
        'Food' => 3000,
        'Transport' => 1500,
        'Rent' => 8000,
        'Utilities' => 2000,
        'Entertainment' => 1000,
        'Others' => 500,
    ];
}
if (!isset($_SESSION['entries'])) {
    $_SESSION['entries'] = [
        ['type' => 'income', 'category' => 'Others', 'amount' => 12000, 'date' => '2025-06-01', 'notes' => 'Salary'],
        ['type' => 'expense', 'category' => 'Food', 'amount' => 1500, 'date' => '2025-06-03', 'notes' => 'Groceries'],
        ['type' => 'expense', 'category' => 'Transport', 'amount' => 400, 'date' => '2025-06-04', 'notes' => 'Grab'],
        ['type' => 'expense', 'category' => 'Utilities', 'amount' => 800, 'date' => '2025-06-05', 'notes' => 'Meralco'],
        ['type' => 'expense', 'category' => 'Rent', 'amount' => 8000, 'date' => '2025-06-01', 'notes' => 'Condo'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_entry'])) {
        $entry = [
            'type' => $_POST['type'],
            'category' => $_POST['category'],
            'amount' => floatval($_POST['amount']),
            'date' => $_POST['date'],
            'notes' => trim($_POST['notes']),
        ];
        $_SESSION['entries'][] = $entry;
    } elseif (isset($_POST['update_budget'])) {
        foreach ($categories as $cat) {
            $_SESSION['budgets'][$cat] = floatval($_POST['budget_' . $cat]);
        }
    }
}

$filter_cat = $_GET['filter_cat'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';
$entries = $_SESSION['entries'];
if ($filter_cat) $entries = array_filter($entries, fn($e) => $e['category'] == $filter_cat);
if ($filter_date) $entries = array_filter($entries, fn($e) => $e['date'] == $filter_date);

// Compute totals & per category
$totals = ['income' => 0, 'expense' => 0];
$per_cat = [];
foreach ($categories as $cat) $per_cat[$cat] = ['income' => 0, 'expense' => 0];
foreach ($_SESSION['entries'] as $e) {
    $totals[$e['type']] += $e['amount'];
    $per_cat[$e['category']][$e['type']] += $e['amount'];
}
$net = $totals['income'] - $totals['expense'];
?>

<div class="row g-3">
    <!-- Column 1: Input Form & Budgets -->
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <h5>Add Income/Expense</h5>
            <form method="post" class="mb-2">
                <div class="mb-2">
                    <select name="type" class="form-select" required>
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>
                <div class="mb-2">
                    <select name="category" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?=htmlspecialchars($cat)?>"><?=htmlspecialchars($cat)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="number" name="amount" class="form-control" placeholder="Amount" min="0" step="0.01" required>
                </div>
                <div class="mb-2">
                    <input type="date" name="date" class="form-control" value="<?=date('Y-m-d')?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="notes" class="form-control" placeholder="Notes (optional)">
                </div>
                <button type="submit" name="add_entry" class="btn btn-primary w-100">Add Entry</button>
            </form>
        </div>
        <div class="card p-3">
            <h6>Edit Budgets</h6>
            <form method="post">
                <?php foreach ($categories as $cat): ?>
                    <div class="mb-2 row align-items-center">
                        <label class="col-6 col-form-label"><?=htmlspecialchars($cat)?></label>
                        <div class="col-6">
                            <input type="number" name="budget_<?=$cat?>" class="form-control form-control-sm"
                                   min="0" step="0.01" value="<?=htmlspecialchars($_SESSION['budgets'][$cat])?>">
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_budget" class="btn btn-outline-primary w-100 btn-sm">Update Budgets</button>
            </form>
        </div>
    </div>

    <!-- Column 2: Entry Table with Filter -->
    <div class="col-md-4">
        <div class="card p-3">
            <form method="get" class="row mb-3 g-2">
                <div class="col-5">
                    <select name="filter_cat" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?=$cat?>" <?=($filter_cat==$cat)?'selected':''?>><?=$cat?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-5">
                    <input type="date" name="filter_date" class="form-control form-control-sm" value="<?=htmlspecialchars($filter_date)?>">
                </div>
                <div class="col-2">
                    <button class="btn btn-outline-secondary btn-sm w-100" type="submit">Filter</button>
                </div>
            </form>
            <div class="table-responsive" style="max-height: 360px; overflow-y:auto;">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                    <tr class="table-light">
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($entries as $e): ?>
                        <tr class="<?=$e['type']=='income'?'income-row':'expense-row'?>">
                            <td><?=htmlspecialchars($e['date'])?></td>
                            <td><span class="badge <?=$e['type']=='income'?'bg-success':'bg-warning text-dark'?>"><?=ucfirst($e['type'])?></span></td>
                            <td><?=htmlspecialchars($e['category'])?></td>
                            <td><?=number_format($e['amount'],2)?></td>
                            <td><?=htmlspecialchars($e['notes'])?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($entries)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No entries</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Column 3: Analysis -->
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <h6 class="mb-3">Budget Analysis</h6>
            <ul class="list-group mb-2">
                <?php foreach ($categories as $cat):
                    $spent = $per_cat[$cat]['expense'];
                    $budget = $_SESSION['budgets'][$cat];
                    $percent = $budget>0 ? min(100, round($spent/$budget*100)) : 0;
                    $status = $spent > $budget ? 'budget-over' : 'budget-under';
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?=$status?>">
                        <div>
                            <span class="fw-semibold"><?=$cat?></span><br>
                            <small class="text-muted"><?=$spent ? 'Spent: ₱'.number_format($spent,2) : 'No expense'?></small>
                        </div>
                        <div style="min-width:120px;">
                            <div class="progress" style="height:14px;">
                                <div class="progress-bar <?=$spent>$budget?'bg-danger':'bg-success'?>" role="progressbar" style="width: <?=$percent?>%;"></div>
                            </div>
                            <div class="text-end small"><?=$budget>0 ? ($percent).'%' : ''?></div>
                            <span class="badge bg-light text-dark ms-1"><?=$spent>$budget?'Over':'Within'?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="card p-3">
            <div class="mb-2">
                <strong>Total Income:</strong> ₱<?=number_format($totals['income'],2)?>
            </div>
            <div class="mb-2">
                <strong>Total Expenses:</strong> ₱<?=number_format($totals['expense'],2)?>
            </div>
            <div>
                <strong>Net Balance:</strong>
                <span class="fw-bold <?=($net>=0)?'text-success':'text-danger'?>">₱<?=number_format($net,2)?></span>
            </div>
        </div>
    </div>
</div>
