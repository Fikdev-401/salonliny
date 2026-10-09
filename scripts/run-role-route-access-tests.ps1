param(
    [string]$PhpBin = "php"
)

$phpVersion = & $PhpBin -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;"
if ($LASTEXITCODE -ne 0) {
    Write-Error "Gagal membaca versi PHP dari binary: $PhpBin"
    exit 1
}

if ([version]$phpVersion -lt [version]"8.2") {
    Write-Error "PHP $phpVersion terdeteksi. Project ini memerlukan PHP >= 8.2."
    exit 1
}

Write-Host "Menjalankan test akses role-menu..."
& $PhpBin artisan test --filter="RoleMenuAccessMatrixTest|RoleAuthorizationTest"
exit $LASTEXITCODE
