#!/bin/bash
# MIW Emergency Rollback Script
# Quick rollback to production-backup branch for Railway deployment

set -e

echo "🚨 MIW Emergency Rollback Script"
echo "================================="

# Configuration
REMOTE_NAME="origin"
PRODUCTION_BRANCH="production-backup"
MAIN_BRANCH="main"

# Check if we're in the right directory
if [ ! -f "config.php" ] || [ ! -f "railway.json" ]; then
    echo "❌ Error: This script must be run from the MIW project root directory"
    exit 1
fi

# Get current branch
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 Current branch: $CURRENT_BRANCH"

# Show current status
echo "📊 Current repository status:"
git status --short

# Confirm rollback action
echo ""
echo "⚠️  WARNING: This will:"
echo "   1. Switch to production-backup branch"
echo "   2. Force push to main branch (Railway deployment)"
echo "   3. Overwrite any uncommitted changes"
echo ""
read -p "Do you want to proceed with emergency rollback? (type 'YES' to confirm): " confirmation

if [ "$confirmation" != "YES" ]; then
    echo "❌ Rollback cancelled"
    exit 1
fi

echo ""
echo "🔄 Starting emergency rollback process..."

# Step 1: Fetch latest changes
echo "📥 Fetching latest changes..."
git fetch $REMOTE_NAME

# Step 2: Stash any uncommitted changes
if ! git diff-index --quiet HEAD --; then
    echo "💾 Stashing uncommitted changes..."
    git stash push -m "Emergency rollback stash $(date '+%Y-%m-%d %H:%M:%S')"
fi

# Step 3: Switch to production-backup branch
echo "🔀 Switching to $PRODUCTION_BRANCH branch..."
git checkout $PRODUCTION_BRANCH

# Step 4: Pull latest production-backup
echo "⬇️  Pulling latest $PRODUCTION_BRANCH..."
git pull $REMOTE_NAME $PRODUCTION_BRANCH

# Step 5: Force push to main (this triggers Railway deployment)
echo "🚀 Force pushing to $MAIN_BRANCH (triggering Railway deployment)..."
git push $REMOTE_NAME $PRODUCTION_BRANCH:$MAIN_BRANCH --force

# Step 6: Switch back to main and update
echo "🔄 Updating local main branch..."
git checkout $MAIN_BRANCH
git reset --hard $REMOTE_NAME/$MAIN_BRANCH

echo ""
echo "✅ Emergency rollback completed successfully!"
echo ""
echo "📋 Summary:"
echo "   - Switched to production-backup branch"
echo "   - Force pushed production-backup to main"
echo "   - Railway will deploy the stable backup version"
echo "   - Local main branch updated to match"
echo ""
echo "🔗 Check Railway deployment status at:"
echo "   https://railway.app/project/[your-project-id]"
echo ""
echo "⚠️  Remember to:"
echo "   1. Monitor Railway deployment logs"
echo "   2. Test application functionality"
echo "   3. Check database connectivity"
echo "   4. Verify file upload functionality"
echo ""

# Optional: Show recent commits
echo "📝 Recent commits on main branch:"
git log --oneline -5
