<?php
// Diagnostic: raw realtime event capture from X901
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

echo "auth ok, sess=$sess\n";

$send(hdr(500, $sess, $u2[4], pack('V', 1)));
$r3 = $recv();
$u3 = unpack('v4', $r3);
echo "reg event resp cmd={$u3[1]} pay=" . bin2hex(substr($r3, 8)) . "\n";

echo "Menunggu event tap (60s)...\n";
$deadline = time() + 60;
while (time() < $deadline) {
    $read = [$fp];
    $w = null;
    $e = null;
    $sel = @stream_select($read, $w, $e, 5, 0);
    if ($sel === false || $sel === 0) {
        echo ' .';
        continue;
    }
    $pkt = $recv();
    if ($pkt === null || strlen($pkt) < 8) {
        echo "\nrecv null\n";
        break;
    }
    $h = unpack('v4', $pkt);
    echo "\nPACKET cmd={$h[1]} len=" . strlen($pkt) . " pay=" . bin2hex(substr($pkt, 8)) . "\n";
    if ($h[1] == 500 && strlen($pkt) >= 8 + 32) {
        $ev = substr($pkt, 8);
        $uid = rtrim(substr($ev, 0, 9), "\x00");
        printf("user='%s' state=%d verify=%d year=%d ymdhms=%02d-%02d-%02d %02d:%02d:%02d\n",
            $uid, ord($ev[24]), ord($ev[25]),
            2000 + ord($ev[26]), ord($ev[27]), ord($ev[28]),
            ord($ev[29]), ord($ev[30]), ord($ev[31]));
    }
}
echo "\nselesai\n";
fclose($fp);
