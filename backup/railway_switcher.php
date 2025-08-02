<?php
/**
 * MIW Railway Branch Switcher
 * Allows switching Railway deployment between different git branches
 */

require_once __DIR__ . '/../config.php';

class RailwayBranchSwitcher {
    private $allowedBranches = [
        'main' => 'Main Development Branch',
        'production-backup' => 'Stable Production Backup',
        'staging' => 'Staging/Testing Branch'
    ];
    
    public function getCurrentBranch() {
        $output = [];
        exec('git branch --show-current', $output, $return_var);
        
        if ($return_var === 0 && !empty($output)) {
            return trim($output[0]);
        }
        
        return 'unknown';
    }
    
    public function getAvailableBranches() {
        $output = [];
        exec('git branch -r', $output, $return_var);
        
        $branches = [];
        if ($return_var === 0) {
            foreach ($output as $line) {
                $branch = trim(str_replace('origin/', '', $line));
                if (isset($this->allowedBranches[$branch])) {
                    $branches[$branch] = $this->allowedBranches[$branch];
                }
            }
        }
        
        return $branches;
    }
    
    public function switchToProductionBackup() {
        return $this->executeBranchSwitch('production-backup', 'main');
    }
    
    public function switchToStaging() {
        return $this->executeBranchSwitch('staging', 'main');
    }
    
    public function switchToMain() {
        return $this->executeBranchSwitch('main', 'main');
    }
    
    private function executeBranchSwitch($sourceBranch, $targetBranch) {
        $commands = [
            'git fetch origin',
            "git checkout {$sourceBranch}",
            "git pull origin {$sourceBranch}",
            "git push origin {$sourceBranch}:{$targetBranch} --force"
        ];
        
        $results = [];
        foreach ($commands as $command) {
            $output = [];
            $return_var = 0;
            exec($command . ' 2>&1', $output, $return_var);
            
            $results[] = [
                'command' => $command,
                'output' => implode("\n", $output),
                'success' => $return_var === 0
            ];
            
            if ($return_var !== 0) {
                return [
                    'success' => false,
                    'message' => "Command failed: {$command}",
                    'details' => $results
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => "Successfully switched Railway deployment to {$sourceBranch}",
            'details' => $results
        ];
    }
    
    public function getDeploymentStatus() {
        // This would ideally check Railway API for deployment status
        // For now, return basic git information
        $commands = [
            'git log --oneline -5',
            'git status --porcelain'
        ];
        
        $status = [];
        foreach ($commands as $command) {
            $output = [];
            exec($command, $output, $return_var);
            $status[$command] = [
                'output' => implode("\n", $output),
                'success' => $return_var === 0
            ];
        }
        
        return $status;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $switcher = new RailwayBranchSwitcher();
    
    switch ($_GET['action']) {
        case 'get_current_branch':
            echo json_encode(['branch' => $switcher->getCurrentBranch()]);
            exit;
            
        case 'get_branches':
            echo json_encode($switcher->getAvailableBranches());
            exit;
            
        case 'switch_to_production':
            echo json_encode($switcher->switchToProductionBackup());
            exit;
            
        case 'switch_to_staging':
            echo json_encode($switcher->switchToStaging());
            exit;
            
        case 'switch_to_main':
            echo json_encode($switcher->switchToMain());
            exit;
            
        case 'get_status':
            echo json_encode($switcher->getDeploymentStatus());
            exit;
    }
}

$switcher = new RailwayBranchSwitcher();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway Branch Switcher</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; font-size: 14px; }
        .btn-primary { background: #007cba; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .btn:hover { opacity: 0.9; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .status.success { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.info { background: #d1ecf1; color: #0c5460; }
        .branch-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .command-output { background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
        .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Railway Branch Switcher</h1>
            <p>Manage Railway deployment branch switching</p>
        </div>

        <div class="section">
            <h2>Current Status</h2>
            <div class="branch-info">
                <strong>Current Branch:</strong> <span id="current-branch">Loading...</span><br>
                <strong>Environment:</strong> <?= getCurrentEnvironment() ?><br>
                <strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?>
            </div>
            <button class="btn btn-success" onclick="refreshStatus()">Refresh Status</button>
        </div>

        <div class="warning-box">
            <strong>⚠️ Important:</strong> Switching branches will trigger a new Railway deployment. 
            Make sure you understand the implications before proceeding.
        </div>

        <div class="section">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4>🛡️ Switch to Production Backup</h4>
                    <p>Deploy the stable production-backup branch</p>
                    <button class="btn btn-warning" onclick="switchToProduction()">Deploy Production Backup</button>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4>🧪 Switch to Staging</h4>
                    <p>Deploy the staging branch for testing</p>
                    <button class="btn btn-primary" onclick="switchToStaging()">Deploy Staging</button>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4>🚀 Switch to Main</h4>
                    <p>Deploy the main development branch</p>
                    <button class="btn btn-success" onclick="switchToMain()">Deploy Main</button>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Deployment Status</h2>
            <div id="deployment-status">
                <div class="status info">Click "Get Deployment Status" to check current deployment information</div>
            </div>
            <button class="btn btn-primary" onclick="getDeploymentStatus()">Get Deployment Status</button>
        </div>

        <div id="status-area"></div>
    </div>

    <script>
        // Load current branch on page load
        document.addEventListener('DOMContentLoaded', function() {
            refreshStatus();
        });

        function refreshStatus() {
            fetch('?action=get_current_branch')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('current-branch').textContent = data.branch;
                });
        }

        function switchToProduction() {
            if (!confirm('Switch Railway deployment to production-backup branch? This will deploy the stable backup version.')) {
                return;
            }
            
            showStatus('Switching to production-backup branch...', 'info');
            
            fetch('?action=switch_to_production')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('✅ ' + data.message, 'success');
                        refreshStatus();
                    } else {
                        showStatus('❌ Switch failed: ' + data.message, 'error');
                        console.log('Details:', data.details);
                    }
                });
        }

        function switchToStaging() {
            if (!confirm('Switch Railway deployment to staging branch?')) {
                return;
            }
            
            showStatus('Switching to staging branch...', 'info');
            
            fetch('?action=switch_to_staging')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('✅ ' + data.message, 'success');
                        refreshStatus();
                    } else {
                        showStatus('❌ Switch failed: ' + data.message, 'error');
                    }
                });
        }

        function switchToMain() {
            if (!confirm('Switch Railway deployment to main branch? This will deploy the latest development version.')) {
                return;
            }
            
            showStatus('Switching to main branch...', 'info');
            
            fetch('?action=switch_to_main')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('✅ ' + data.message, 'success');
                        refreshStatus();
                    } else {
                        showStatus('❌ Switch failed: ' + data.message, 'error');
                    }
                });
        }

        function getDeploymentStatus() {
            fetch('?action=get_status')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    for (let command in data) {
                        html += `<h4>${command}</h4>`;
                        html += `<div class="command-output">${data[command].output}</div>`;
                    }
                    document.getElementById('deployment-status').innerHTML = html;
                });
        }

        function showStatus(message, type) {
            const statusArea = document.getElementById('status-area');
            statusArea.innerHTML = `<div class="status ${type}">${message}</div>`;
            setTimeout(() => {
                statusArea.innerHTML = '';
            }, 8000);
        }
    </script>
</body>
</html>
