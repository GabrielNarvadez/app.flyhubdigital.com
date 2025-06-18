<?php
declare(strict_types=1);

// sync_ui.php — demo UI for tenant 12
const DEMO_TENANT_ID = 12;

$direction = $_GET['direction'] ?? null;
$stream    = isset($_GET['stream']) && in_array($direction, ['shopify','flyhub','halt'], true);

// Streaming endpoint: run the CLI and push Server-Sent Events
if ($stream) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    // Build our command
    $script = __DIR__ . DIRECTORY_SEPARATOR . 'shopify.php';
    if ($direction === 'halt') {
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Windows: use PowerShell to kill anything with shopify.php in its command line
            $cmd = 'powershell -Command "Get-CimInstance Win32_Process '
                 . '-Filter \\"CommandLine LIKE \'%shopify.php%\'\\" '
                 . '| Select-Object -ExpandProperty ProcessId '
                 . '| ForEach-Object { Stop-Process -Id $_ -Force }" 2>&1';
        } else {
            // Linux/Unix
            $cmd = 'pkill -f shopify.php 2>&1';
        }
    } else {
        // The exact command you asked for:
        $cmd = sprintf(
            'php %s --direction=%s %d 2>&1',
            escapeshellarg($script),
            $direction,
            DEMO_TENANT_ID
        );
    }

    // Launch and stream
    $proc = proc_open($cmd, [
        ['pipe','r'],  // STDIN—ignored
        ['pipe','w'],  // STDOUT
        ['pipe','w'],  // STDERR
    ], $pipes);

    if (is_resource($proc)) {
        fclose($pipes[0]);
        while (!feof($pipes[1])) {
            $line = fgets($pipes[1]);
            if ($line === false) break;
            // send as SSE
            echo "data: " . trim($line) . "\n\n";
            @ob_flush(); @flush();
        }
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);
    }
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopify ⇄ Flyhub Sync</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height:100vh;">
  <div class="text-center" style="width: 600px;">
    <h1 class="mb-4">Shopify ⇄ Flyhub Sync (Tenant 12)</h1>

    <div class="mb-4">
      <button id="btnShopify" class="btn btn-primary me-2 mb-2">
        Sync Shopify → Flyhub
      </button>
      <button id="btnFlyhub" class="btn btn-secondary me-2 mb-2">
        Sync Flyhub → Shopify
      </button>
      <button id="btnHalt" class="btn btn-danger mb-2">
        Halt Sync
      </button>
    </div>

    <div id="alert" class="alert alert-info d-none"></div>

    <div 
      id="log" 
      class="border rounded bg-white text-start p-3" 
      style="height: 300px; overflow-y: auto; font-family: monospace; white-space: pre-wrap;"
    ></div>
  </div>

  <script>
    let source;

    function startSync(dir) {
      // Close existing stream
      if (source) source.close();

      // Clear log and show alert
      document.getElementById('log').textContent = '';
      const alertEl = document.getElementById('alert');
      alertEl.classList.remove('d-none');
      alertEl.textContent = dir === 'halt'
        ? 'Sending halt signal…'
        : `Running ${dir} sync…`;

      // Open SSE connection
      source = new EventSource(`?direction=${dir}&stream=1`);
      source.onmessage = e => {
        const log = document.getElementById('log');
        log.textContent += e.data + "\n";
        log.scrollTop = log.scrollHeight;
      };
      source.onerror = () => {
        source.close();
        alertEl.textContent = dir === 'halt'
          ? 'Halt complete.'
          : 'Sync complete.';
      };
    }

    document.getElementById('btnShopify').onclick = () => startSync('shopify');
    document.getElementById('btnFlyhub').onclick  = () => startSync('flyhub');
    document.getElementById('btnHalt').onclick    = () => startSync('halt');
  </script>
</body>
</html>
