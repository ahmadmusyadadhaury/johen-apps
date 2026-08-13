<?php

namespace App\Services\ZkMachine;

class ZkClient
{
    const USHRT_MAX = 65535;

    const CMD_CONNECT = 1000;

    const CMD_EXIT = 1001;

    const CMD_ENABLE_DEVICE = 1002;

    const CMD_DISABLE_DEVICE = 1003;

    const CMD_GET_TIME = 201;

    const CMD_SET_TIME = 202;

    const CMD_VERSION = 1100;

    const CMD_ACK_AUTH = 1102;

    const CMD_REG_EVENT = 500;

    const CMD_USER_TEMP_RRQ = 9;

    const CMD_ATT_LOG_RRQ = 13;

    const CMD_DEVICE = 11;

    const CMD_PREPARE_DATA = 1500;

    const CMD_DATA = 1501;

    const CMD_FREE_DATA = 1502;

    const CMD_ACK_OK = 2000;

    const CMD_ACK_ERROR = 2001;

    const CMD_ACK_DATA = 2002;

    const CMD_ACK_UNAUTH = 2005;

    const EF_ATTLOG = 1;

    const EF_FINGER = 2;

    const TCP_HEADER = "\x50\x50\x82\x7d";

    private string $host;

    private int $port;

    private int $commKey;

    private int $timeout;

    private $fp = null;

    private int $sessionId = 0;

    private int $replyId = 0;

    private string $buffer = '';

    private array $eventQueue = [];

    public function __construct(string $host, int $port = 4370, int $commKey = 0, int $timeout = 5)
    {
        $this->host = $host;
        $this->port = $port;
        $this->commKey = $commKey;
        $this->timeout = $timeout;
    }

    public function isConnected(): bool
    {
        return is_resource($this->fp) && ! feof($this->fp);
    }

    public function connect(): bool
    {
        $this->disconnect();

        $this->fp = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (! is_resource($this->fp)) {
            return false;
        }

        stream_set_timeout($this->fp, $this->timeout);
        $this->buffer = '';
        $this->eventQueue = [];

        $this->write($this->makePacket(self::CMD_CONNECT, 0, 65534, ''));

        $resp = $this->readPacket();
        if ($resp === null || strlen($resp) < 8) {
            $this->disconnect();

            return false;
        }

        $hdr = $this->parseHeader($resp);
        $cmd = $hdr['command'];

        if ($cmd === self::CMD_ACK_UNAUTH) {
            $this->sessionId = $hdr['session'];
            $this->replyId = $hdr['reply'];

            $key = $this->makeCommKey($this->commKey, $this->sessionId);
            $this->write($this->makePacket(self::CMD_ACK_AUTH, $this->sessionId, 65534, $key));

            $resp2 = $this->readPacket();
            if ($resp2 === null || strlen($resp2) < 8) {
                $this->disconnect();

                return false;
            }

            $hdr2 = $this->parseHeader($resp2);
            if ($hdr2['command'] === self::CMD_ACK_UNAUTH) {
                $this->disconnect();

                return false;
            }

            $this->sessionId = $hdr2['session'];
            $this->replyId = $hdr2['reply'];

            return in_array($hdr2['command'], [self::CMD_ACK_OK, self::CMD_ACK_AUTH, self::CMD_ACK_DATA], true);
        }

        if (in_array($cmd, [self::CMD_ACK_OK, self::CMD_ACK_AUTH], true)) {
            $this->sessionId = $hdr['session'];
            $this->replyId = $hdr['reply'];

            return true;
        }

        $this->disconnect();

        return false;
    }

    public function disconnect(): void
    {
        if (is_resource($this->fp)) {
            @fclose($this->fp);
        }
        $this->fp = null;
        $this->buffer = '';
        $this->eventQueue = [];
        $this->sessionId = 0;
        $this->replyId = 0;
    }

    public function ping(): bool
    {
        if (! $this->isConnected() && ! $this->connect()) {
            return false;
        }

        $res = $this->command(self::CMD_GET_TIME, '');

        return $res !== false;
    }

