<?php
// ---- Place this at the TOP of your PHP file before the HTML (setup demo fetch) ----
require_once __DIR__ . '/layouts/config.php'; // adjust path as needed

$contact_id = 1; // or whatever entity_id you want to display
$timeline = [];
$sql = "SELECT * FROM activity_timeline WHERE entity_type='contact' AND entity_id=? ORDER BY created_at DESC";
$stmt = $link->prepare($sql);
$stmt->bind_param('i', $contact_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $timeline[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Timeline Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7fafd; }
        .nav-tabs { border-bottom: none; }
        .tab-custom .nav-link {
            color: #40516a;
            font-weight: 500;
            border: none;
            background: transparent;
            margin-right: 20px;
            padding-bottom: 8px;
        }

        body, html {
            max-width: 100vw;
            overflow-x: hidden !important;
        }
        .tab-custom .nav-link.active {
            color: #32475b;
            border-bottom: 4px solid #3d5a80;
            background: transparent;
        }
        .tab-content {
            background: #f7fafd;
            padding: 0px 0 0 0;
            min-height: 250px;
        }
        .create-btn {
            float: right;
            margin-top: -42px;
        }
        .activity-timeline .activity-card {
            border-radius: 12px;
            margin-bottom: 18px;
            padding: 20px 24px 16px 20px;
            background: #fff;
            box-shadow: 0 2px 8px 0 rgba(60,72,88,0.04);
            border-left: 5px solid #3d5a80;
            transition: box-shadow .2s;
        }
        .activity-timeline .activity-card .activity-type {
            font-size: 0.95rem;
            font-weight: 600;
            color: #3d5a80;
            text-transform: capitalize;
            margin-bottom: 2px;
        }
        .activity-timeline .activity-card .activity-title {
            font-weight: 500;
            color: #212b36;
            font-size: 1.09rem;
        }
        .activity-timeline .activity-card .activity-details {
            font-size: 0.98rem;
            color: #5d6d7e;
            margin-top: 6px;
            margin-bottom: 4px;
        .activity-timeline .activity-card .activity-time {
            font-size: 0.95rem;
            color: #6c757d;
            margin-top: 0;
            margin-left: 12px;
            white-space: nowrap;
        }
        @media (max-width: 576px) {
            .create-btn { float: none; margin: 16px 0 0 0; width: 100%; }
            .activity-timeline .activity-card { padding: 14px 8px 10px 10px; }
        }
    </style>
</head>
<body>
<div class="py-2" style="padding-left:30px; padding-right:30px;">
    <div class="row mb-2">
        <div class="col-12">
            <ul class="nav nav-tabs tab-custom" id="activityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">Activity</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="emails-tab" data-bs-toggle="tab" data-bs-target="#emails" type="button" role="tab">Emails</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="calls-tab" data-bs-toggle="tab" data-bs-target="#calls" type="button" role="tab">Calls</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">Tasks</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="meetings-tab" data-bs-toggle="tab" data-bs-target="#meetings" type="button" role="tab">Meetings</button>
                </li>
            </ul>
            <div class="row mb-3">
                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary create-btn" id="createBtn" style="margin-top:20px;">Create Activity</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content" id="activityTabsContent">
        <div class="tab-pane fade show active" id="activity" role="tabpanel">
            <div class="activity-timeline" id="timeline-activity"></div>
        </div>
        <div class="tab-pane fade" id="notes" role="tabpanel">
            <div class="activity-timeline" id="timeline-notes"></div>
        </div>
        <div class="tab-pane fade" id="emails" role="tabpanel">
            <div class="activity-timeline" id="timeline-emails"></div>
        </div>
        <div class="tab-pane fade" id="calls" role="tabpanel">
            <div class="activity-timeline" id="timeline-calls"></div>
        </div>
        <div class="tab-pane fade" id="tasks" role="tabpanel">
            <div class="activity-timeline" id="timeline-tasks"></div>
        </div>
        <div class="tab-pane fade" id="meetings" role="tabpanel">
            <div class="activity-timeline" id="timeline-meetings"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
const labels = {
    'activity': 'New Activity',
    'notes': 'Create Note',
    'emails': 'Log Email',
    'calls': 'Log Call',
    'tasks': 'Create Task',
    'meetings': 'Log Meeting'
};
function updateButtonLabel() {
    let activeTab = $('.tab-custom .nav-link.active').attr('id').replace('-tab','');
    $('#createBtn').text(labels[activeTab]);
}
$('.tab-custom .nav-link').on('shown.bs.tab', function () {
    updateButtonLabel();
});
$(document).ready(function() {
    updateButtonLabel();

    // --- Data from PHP ---
    const timelineData = <?php echo json_encode($timeline, JSON_UNESCAPED_UNICODE); ?>;

    // --- Timeline Card Template ---
    function iconForType(type) {
        // Optionally, return icons per type
        switch(type) {
            case 'note': return '<span class="me-2"><i class="bi bi-journal-text text-primary"></i></span>';
            case 'email': return '<span class="me-2"><i class="bi bi-envelope-at text-success"></i></span>';
            case 'call': return '<span class="me-2"><i class="bi bi-telephone text-info"></i></span>';
            case 'task': return '<span class="me-2"><i class="bi bi-list-task text-warning"></i></span>';
            case 'meeting': return '<span class="me-2"><i class="bi bi-calendar-event text-danger"></i></span>';
            case 'lifecycle_change': return '<span class="me-2"><i class="bi bi-arrow-repeat text-secondary"></i></span>';
            case 'form_submission': return '<span class="me-2"><i class="bi bi-ui-checks-grid text-secondary"></i></span>';
            default: return '<span class="me-2"><i class="bi bi-dot text-muted"></i></span>';
        }
    }

    function timelineCard(item) {
        let type = item.activity_type.replace('_', ' ');
        return `
        <div class="activity-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="activity-type">${iconForType(item.activity_type)}${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                <div class="activity-time text-end">${item.created_at ? new Date(item.created_at).toLocaleString() : ''}</div>
            </div>
            <div class="activity-title">${item.title ? item.title : ''}</div>
            <div class="activity-details">${item.details ? item.details : ''}</div>
        </div>
        `;
    }

    function renderTimeline(filterType, containerId) {
        let items;
        if (filterType === 'all') {
            // All except: filterType tabs only show their own type
            items = timelineData;
        } else {
            items = timelineData.filter(item => item.activity_type === filterType);
        }
        let html = (items.length > 0) ? items.map(timelineCard).join('') : '<div class="text-center text-muted pt-4 pb-5">No activities yet.</div>';
        $(containerId).html(html);
    }

    // Render all on load
    renderTimeline('all', '#timeline-activity');
    renderTimeline('note', '#timeline-notes');
    renderTimeline('email', '#timeline-emails');
    renderTimeline('call', '#timeline-calls');
    renderTimeline('task', '#timeline-tasks');
    renderTimeline('meeting', '#timeline-meetings');
});
</script>
<!-- Add Bootstrap Icons CDN for icons (optional, but makes UI nice) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>
