#!/bin/bash

# Check Laravel error log
echo "=== Laravel Error Log ==="
tail -50 storage/logs/laravel.log 2>/dev/null || echo "No error log found"

echo ""
echo "=== PHP Error Log ==="
tail -20 /var/log/apache2/error.log 2>/dev/null || tail -20 /var/log/httpd/error_log 2>/dev/null || echo "Server error log not accessible"

echo ""
echo "=== File Permissions ==="
ls -la storage/
ls -la bootstrap/
