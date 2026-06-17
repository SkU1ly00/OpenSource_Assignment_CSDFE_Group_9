<?php
/**
 * Main Dashboard Page
 */

session_start();
require_once 'config/config.php';
require_once 'src/classes/Database.php';
require_once 'src/classes/Auth.php';
require_once 'src/classes/Incident.php';
require_once 'src/functions.php';

if (!isUserLoggedIn()) {
    redirect('login.php');
}

$incident = new Incident();
$stats = $incident->getStatistics();
$recent_incidents = $incident->getAllIncidents(5, 0);
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Security Incident Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card {
            border-left: 4px solid #667eea;
            border-radius: 5px;
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card.critical { border-left-color: #FF0000; }
        .stat-card.high { border-left-color: #FF6600; }
        .stat-card.medium { border-left-color: #FFCC00; }
        .stat-card.low { border-left-color: #00CC00; }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shield-lock"></i> Security Incident Reporting
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="src/pages/incidents.php">All Incidents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="src/pages/add_incident.php">Report Incident</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="src/pages/search.php">Search</a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="src/pages/users.php">Users</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($current_user['username']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="src/pages/profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="src/pages/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Dashboard</h2>
                <p class="text-muted">Welcome, <?php echo htmlspecialchars($current_user['first_name']); ?></p>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_incidents'] ?? 0; ?></div>
                    <div class="stat-label">Total Incidents</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card critical">
                    <div class="stat-number" style="color: #FF0000;"><?php echo $stats['critical'] ?? 0; ?></div>
                    <div class="stat-label">Critical</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card high">
                    <div class="stat-number" style="color: #FF6600;"><?php echo $stats['high'] ?? 0; ?></div>
                    <div class="stat-label">High Priority</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card medium">
                    <div class="stat-number" style="color: #FFCC00;"><?php echo $stats['medium'] ?? 0; ?></div>
                    <div class="stat-label">Medium Priority</div>
                </div>
            </div>
        </div>

        <!-- Recent Incidents -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Recent Incidents</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_incidents)): ?>
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
                                        <?php foreach ($recent_incidents as $inc): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($inc['incident_id']); ?></strong></td>
                                            <td><?php echo htmlspecialchars(truncateString($inc['title'], 30)); ?></td>
                                            <td><?php echo htmlspecialchars($inc['type_name']); ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?php echo getSeverityColor($inc['severity']); ?>">
                                                    <?php echo htmlspecialchars($inc['severity']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($inc['status']); ?></span>
                                            </td>
                                            <td><?php echo formatDate($inc['incident_date']); ?></td>
                                            <td>
                                                <a href="src/pages/view_incident.php?id=<?php echo $inc['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="src/pages/incidents.php" class="btn btn-outline-primary">View All Incidents</a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">No incidents reported yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4 mb-4">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-plus-circle" style="font-size: 32px; color: #667eea;"></i>
                        <h5 class="card-title mt-3">Report New Incident</h5>
                        <p class="card-text">Document a new security incident</p>
                        <a href="src/pages/add_incident.php" class="btn btn-primary">Report Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-search" style="font-size: 32px; color: #667eea;"></i>
                        <h5 class="card-title mt-3">Search Incidents</h5>
                        <p class="card-text">Find incidents by ID or other criteria</p>
                        <a href="src/pages/search.php" class="btn btn-primary">Search</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>