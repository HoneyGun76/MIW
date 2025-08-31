<?php
/**
 * Email Queue Admin Panel
 * Monitor and manage email queue status
 */

require_once 'config.php';

// Only allow access in Railway environment or with admin access
if (!$isRailway && !isset($_GET['admin'])) {
    die("Access denied.");
}

// Handle actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'retry_failed':
            $stmt = $pdo->prepare("UPDATE email_queue SET status = 'pending', attempts = 0 WHERE status = 'failed'");
            $stmt->execute();
            $message = "Failed emails marked for retry";
            break;
            
        case 'clear_sent':
            $stmt = $pdo->prepare("DELETE FROM email_queue WHERE status = 'sent' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $stmt->execute();
            $message = "Sent emails older than 7 days cleared";
            break;
    }
}

// Get queue statistics
try {
    $stats = $pdo->query("
        SELECT 
            status,
            COUNT(*) as count,
            MIN(created_at) as oldest,
            MAX(created_at) as newest
        FROM email_queue 
        GROUP BY status
        ORDER BY 
            CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'processing' THEN 2 
                WHEN 'sent' THEN 3 
                WHEN 'failed' THEN 4 
                ELSE 5 
            END
    ")->fetchAll();
    
    // Get recent emails
    $recent = $pdo->query("
        SELECT id, recipient_email, subject, status, email_type, attempts, created_at, error_message
        FROM email_queue 
        ORDER BY created_at DESC 
        LIMIT 20
    ")->fetchAll();
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Queue Admin - MIW Travel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            border: 1px solid #ddd; 
            padding: 15px; 
            border-radius: 5px; 
            min-width: 150px;
            text-align: center;
        }
        .stat-card.pending { border-color: #f39c12; background-color: #fef9e7; }
        .stat-card.processing { border-color: #3498db; background-color: #ebf3fd; }
        .stat-card.sent { border-color: #27ae60; background-color: #eafaf1; }
        .stat-card.failed { border-color: #e74c3c; background-color: #fdedec; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f6b127; color: #000; }
        .status-pending { color: #f39c12; font-weight: bold; }
        .status-processing { color: #3498db; font-weight: bold; }
        .status-sent { color: #27ae60; font-weight: bold; }
        .status-failed { color: #e74c3c; font-weight: bold; }
        .actions { margin: 20px 0; }
        .btn { padding: 10px 15px; margin-right: 10px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-warning { background-color: #f39c12; color: white; }
        .btn-danger { background-color: #e74c3c; color: white; }
        .message { padding: 10px; margin: 10px 0; border-radius: 3px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    </style>
</head>
<body>
    <h1>📧 Email Queue Admin Panel</h1>
    <p><strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?></p>
    
    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="message" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <h2>📊 Queue Statistics</h2>
    <div class="stats">
        <?php 
        $statusCounts = [];
        foreach ($stats as $stat) {
            $statusCounts[$stat['status']] = $stat;
        }
        
        $allStatuses = ['pending', 'processing', 'sent', 'failed'];
        foreach ($allStatuses as $status):
            $stat = $statusCounts[$status] ?? ['count' => 0, 'oldest' => null, 'newest' => null];
        ?>
            <div class="stat-card <?= $status ?>">
                <h3><?= ucfirst($status) ?></h3>
                <div style="font-size: 24px; font-weight: bold;"><?= $stat['count'] ?></div>
                <?php if ($stat['oldest']): ?>
                    <small>Oldest: <?= date('M j, H:i', strtotime($stat['oldest'])) ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="actions">
        <h2>🔧 Actions</h2>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="retry_failed">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Retry all failed emails?')">
                🔄 Retry Failed Emails
            </button>
        </form>
        
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="clear_sent">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Clear sent emails older than 7 days?')">
                🗑️ Clear Old Sent Emails
            </button>
        </form>
        
        <button onclick="location.reload()" class="btn" style="background-color: #95a5a6; color: white;">
            🔄 Refresh
        </button>
    </div>
    
    <h2>📋 Recent Emails</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Recipient</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Status</th>
                <th>Attempts</th>
                <th>Created</th>
                <th>Error</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent as $email): ?>
                <tr>
                    <td><?= $email['id'] ?></td>
                    <td><?= htmlspecialchars($email['recipient_email']) ?></td>
                    <td><?= htmlspecialchars(substr($email['subject'], 0, 50)) ?>...</td>
                    <td><?= $email['email_type'] ?></td>
                    <td class="status-<?= $email['status'] ?>"><?= $email['status'] ?></td>
                    <td><?= $email['attempts'] ?></td>
                    <td><?= date('M j, H:i', strtotime($email['created_at'])) ?></td>
                    <td><?= $email['error_message'] ? htmlspecialchars(substr($email['error_message'], 0, 50)) . '...' : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h2>📖 Instructions</h2>
    <ol>
        <li><strong>Run Local Worker:</strong> Download <code>local_email_worker.php</code> and run on your local machine</li>
        <li><strong>Configure Database:</strong> Update Railway database connection details in worker script</li>
        <li><strong>Configure SMTP:</strong> Set up your local email credentials (Gmail, etc.)</li>
        <li><strong>Keep Running:</strong> Local worker should run continuously to process emails</li>
    </ol>
    
    <p><small>This panel shows emails queued by Railway for processing by your local email worker.</small></p>
</body>
</html>
