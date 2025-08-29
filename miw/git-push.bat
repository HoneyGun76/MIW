@echo off
echo Staging changes...
git add .

echo.
echo Enter your commit message:
set /p commit_msg="> "

echo.
echo Committing changes...
git commit -m "%commit_msg%"

echo.
echo Pushing to GitHub...
git push

echo.
echo Done!
pause
