<?php
// Diagnostic v3: dump attendance log record layout from X901 (stride 49 hypothesis)
$ip = '192.168.0.209';
$port = 4370;
$key = 0;
$UM = 65535;

function chk($b)
{
    $l = count($b);
    $c = 0;
    $i = $l;
    $j = 0;
    while ($i > 1) {
        $c += ($b[$j] | ($b[$j + 1] << 8));
        if ($c > $GLOBALS['UM']) {
            $c -= $GLOBALS['UM'];
        }
        $i -= 2;
        $j += 2;
    }
    if ($i) {
        $c += $b[$j];
    }
    while ($c > $GLOBALS['UM']) {
        $c -= $GLOBALS['UM'];
    }
    if ($c > 0) {
        $c = -$c;
    } else {
        $c = abs($c);
    }
    $c -= 1;
    while ($c < 0) {
        $c += $GLOBALS['UM'];
    }
    return $c;
}

function hdr($cmd, $sess, $rep, $pay = '')
{
    $b = array_values(unpack('C*', pack('vvvv', $cmd, 0, $sess, $rep) . $pay));
    $c = chk($b);
    $rep += 1;
    if ($rep >= 65535) {
        $rep -= 65535;
    }
    return pack('vvvv', $cmd, $c, $sess, $rep) . $pay;
}

function ck($k, $s)
{
    $x = 0;
    for ($i = 0; $i < 32; $i++) {
        if ($k & (1 << $i)) {
            $x = ($x << 1) | 1;
        } else {
            $x = $x << 1;
        }
    }
    $x += $s;
    $b = array_values(unpack('C4', pack('V', $x)));
    $b = array($b[0] ^ 0x5A, $b[1] ^ 0x4B, $b[2] ^ 0x53, $b[3] ^ 0x4F);
    $w = array_values(unpack('S2', pack('C4', $b[0], $b[1], $b[2], $b[3])));
    $b = array_values(unpack('C4', pack('S2', $w[1], $w[0])));
    $B = 50;
    return pack('C4', $b[0] ^ $B, $b[1] ^ $B, $B, $b[3] ^ $B);
}

function decodePacked($t)
{
    $second = $t % 60;
    $t = intdiv($t, 60);
    $minute = $t % 60;
    $t = intdiv($t, 60);
    $hour = $t % 24;
    $t = intdiv($t, 24);
    $day = $t % 31 + 1;
    $t = intdiv($t, 31);
    $month = $t % 12 + 1;
    $t = intdiv($t, 12);
    $year = $t + 2000;
    return sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
}

$fp = @fsockopen($ip, $port, $e1, $e2, 3);
stream_set_timeout($fp, 8);
$send = function ($buf) use ($fp) {
    $buf = "\x50\x50\x82\x7d" . pack('V', strlen($buf)) . $buf;
    return @fwrite($fp, $buf);
};
$buffer = '';
$recv = function () use ($fp, &$buffer) {
    while (true) {
        if (strlen($buffer) >= 8) {
            if (substr($buffer, 0, 4) !== "\x50\x50\x82\x7d") {
                $buffer = substr($buffer, 1);
                continue;
            }
            $len = unpack('V', substr($buffer, 4, 4))[1];
            if (strlen($buffer) >= 8 + $len) {
                $pkt = substr($buffer, 8, $len);
                $buffer = substr($buffer, 8 + $len);
                return $pkt;
            }
        }
        $chunk = @fread($fp, 16384);
        if ($chunk === false || $chunk === '') {
            return null;
        }
        $buffer .= $chunk;
    }
};

$send(hdr(1000, 0, 65534));
$r = $recv();
$u = unpack('v4', $r);
$sess = $u[3];
$send(hdr(1102, $sess, 65534, ck($key, $sess)));
$r2 = $recv();
$u2 = unpack('v4', $r2);
$sess = $u2[3];