    public function getVersion(): ?string
    {
        $res = $this->command(self::CMD_VERSION, '');
        if ($res === false) {
            return null;
        }

        return trim($res['payload']);
    }

    public function getTime(): ?string
    {
        $res = $this->command(self::CMD_GET_TIME, '');
        if ($res === false || strlen($res['payload']) < 4) {
            return null;
        }

        return $this->decodeTime(unpack('V', substr($res['payload'], 0, 4))[1]);
    }

    public function getAttendanceLogs(): array
    {
        $res = $this->command(self::CMD_ATT_LOG_RRQ, '');
        if ($res === false) {
            return [];
        }

        $size = 0;
        if ($res['command'] === self::CMD_PREPARE_DATA && strlen($res['payload']) >= 4) {
            $size = unpack('V', substr($res['payload'], 0, 4))[1];
        }

        if ($size <= 0) {
            return [];
        }

        $data = $this->receiveData($size);
        if ($data === '') {
            return [];
        }

        $data = substr($data, 2);

        $logs = [];
        while (strlen($data) >= 49) {
            $rec = substr($data, 0, 49);
            $data = substr($data, 49);

            $rawTimestamp = unpack('V', substr($rec, 29, 4))[1];
            $userId = rtrim(substr($rec, 4, 9), "\x00");

            if ($rawTimestamp === 0 || $userId === '') {
                continue;
            }

            $logs[] = [
                'uid' => unpack('v', substr($rec, 0, 2))[1],
                'seq' => ord($rec[2]),
                'user_id' => $userId,
                'state' => ord($rec[28]),
                'verify' => ord($rec[33]),
                'record_time' => $this->decodeTime($rawTimestamp),
                'temperature' => trim(substr($rec, 43, 4), "\x00"),
            ];
        }

        return $logs;
    }

    public function getUsers(): array
    {
        $res = $this->command(self::CMD_USER_TEMP_RRQ, chr(5));
        if ($res === false) {
            return [];
        }

        $size = 0;
        if ($res['command'] === self::CMD_PREPARE_DATA && strlen($res['payload']) >= 4) {
            $size = unpack('V', substr($res['payload'], 0, 4))[1];
        }

        if ($size <= 0) {
            return [];
        }

        $data = $this->receiveData($size);
        if ($data === '') {
            return [];
        }

        $data = substr($data, 3);

        $users = [];
        while (strlen($data) >= 72) {
            $rec = substr($data, 0, 72);
            $data = substr($data, 72);

            // PIN disimpan 9 byte; potong di NUL pertama karena mesin kerap
            // menyisakan byte lama setelah terminator (mis. "51\0 26\0...").
            $rawPin = substr($rec, 49, 9);
            $nul = strpos($rawPin, "\0");
            $userId = $nul === false ? rtrim($rawPin, "\0") : substr($rawPin, 0, $nul);
            if ($userId === '') {
                $userId = (string) unpack('v', substr($rec, 1, 2))[1];
            }

            $rawName = substr($rec, 12, 24);
            $nul = strpos($rawName, "\0");
            $name = $nul === false ? rtrim($rawName, "\0") : substr($rawName, 0, $nul);

            $users[$userId] = [
                'uid' => unpack('v', substr($rec, 1, 2))[1],
                'user_id' => $userId,
                'role' => ord($rec[3]),
                'password' => $this->cstring($rec, 4, 8),
                'name' => trim($name),
                'card_no' => unpack('V', substr($rec, 36, 4))[1],
            ];
        }

        return $users;
    }

    private function cstring(string $record, int $offset, int $length): string
    {
        $raw = substr($record, $offset, $length);
        $nul = strpos($raw, "\0");

        return $nul === false ? rtrim($raw, "\0") : substr($raw, 0, $nul);
    }

