<?php
/**
 * View Incidents Page
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

$incident = new Incident();
$incidents = $incident->getAllIncidents(50, 0);

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Incidents - Security Incident Reporting System</title>
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
                        <a class="nav-link active" href="incidents.php">All Incidents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_incident.php">Report Incident</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Search</a>
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

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>All Incidents</h2>
                    <a href="add_incident.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Report New Incident
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Incident List (<?php echo count($incidents); ?> total)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($incidents)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Incident ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Reporter</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incidents as $inc): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($inc['incident_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars(truncateString($inc['title'], 35)); ?></td>
                                    <td><?php echo htmlspecialchars($inc['type_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo getSeverityColor($inc['severity'] ?? 'Low'); ?>">
                                            <?php echo htmlspecialchars($inc['severity'] ?? 'Unknown'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($inc['status'] ?? 'Unknown'); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($inc['reporter_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo formatDate($inc['incident_date']); ?></td>
                                    <td>
                                        <a href="view_incident.php?id=<?php echo $inc['id']; ?>" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle"></i> No incidents reported yet.
                        <a href="add_incident.php" class="alert-link">Report the first incident</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>