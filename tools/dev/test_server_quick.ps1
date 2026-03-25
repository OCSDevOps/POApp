try {
    $r = Invoke-WebRequest -UseBasicParsing -Uri 'http://127.0.0.1:8001/' -Method GET -TimeoutSec 30
    Write-Output "Status: $($r.StatusCode), Length: $($r.Content.Length)"
    if ($r.Content -match '<title>(.*?)</title>') {
        Write-Output "Title: $($Matches[1])"
    }
    if ($r.Content -match 'Login|login|email|password') {
        Write-Output "Login page detected!"
    }
} catch {
    Write-Output "Error: $($_.Exception.Message)"
    if ($_.Exception.Response) {
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $body = $reader.ReadToEnd()
        if ($body.Length -gt 500) { $body = $body.Substring(0,500) }
        Write-Output "Response body (first 500 chars):"
        Write-Output $body
    }
}
