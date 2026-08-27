param(
    [Parameter(Position=0)]
    [string]$Message
)

$ErrorActionPreference = "Stop"

if (-not $Message) {
    $Message = Read-Host "Mensaje del commit"
}

if (-not $Message) {
    Write-Host "Se necesita un mensaje de commit." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  GALATV - COMMIT + PUSH" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/3] Agregando cambios..." -ForegroundColor Yellow
git add .
if ($LASTEXITCODE -ne 0) { exit 1 }

Write-Host "[2/3] Creando commit: $Message" -ForegroundColor Yellow
git commit -m $Message
if ($LASTEXITCODE -ne 0) { exit 1 }

Write-Host "[3/3] Subiendo a GitHub..." -ForegroundColor Yellow
git push
if ($LASTEXITCODE -ne 0) { exit 1 }

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Listo! El deploy a FTP se inicio" -ForegroundColor Green
Write-Host "  (ver: github.com/estado3d-svg/galatv/actions)" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
