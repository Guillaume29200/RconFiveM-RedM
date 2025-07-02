<?php
class RconFiveM {
    private $socket;
    private $ip;
    private $port;
    private $password;
    private $requestId;

    const SERVERDATA_AUTH = 3;
    const SERVERDATA_AUTH_RESPONSE = 2;
    const SERVERDATA_EXECCOMMAND = 2;
    const SERVERDATA_RESPONSE_VALUE = 0;

    public function __construct($ip, $port, $password) {
        $this->ip = $ip;
        $this->port = $port;
        $this->password = $password;
        $this->requestId = 0;
    }

    public function connect($timeout = 3) {
        $this->socket = @fsockopen($this->ip, $this->port, $errno, $errstr, $timeout);
        if (!$this->socket) {
            throw new Exception("Unable to connect to RCON server: $errstr ($errno)");
        }
        stream_set_timeout($this->socket, $timeout);
        $this->authenticate();
    }

    private function authenticate() {
        $this->requestId = random_int(1, 100000);
        $this->sendPacket($this->requestId, self::SERVERDATA_AUTH, $this->password);
        $response = $this->readPacket();

        if ($response['requestId'] == -1) {
            throw new Exception('Authentication failed: Invalid RCON password.');
        }
        // Some servers send a second empty packet after auth, read it to clear socket
        $this->readPacket();
    }

    public function sendCommand(string $command) {
        $this->requestId++;
        $this->sendPacket($this->requestId, self::SERVERDATA_EXECCOMMAND, $command);
        $response = $this->readPacket();

        // Handle multi-packet responses
        $data = $response['body'];
        while (!$this->isCompleteResponse($data)) {
            $next = $this->readPacket();
            $data .= $next['body'];
        }

        return $data;
    }

    private function sendPacket(int $requestId, int $type, string $body) {
        $body = substr($body, 0, 4096); // max size for RCON body
        $packet = pack('V', $requestId) .
                  pack('V', $type) .
                  $body . "\x00" .
                  "\x00";
        $packetLength = strlen($packet);
        fwrite($this->socket, pack('V', $packetLength) . $packet);
    }

    private function readPacket() {
        $lengthData = fread($this->socket, 4);
        if (strlen($lengthData) < 4) {
            throw new Exception('Failed to read packet length');
        }
        $length = unpack('V', $lengthData)[1];
        $packet = fread($this->socket, $length);
        if (strlen($packet) < $length) {
            throw new Exception('Incomplete packet received');
        }
        $requestId = unpack('V', substr($packet, 0, 4))[1];
        $type = unpack('V', substr($packet, 4, 4))[1];
        $body = substr($packet, 8, strlen($packet) - 10); // remove 2 null terminators

        return [
            'requestId' => $requestId,
            'type' => $type,
            'body' => $body
        ];
    }

    private function isCompleteResponse(string $data): bool {
        // Some servers send split responses, check if response is complete
        // This is a simple heuristic: FiveM often sends responses <= 4096 bytes per packet
        return strlen($data) < 4096;
    }

    public function disconnect() {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }
}