$send(hdr(13, $sess, $u2[4], ''));
$r3 = $recv();
$u3 = unpack('v4', $r3);
echo "att cmd={$u3[1]}\n";
if ($u3[1] == 1500) {
    $size = unpack('V', substr($r3, 8, 4))[1];
    echo "size=$size\n";
    $data = '';
    $received = 0;
    while ($received < $size) {
        $pkt = $recv();
        if ($pkt === null) {
            break;
        }
        $u4 = unpack('v4', $pkt);
        if ($u4[1] == 1501) {
            $chunk = substr($pkt, 8);
            $data .= $chunk;
            $received += strlen($chunk);
        }
    }
    $data = substr($data, 2);
    $L = strlen($data);
    echo "len=$L\n";

    echo "\n--- HEXDUMP 0..719 ---\n";
    for ($r = 0; $r < 45; $r++) {
        $off = $r * 16;
        $hex = bin2hex(substr($data, $off, 16));
        $hex = implode(' ', str_split($hex, 2));
        $ascii = '';
        for ($i = 0; $i < 16; $i++) {
            $c = ord($data[$off + $i] ?? 0);
            $ascii .= ($c >= 32 && $c < 127) ? chr($c) : '.';
        }
        printf("%05d  %-47s  %s\n", $off, $hex, $ascii);
    }

    echo "\n--- GRID 49 (first 14) ---\n";
    for ($g = 0; $g < 14 * 49; $g += 49) {
        $rec = substr($data, $g, 49);
        if (strlen($rec) < 49) {
            break;
        }
        $ts = unpack('V', substr($rec, 29, 4))[1];
        printf("g=%3d hex=%s\n", $g, bin2hex($rec));
        printf("      V@0=%d uid='%s' b13..27=%s b28=0x%02x ts=%s b33=0x%02x tail34..48=%s\n",
            unpack('V', substr($rec, 0, 4))[1],
            rtrim(substr($rec, 4, 9), "\x00"),
            bin2hex(substr($rec, 13, 15)),
            ord($rec[28]),
            decodePacked($ts),
            ord($rec[33]),
            bin2hex(substr($rec, 34, 15)));
    }

    echo "\n--- GRID SCAN (all) ---\n";
    $valid = 0;
    $total = 0;
    $stateCount = [];
    $b33Count = [];
    $userCount = [];
    $minTs = PHP_INT_MAX;
    $maxTs = 0;
    for ($g = 0; $g + 49 <= $L; $g += 49) {
        $total++;
        $ts = unpack('V', substr($data, $g + 29, 4))[1];
        $sec = $ts % 60;
        $t = intdiv($ts, 60);
        $min = $t % 60;
        $t = intdiv($t, 60);
        $hour = $t % 24;
        $t = intdiv($t, 24);
        $day = $t % 31 + 1;
        $t = intdiv($t, 31);
        $month = $t % 12 + 1;
        $t = intdiv($t, 12);
        $year = $t + 2000;
        $ok = $month >= 1 && $month <= 12 && $day >= 1 && $day <= 31
            && $hour <= 23 && $min <= 59 && $sec <= 59
            && $year >= 2000 && $year <= 2100;
        if (!$ok) {
            continue;
        }
        $valid++;
        $b28 = ord($data[$g + 28]);
        $b33 = ord($data[$g + 33]);
        $stateCount[$b28] = ($stateCount[$b28] ?? 0) + 1;
        $b33Count[$b33] = ($b33Count[$b33] ?? 0) + 1;
        $uid = rtrim(substr($data, $g + 4, 9), "\x00");
        if ($uid !== '') {
            $userCount[$uid] = ($userCount[$uid] ?? 0) + 1;
        }
        $minTs = min($minTs, $ts);
        $maxTs = max($maxTs, $ts);
    }
    echo "total grids=$total valid-ts=$valid\n";
    ksort($stateCount);
    echo 'b28 (state) hist: ' . json_encode($stateCount) . "\n";
    ksort($b33Count);
    echo 'b33 hist: ' . json_encode($b33Count) . "\n";
    echo "min ts: " . decodePacked($minTs) . " ($minTs)\n";
    echo "max ts: " . decodePacked($maxTs) . " ($maxTs)\n";
    arsort($userCount);
    echo 'distinct userids: ' . count($userCount) . "\n";
    print_r(array_slice($userCount, 0, 10, true));

    echo "\n--- TEMP PATTERN (ff+dd.d) ---\n";
    $m = [];
    preg_match_all('/\xff\d\d\.\d/', $data, $m, PREG_OFFSET_CAPTURE);
    echo 'temp strings: ' . count($m[0]) . "\n";
    $mods = [];
    foreach (array_slice($m[0], 0, 30) as $x) {
        $mods[] = $x[1] % 49 . ":" . $x[0];
    }
    echo implode(' ', $mods) . "\n";

    echo "\n--- FOOTER (last 12 bytes) ---\n";
    echo bin2hex(substr($data, -12)) . "\n";
}
fclose($fp);
