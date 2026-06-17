<?php
/**
 * Search Incidents Page
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

$search_results = [];
$search_performed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['q']) && !empty($_GET['q']))) {
    $search_term = sanitizeInput($_GET['q'] ?? $_POST['search_term'] ?? '');
    
    if (!empty($search_term)) {
        $incident = new Incident();
        $search_results = $incident->searchIncidentById($search_term);
        $search_performed = true;
    }
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
                <p class="text-muted">Search incidents by incident ID</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="search.php" class="row g-3">
                    <div class="col-12 col-md-8">
                        <input type="text" class="form-control form-control-lg" name="search_term" placeholder="Enter Incident ID (e.g., INC...)" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
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
</body>
</html>