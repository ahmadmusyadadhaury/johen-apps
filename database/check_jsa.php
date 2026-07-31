<?php
$path = __DIR__ . '/../JSA_Office';
if (!file_exists($path)) {
    echo "JSA_Office NOT found\n";
    exit;
}
echo "JSA_Office found, size: " . filesize($path) . " bytes\n";
$db = new PDO("sqlite:$path");
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations' ORDER BY name");
$any = false;
foreach ($tables as $t) {
    $name = $t[0];
    $cnt = $db->query("SELECT COUNT(*) FROM \"$name\"")->fetchColumn();
    if ($cnt > 0) {
        echo "$name: $cnt rows\n";
        $any = true;
    }
}
if (!$any) echo "All tables are empty\n";
