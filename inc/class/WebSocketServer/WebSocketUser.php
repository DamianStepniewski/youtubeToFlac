<?php
namespace WebSocketServer;
class WebSocketUser {

	public $socket;
	public $id;
	public $headers = [];
	public $handshake = false;

	public $handlingPartialPacket = false;
	public $partialBuffer = "";

	public $sendingContinuous = false;
	public $partialMessage = "";

	public $hasSentClose = false;
	
	public $cookies = [];

    /**
     * WebSocketUser constructor.
     * @param int $id
     * @param int $socket
     * @throws \Exception
     */
    public function __construct($id, $socket) {
		$this->id = $id;
		$this->socket = $socket;
	}

    /**
     * Parses user cookies and stores them in this object
     */
    public function parseCookies() {
	    if (isset($this->headers['cookie'])) {
            $cookies = explode('; ', $this->headers['cookie']);

            foreach ($cookies as $cookie) {
                $exploded = explode('=', $cookie);
                $this->cookies[$exploded[0]] = $exploded[1];
            }
        }
	}
}