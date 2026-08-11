<?php
// Diagnostic: dump raw user record layout from X901
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

$fp = @fsockopen($ip, $port, $e1, $e2, 3);
stream_set_timeout($fp, 5);
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
echo "auth cmd={$u2[1]} session={$u2[3]}\n";
$sess = $u2[3];

$send(hdr(9, $sess, $u2[4], chr(5)));
$r3 = $recv();
$u3 = unpack('v4', $r3);
echo "users cmd={$u3[1]}\n";
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

// skip first 3 bytes
$data = substr($data, 3);
echo "records: " . intdiv(strlen($data), 72) . "\n";
$count = 0;
while (strlen($data) >= 72) {
    $rec = substr($data, 0, 72);
    $data = substr($data, 72);
    $count++;
    $name = rtrim(substr($rec, 12, 24), "\x00");
    echo str_pad('#' . $count, 4) . ' ' . str_pad($name, 22) . ' | id_hex: ' . bin2hex(substr($rec, 49, 9)) . ' | id: "' . rtrim(substr($rec, 49, 9), "\x00") . '"' . "\n";
}
fclose($fp);