    public function enableRealtime(int $events = self::EF_ATTLOG): bool
    {
        $res = $this->command(self::CMD_REG_EVENT, pack('V', $events));

        return $res !== false && in_array($res['command'], [self::CMD_ACK_OK, self::CMD_ACK_DATA, self::CMD_ACK_AUTH], true);
    }

    public function readRealtimeEvent(int $waitSeconds = 30): ?array
    {
        if (! empty($this->eventQueue)) {
            $packet = array_shift($this->eventQueue);

            return $this->decodeRealtimeEvent($packet);
        }

        if (! is_resource($this->fp)) {
            return null;
        }

        $read = [$this->fp];
        $write = null;
        $except = null;
        $sel = @stream_select($read, $write, $except, $waitSeconds, 0);

        if ($sel === false || $sel === 0) {
            $this->command(self::CMD_GET_TIME, '');

            return null;
        }

        $packet = $this->readPacket();
        if ($packet === null || strlen($packet) < 8) {
            return null;
        }

        $hdr = $this->parseHeader($packet);
        if ($hdr['command'] !== self::CMD_REG_EVENT) {
            $this->sessionId = $hdr['session'];
            $this->replyId = $hdr['reply'];

            return null;
        }

        return $this->decodeRealtimeEvent($packet);
    }

    private function decodeRealtimeEvent(string $packet): ?array
    {
        if (strlen($packet) < 8) {
            return null;
        }

        $eventType = unpack('v', substr($packet, 4, 2))[1];
        if ($eventType !== self::EF_ATTLOG) {
            return null;
        }

        $recv = substr($packet, 8);
        if (strlen($recv) < 32) {
            return null;
        }

        $userId = rtrim(substr($recv, 0, 9), "\x00");

        return [
            'user_id' => $userId,
            'record_time' => sprintf(
                '%04d-%02d-%02d %02d:%02d:%02d',
                2000 + ord($recv[26]),
                ord($recv[27]),
                ord($recv[28]),
                ord($recv[29]),
                ord($recv[30]),
                ord($recv[31])
            ),
            'state' => ord($recv[24]),
        ];
    }

    private function command(int $command, string $payload = '')
    {
        $pkt = $this->makePacket($command, $this->sessionId, $this->replyId, $payload);
        if (! $this->write($pkt)) {
            return false;
        }

        $packet = $this->readPacketForCommand();
        if ($packet === null || strlen($packet) < 8) {
            return false;
        }

        $hdr = $this->parseHeader($packet);
        $this->sessionId = $hdr['session'];
        $this->replyId = $hdr['reply'];

        return [
            'command' => $hdr['command'],
            'session' => $hdr['session'],
            'reply' => $hdr['reply'],
            'payload' => substr($packet, 8),
        ];
    }

    private function readPacketForCommand(int $maxReads = 30)
    {
        while ($maxReads-- > 0) {
            $packet = $this->readPacket();
            if ($packet === null || strlen($packet) < 8) {
                return null;
            }

            $command = unpack('v', substr($packet, 0, 2))[1];
            if ($command === self::CMD_REG_EVENT) {
                $this->eventQueue[] = $packet;

                continue;
            }

            return $packet;
        }

        return null;
    }

    private function receiveData(int $size, int $maxErrors = 10): string
    {
        $data = '';
        $received = 0;
        $errors = 0;

        while ($received < $size && $errors < $maxErrors) {
            $packet = $this->readPacket();
            if ($packet === null || strlen($packet) < 8) {
                $errors++;
                usleep(200000);

                continue;
            }

            $hdr = $this->parseHeader($packet);
            if ($hdr['command'] === self::CMD_PREPARE_DATA && strlen($packet) >= 12) {
                $size = max($size, unpack('V', substr($packet, 8, 4))[1]);

                continue;
            }

            if ($hdr['command'] !== self::CMD_DATA) {
                $errors++;

                continue;
            }

            $chunk = substr($packet, 8);
            $data .= $chunk;
            $received += strlen($chunk);
        }

        $final = $this->readPacket();
        if ($final !== null && strlen($final) >= 8) {
            $hdr = $this->parseHeader($final);
            $this->sessionId = $hdr['session'];
            $this->replyId = $hdr['reply'];
        }

        return $data;
    }

