<?php
namespace WebSocketServer;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Flac;
use FFMpeg\Format\Audio\Mp3;
use URIParser;

class WebSocket extends WebSocketServer {

	private $isTranscoding = false;

    /**
     * Process incoming messages
     *
     * @param WebSocketUser $user Connected user
     * @param string $message
     * @throws \Exception
     */
    protected function process ($user, $message) {
		$this->stdout('User sent message: ' . $message);
		$parsed = json_decode($message, true);

		if ($parsed['action'] === 'transcode') {
            $dir = opendir('../temp');
            while (false !== ($entry = readdir($dir))) {
                if ($entry != '..' && $entry != '.' && !empty($entry)) {
                    if (filemtime('../temp/'.$entry)+60*60 < time()) {
                        unlink('../temp/'.$entry);
                    }
                }
            }
            closedir($dir);
            if (!$this->isTranscoding) {
                $this->isTranscoding = true;
                $FFMpeg = FFMpeg::create();
                $data = URIParser::getVideoURI($parsed['url']);
                if ($data) {
                    $video = $FFMpeg->open($data['url']);
                    $ext = '.flac';
                    if ($parsed['output'] == 'flac') {
                        $format = new Flac();
                    } else {
                        $ext = '.mp3';
                        $format = new Mp3();
                    }

                    $time = time();
                    $format->on('progress', function ($video, $format, $percentage) use ($user, $time) {
                        $estimatedTime = (time() - $time) / ($percentage / 100) - (time() - $time);
                        $this->send($user, json_encode(['app' => 'YT', 'action' => 'transcodingProgress', 'progress' => $percentage, 'estimatedTime' => 'Estimated time remaining: '.ceil($estimatedTime). 's.']));
                    });
                    $data['title'] = str_replace(' ', '_', $data['title']);
                    $data['title'] = substr($data['title'], 0, 17) . substr($data['title'], -3);
                    $video->save($format, '../temp/'.$data['title'].$ext);
                    $this->send($user, json_encode(['app' => 'YT', 'action' => 'result', 'status' => 'OK', 'filename' => $data['title'].$ext]));
                } else {
                    $this->send($user, json_encode(['app' => 'YT', 'action' => 'result', 'status' => 'ERROR', 'message' => 'Invalid video.']));
                }
                $this->isTranscoding = false;
            } else {
                $this->send($user, json_encode(['app' => 'YT', 'action' => 'result', 'status' => 'ERROR', 'message' => 'Only 1 video can be processed at a time, try again in a minute.']));
            }
		}
	}

    /**
     * Handle incoming connection
     *
     * @param WebSocketUser $user Connected user
     * @throws \Exception
     */
    protected function connected ($user) {
        $this->stdout('User connected');
	}

    /**
     * Handle closing connection
     *
     * @param WebSocketUser $user Connected user
     */
    protected function closed ($user) {
		unset($user->user);
	}
}