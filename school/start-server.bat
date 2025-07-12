@echo off
echo Starting Laravel server with increased upload limits...
php -d upload_max_filesize=20M -d post_max_size=25M -d max_execution_time=300 -d memory_limit=256M -d max_file_uploads=50 -S 127.0.0.1:8000 -t public
pause 