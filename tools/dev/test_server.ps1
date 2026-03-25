try {
    $r = Invoke-WebRequest -UseBasicParsing -Uri 'http://localhost:8001/' -Method GET -TimeoutSec 30
    Write-Output "Status: $($r.StatusCode), Length: $($r.Content.Length)"
} catch {
    Write-Output "Error: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        Write-Output "HTTP Status: $($_.Exception.Response.StatusCode)"
    }
}
