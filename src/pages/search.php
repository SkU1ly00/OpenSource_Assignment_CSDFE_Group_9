<?php
/**
 * Search Incidents Page (Advanced)
 * Supports simple search by incident ID and advanced filters
 */

session_start();
require_once '../../config/config.php';
require_once '../../src/classes/Database.php';
require_once '../../src/classes/Auth.php';
require_once '../../src/classes/Incident.php';
require_once '../../src/functions.php';

if (!isUserLoggedIn()) {
    redirect('../../login.php');
}

$db = new Database();
$db->connect();

// Fetch filter options
$db->prepare('SELECT id, type_name FROM incident_types ORDER BY type_name');
$incident_types = $db->resultSet();

$db->prepare('SELECT id, level_name FROM severity_levels ORDER BY id');
$severity_levels = $db->resultSet();

$db->prepare('SELECT id, status_name FROM incident_status ORDER BY id');
$status_options = $db->resultSet();

$search_results = [];
$search_performed = false;

$incident = new Incident();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['q']) && !empty($_GET['q']))) {
    // Build filters
    $filters = [];
    $simple_query = sanitizeInput($_GET['q'] ?? $_POST['search_term'] ?? '');

    $filters['incident_id'] = $simple_query;
    $filters['incident_type_id'] = !empty($_POST['incident_type_id']) ? intval($_POST['incident_type_id']) : null;
    $filters['severity_id'] = !empty($_POST['severity_id']) ? intval($_POST['severity_id']) : null;
    $filters['status_id'] = !empty($_POST['status_id']) ? intval($_POST['status_id']) : null;
    $filters['start_date'] = !empty($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : null;
    $filters['end_date'] = !empty($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : null;

    // Determine if any advanced filter is set
    $advanced_set = false;
    foreach (['incident_type_id','severity_id','status_id','start_date','end_date'] as $f) {
        if (!empty($filters[$f])) { $advanced_set = true; break; }
    }

    if ($advanced_set) {
        $search_results = $incident->searchIncidents($filters);
    } else {
        // Simple search by incident id
        if (!empty($simple_query)) {
            $search_results = $incident->searchIncidentById($simple_query);
        }
    }

    $search_performed = true;
}

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Incidents - Security Incident Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .advanced-filters { display: none; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../index.php">
                <i class="bi bi-shield-lock"></i> Security Incident Reporting
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="incidents.php">All Incidents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_incident.php">Report Incident</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="search.php">Search</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($current_user['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="bi bi-search"></i> Search Incidents</h2>
                <p class="text-muted">Search incidents by incident ID or use advanced filters</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="search.php" class="row g-3">
                    <div class="col-12 col-md-6">
                        <input type="text" class="form-control form-control-lg" name="search_term" placeholder="Enter Incident ID (e.g., INC...)" value="<?php echo htmlspecialchars($_POST['search_term'] ?? $_GET['q'] ?? ''); ?>">
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <a href="#" id="toggleAdvanced" class="btn btn-outline-secondary">Advanced Filters</a>
                    </div>

                    <div class="col-12 advanced-filters mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Incident Type</label>
                                <select name="incident_type_id" class="form-select">
                                    <option value="">Any</option>
                                    <?php foreach ($incident_types as $t): ?>
                                        <option value="<?php echo $t['id']; ?>" <?php echo (isset($_POST['incident_type_id']) && $_POST['incident_type_id'] == $t['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($t['type_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Severity</label>
                                <select name="severity_id" class="form-select">
                                    <option value="">Any</option>
                                    <?php foreach ($severity_levels as $s): ?>
                                        <option value="<?php echo $s['id']; ?>" <?php echo (isset($_POST['severity_id']) && $_POST['severity_id'] == $s['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['level_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status_id" class="form-select">
                                    <option value="">Any</option>
                                    <?php foreach ($status_options as $st): ?>
                                        <option value="<?php echo $st['id']; ?>" <?php echo (isset($_POST['status_id']) && $_POST['status_id'] == $st['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['status_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </div>

        <?php if ($search_performed): ?>
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Search Results (<?php echo count($search_results); ?> found)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($search_results)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Incident ID</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($search_results as $incident): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($incident['incident_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(truncateString($incident['title'], 40)); ?></td>
                                        <td><?php echo htmlspecialchars($incident['type_name']); ?></td>
                                        <td>
                                            <span class="badge" style="background-color: <?php echo getSeverityColor($incident['severity']); ?>">
                                                <?php echo htmlspecialchars($incident['severity']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($incident['status']); ?></span>
                                        </td>
                                        <td><?php echo formatDate($incident['incident_date']); ?></td>
                                        <td>
                                            <a href="view_incident.php?id=<?php echo $incident['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle"></i> No incidents found matching your search criteria.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleAdvanced').addEventListener('click', function(e) {
            e.preventDefault();
            const adv = document.querySelector('.advanced-filters');
            if (adv.style.display === 'none' || adv.style.display === '') {
                adv.style.display = 'block';
                this.textContent = 'Hide Filters';
            } else {
                adv.style.display = 'none';
                this.textContent = 'Advanced Filters';
            }
        });

        // If any advanced filter value is present, open the panel
        window.addEventListener('DOMContentLoaded', function() {
            const adv = document.querySelector('.advanced-filters');
            const anySet = <?php echo json_encode((bool) (!empty($_POST['incident_type_id']) || !empty($_POST['severity_id']) || !empty($_POST['status_id']) || !empty($_POST['start_date']) || !empty($_POST['end_date']))); ?>;
            if (anySet) {
                adv.style.display = 'block';
                document.getElementById('toggleAdvanced').textContent = 'Hide Filters';
            }
        });
    </script>
</body>
</html>
