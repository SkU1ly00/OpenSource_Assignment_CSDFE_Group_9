<?php
/**
 * View Incident Details Page
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

if (!isset($_GET['id'])) {
    redirect('incidents.php');
}

$incident = new Incident();
$incident_detail = $incident->getIncidentById(intval($_GET['id']));

if (!$incident_detail) {
    redirect('incidents.php');
}

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Details - Security Incident Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .detail-section { margin-bottom: 25px; }
        .detail-label { font-weight: 600; color: #555; margin-top: 10px; }
        .detail-value { color: #333; }
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

    <div class="container mt-4 mb-4">
        <div class="row mb-4">
            <div class="col-12">
                <a href="incidents.php" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Incidents
                </a>
                <h2>Incident Details</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">ID: <?php echo htmlspecialchars($incident_detail['incident_id']); ?></h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge" style="background-color: <?php echo getSeverityColor($incident_detail['severity']); ?>; font-size: 12px;">
                            <?php echo htmlspecialchars($incident_detail['severity']); ?>
                        </span>
                        <span class="badge bg-info" style="font-size: 12px;">
                            <?php echo htmlspecialchars($incident_detail['status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="detail-section">
                    <div class="detail-label">Title</div>
                    <div class="detail-value"><?php echo htmlspecialchars($incident_detail['title']); ?></div>
                </div>

                <div class="row">
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Type</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incident_detail['type_name']); ?></div>
                    </div>
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Priority Level</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incident_detail['priority_level']); ?></div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-label">Description</div>
                    <div class="detail-value" style="background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                        <?php echo nl2br(htmlspecialchars($incident_detail['description'])); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Incident Date</div>
                        <div class="detail-value"><?php echo formatDateTime($incident_detail['incident_date']); ?></div>
                    </div>
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Discovery Date</div>
                        <div class="detail-value"><?php echo formatDateTime($incident_detail['discovery_date']); ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Reporter</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incident_detail['reporter_name']); ?></div>
                    </div>
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Handler</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incident_detail['handler_name'] ?? 'Not Assigned'); ?></div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?php echo htmlspecialchars($incident_detail['location'] ?? 'Not specified'); ?></div>
                </div>

                <div class="detail-section">
                    <div class="detail-label">Affected Systems</div>
                    <div class="detail-value"><?php echo htmlspecialchars($incident_detail['affected_systems'] ?? 'Not specified'); ?></div>
                </div>

                <div class="row">
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Users Affected</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incident_detail['number_of_users_affected']); ?></div>
                    </div>
                    <div class="col-md-6 detail-section">
                        <div class="detail-label">Data Compromised</div>
                        <div class="detail-value">
                            <?php echo $incident_detail['data_compromised'] ? 'Yes' : 'No'; ?>
                            <?php if ($incident_detail['data_compromised']): ?>
                                (<?php echo htmlspecialchars($incident_detail['data_type_compromised']); ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-label">Estimated Impact</div>
                    <div class="detail-value"><?php echo htmlspecialchars($incident_detail['estimated_impact'] ?? 'Not specified'); ?></div>
                </div>

                <div class="detail-section">
                    <div class="detail-label">Date Reported</div>
                    <div class="detail-value"><?php echo formatDateTime($incident_detail['created_at']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>