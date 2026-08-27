# ========================================
# GALATV - DEPLOYMENT SCRIPT
# ========================================

$Server = "c2642305.ferozo.com"
$User = "tu_c2642305"
$Pass = "Argentinaconsalud26/"
$RemotePath = "/public_html"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  GALATV - DEPLOYMENT SCRIPT" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Conectando a FTPS..." -ForegroundColor Yellow
Write-Host ""

try {
    # Conectar con SFTP
    $sftp = New-Object System.Net.Sockets.TcpClient $Server, 21
    $stream = $sftp.GetStream()
    $reader = New-Object System.IO.StreamReader($stream)
    $writer = New-Object System.IO.StreamWriter($stream, $True, $StreamReader.Encoding)
    
    # Comando USER
    $writer.WriteLine("USER $User")
    $writer.Flush()
    $response = $reader.ReadLine()
    Write-Host "Servidor: $response"
    
    # Comando PASS
    $writer.WriteLine("PASS $Pass")
    $writer.Flush()
    $response = $reader.ReadLine()
    Write-Host "Autenticación: $response"
    
    # Comando CWD
    $writer.WriteLine("CWD $RemotePath")
    $writer.Flush()
    $response = $reader.ReadLine()
    Write-Host "Directorio: $response"
    
    # Subir archivos
    Get-ChildItem -Path . -File | ForEach-Object {
        $writer.WriteLine("STOR $_.Name")
        $writer.Flush()
        Get-Content $_.FullName | ForEach-Object { $writer.WriteLine($_) }
        $writer.Flush()
        Write-Host "Subido: $($_.Name)" -ForegroundColor Green
    }
    
    # Cerrar conexión
    $writer.WriteLine("QUIT")
    $writer.Flush()
    
    $sftp.Close()
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  ¡Listo! Archivos subidos" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    
} catch {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "  ERROR: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
}

Write-Host ""
Write-Host "Presiona una tecla para continuar..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
