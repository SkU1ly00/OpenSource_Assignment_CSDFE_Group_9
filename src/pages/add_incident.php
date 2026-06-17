<?php
/**
 * Add Incident Page
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

// Get incident types
$db->prepare('SELECT id, type_name FROM incident_types ORDER BY type_name');
$incident_types = $db->resultSet();

// Get severity levels
$db->prepare('SELECT id, level_name FROM severity_levels ORDER BY id');
$severity_levels = $db->resultSet();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'incident_type_id' => sanitizeInput($_POST['incident_type_id'] ?? ''),
        'severity_id' => sanitizeInput($_POST['severity_id'] ?? ''),
        'title' => sanitizeInput($_POST['title'] ?? ''),
        'description' => sanitizeInput($_POST['description'] ?? ''),
        'location' => sanitizeInput($_POST['location'] ?? ''),
        'incident_date' => sanitizeInput($_POST['incident_date'] ?? ''),
        'discovery_date' => sanitizeInput($_POST['discovery_date'] ?? ''),
        'reporter_id' => $_SESSION['user_id'],
        'affected_systems' => sanitizeInput($_POST['affected_systems'] ?? ''),
        'number_of_users_affected' => intval($_POST['number_of_users_affected'] ?? 0),
        'data_compromised' => isset($_POST['data_compromised']) ? 1 : 0,
        'data_type_compromised' => sanitizeInput($_POST['data_type_compromised'] ?? ''),
        'priority_level' => sanitizeInput($_POST['priority_level'] ?? 'Medium')
    ];

    $incident = new Incident();
    $result = $incident->createIncident($data);

    if ($result['success']) {
        $success_message = $result['message'];
    } else {
        $error_message = $result['message'];
    }
}

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident - Security Incident Reporting System</title>
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
                        <a class="nav-link active" href="add_incident.php">Report Incident</a>
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

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="bi bi-plus-circle"></i> Report New Incident</h2>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="add_incident.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="incident_type_id" class="form-label">Incident Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="incident_type_id" name="incident_type_id" required>
                                <option value="">Select Incident Type</option>
                                <?php foreach ($incident_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['type_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="severity_id" class="form-label">Severity <span class="text-danger">*</span></label>
                            <select class="form-control" id="severity_id" name="severity_id" required>
                                <option value="">Select Severity</option>
                                <?php foreach ($severity_levels as $level): ?>
                                    <option value="<?php echo $level['id']; ?>"><?php echo htmlspecialchars($level['level_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Incident Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Brief description of the incident" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide detailed information about the incident" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="incident_date" class="form-label">Incident Date/Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="incident_date" name="incident_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discovery_date" class="form-label">Discovery Date/Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="discovery_date" name="discovery_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location/Department</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Where did the incident occur?">
                    </div>

                    <div class="mb-3">
                        <label for="affected_systems" class="form-label">Affected Systems</label>
                        <input type="text" class="form-control" id="affected_systems" name="affected_systems" placeholder="List of systems affected">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="number_of_users_affected" class="form-label">Number of Users Affected</label>
                            <input type="number" class="form-control" id="number_of_users_affected" name="number_of_users_affected" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priority_level" class="form-label">Priority Level</label>
                            <select class="form-control" id="priority_level" name="priority_level">
                                <option value="Critical">Critical</option>
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="data_compromised" name="data_compromised">
                            <label class="form-check-label" for="data_compromised">
                                Data Compromised
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="data_type_div" style="display: none;">
                        <label for="data_type_compromised" class="form-label">Type of Data Compromised</label>
                        <input type="text" class="form-control" id="data_type_compromised" name="data_type_compromised" placeholder="e.g., Personal Information, Financial Data">
                    </div>

                    <div class="mb-3">
                        <label for="estimated_impact" class="form-label">Estimated Impact</label>
                        <textarea class="form-control" id="estimated_impact" name="estimated_impact" rows="3" placeholder="Describe the estimated impact of this incident"></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="incidents.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide data type compromised field
        const dataCompromisedCheckbox = document.getElementById('data_compromised');
        const dataTypeDiv = document.getElementById('data_type_div');
        
        dataCompromisedCheckbox.addEventListener('change', function() {
            dataTypeDiv.style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>