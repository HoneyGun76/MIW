@echo off
REM MIW Emergency Rollback Script (Windows version)
REM Quick rollback to production-backup branch for Railway deployment

echo.
echo 🚨 MIW Emergency Rollback Script
echo =================================
echo.

REM Configuration
set REMOTE_NAME=origin
set PRODUCTION_BRANCH=production-backup
set MAIN_BRANCH=main

REM Check if we're in the right directory
if not exist "config.php" (
    echo ❌ Error: This script must be run from the MIW project root directory
    exit /b 1
)
if not exist "railway.json" (
    echo ❌ Error: This script must be run from the MIW project root directory
    exit /b 1
)

REM Get current branch
for /f "tokens=*" %%i in ('git branch --show-current') do set CURRENT_BRANCH=%%i
echo 📍 Current branch: %CURRENT_BRANCH%

REM Show current status
echo 📊 Current repository status:
git status --short

REM Confirm rollback action
echo.
echo ⚠️  WARNING: This will:
echo    1. Switch to production-backup branch
echo    2. Force push to main branch (Railway deployment)
echo    3. Overwrite any uncommitted changes
echo.
set /p confirmation="Do you want to proceed with emergency rollback? (type 'YES' to confirm): "

if not "%confirmation%"=="YES" (
    echo ❌ Rollback cancelled
    exit /b 1
)

echo.
echo 🔄 Starting emergency rollback process...

REM Step 1: Fetch latest changes
echo 📥 Fetching latest changes...
git fetch %REMOTE_NAME%

REM Step 2: Check for uncommitted changes and stash if needed
git diff-index --quiet HEAD --
if errorlevel 1 (
    echo 💾 Stashing uncommitted changes...
    git stash push -m "Emergency rollback stash %date% %time%"
)

REM Step 3: Switch to production-backup branch
echo 🔀 Switching to %PRODUCTION_BRANCH% branch...
git checkout %PRODUCTION_BRANCH%

REM Step 4: Pull latest production-backup
echo ⬇️  Pulling latest %PRODUCTION_BRANCH%...
git pull %REMOTE_NAME% %PRODUCTION_BRANCH%

REM Step 5: Force push to main (this triggers Railway deployment)
echo 🚀 Force pushing to %MAIN_BRANCH% (triggering Railway deployment)...
git push %REMOTE_NAME% %PRODUCTION_BRANCH%:%MAIN_BRANCH% --force

REM Step 6: Switch back to main and update
echo 🔄 Updating local main branch...
git checkout %MAIN_BRANCH%
git reset --hard %REMOTE_NAME%/%MAIN_BRANCH%

echo.
echo ✅ Emergency rollback completed successfully!
echo.
echo 📋 Summary:
echo    - Switched to production-backup branch
echo    - Force pushed production-backup to main
echo    - Railway will deploy the stable backup version
echo    - Local main branch updated to match
echo.
echo 🔗 Check Railway deployment status at:
echo    https://railway.app/project/[your-project-id]
echo.
echo ⚠️  Remember to:
echo    1. Monitor Railway deployment logs
echo    2. Test application functionality
echo    3. Check database connectivity
echo    4. Verify file upload functionality
echo.

REM Show recent commits
echo 📝 Recent commits on main branch:
git log --oneline -5

echo.
pause
