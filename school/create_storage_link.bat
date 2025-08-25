@echo off
echo Creating storage symbolic link...
cd /d "%~dp0"
if exist "public\storage" (
    echo Removing existing storage link...
    rmdir "public\storage"
)
echo Creating new storage link...
mklink /D "public\storage" "storage\app\public"
echo Storage link created successfully!
pause 