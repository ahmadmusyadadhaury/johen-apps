<?php
$db = new PDO('sqlite:database/database.sqlite');
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations' ORDER BY name");
foreach ($tables as $t) {
    $name = $t[0];
    $cnt = $db->query("SELECT COUNT(*) FROM \"$name\"")->fetchColumn();
    if ($cnt > 0) {
        echo "$name: $cnt rows\n";
    }
}