    private function write(string $data): bool
    {
        if (! is_resource($this->fp)) {
            return false;
        }

        $frame = self::TCP_HEADER.pack('V', strlen($data)).$data;
        $written = @fwrite($this->fp, $frame);

        return $written !== false && $written > 0;
    }

    private function readPacket(int $maxReads = 50)
    {
        while ($maxReads-- > 0) {
            $frame = $this->extractFrame();
            if ($frame !== null) {
                return $frame;
            }

            $chunk = @fread($this->fp, 16384);
            if ($chunk === false || $chunk === '') {
                if (! is_resource($this->fp) || feof($this->fp)) {
                    return null;
                }
                usleep(100000);

                continue;
            }

            $this->buffer .= $chunk;
        }

        return null;
    }

    private function extractFrame()
    {
        while (strlen($this->buffer) >= 8) {
            if (substr($this->buffer, 0, 4) !== self::TCP_HEADER) {
                $this->buffer = substr($this->buffer, 1);

                continue;
            }

            $len = unpack('V', substr($this->buffer, 4, 4))[1];
            if (strlen($this->buffer) < 8 + $len) {
                return null;
            }

            $packet = substr($this->buffer, 8, $len);
            $this->buffer = substr($this->buffer, 8 + $len);

            return $packet;
        }

        return null;
    }

    private function parseHeader(string $packet): array
    {
        $u = unpack('v4', substr($packet, 0, 8));

        return [
            'command' => $u[1],
            'checksum' => $u[2],
            'session' => $u[3],
            'reply' => $u[4],
        ];
    }

    private function makePacket(int $command, int $session, int $reply, string $payload): string
    {
        $raw = pack('vvvv', $command, 0, $session, $reply).$payload;
        $bytes = array_values(unpack('C*', $raw));
        $checksum = $this->checksum($bytes);

        $reply = $reply + 1;
        if ($reply >= self::USHRT_MAX) {
            $reply -= self::USHRT_MAX;
        }

        return pack('vvvv', $command, $checksum, $session, $reply).$payload;
    }

    private function checksum(array $bytes): int
    {
        $sum = 0;
        $count = count($bytes);
        $i = 0;

        while ($i + 1 < $count) {
            $sum += $bytes[$i] | ($bytes[$i + 1] << 8);
            if ($sum > self::USHRT_MAX) {
                $sum -= self::USHRT_MAX;
            }
            $i += 2;
        }

        if ($i < $count) {
            $sum += $bytes[$i];
        }

        while ($sum > self::USHRT_MAX) {
            $sum -= self::USHRT_MAX;
        }

        if ($sum > 0) {
            $sum = -$sum;
        } else {
            $sum = abs($sum);
        }

        $sum -= 1;
        while ($sum < 0) {
            $sum += self::USHRT_MAX;
        }

        return $sum;
    }

    private function makeCommKey(int $key, int $sessionId, int $ticks = 50): string
    {
        $k = 0;
        for ($i = 0; $i < 32; $i++) {
            if ($key & (1 << $i)) {
                $k = ($k << 1) | 1;
            } else {
                $k = $k << 1;
            }
        }

        $k += $sessionId;
        $b = array_values(unpack('C4', pack('V', $k)));
        $b = [$b[0] ^ ord('Z'), $b[1] ^ ord('K'), $b[2] ^ ord('S'), $b[3] ^ ord('O')];
        $w = array_values(unpack('S2', pack('C4', $b[0], $b[1], $b[2], $b[3])));
        $b = array_values(unpack('C4', pack('S2', $w[1], $w[0])));

        $B = 0xFF & $ticks;

        return pack('C4', $b[0] ^ $B, $b[1] ^ $B, $B, $b[3] ^ $B);
    }

    private function decodeTime(int $t): string
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
}
