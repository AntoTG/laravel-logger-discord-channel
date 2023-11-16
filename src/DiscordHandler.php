<?php

namespace KABBOUCHI\LoggerDiscordChannel;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Monolog\Formatter\LineFormatter;
use \Monolog\Logger;
use \Monolog\Level;
use \Monolog\Handler\AbstractProcessingHandler;

class DiscordHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $guzzle;

    private $name;
    private $subname;

    private $webhook;
    private $statement;
    private $roleId;

    private $username;
    private $avatarSrc;

	/**
	 * MonologDiscordHandler constructor.
	 * @param $webhook
	 * @param $name
	 * @param string $subname
	 * @param int $level
	 * @param bool $bubble
	 * @param null $roleId
	 */
    public function __construct($webhook, $name, $subname = '', $level = Level::Debug, $bubble = true, $roleId = null, $username, $avatarSrc)
    {
        $this->name = $name;
        $this->subname = $subname;
        $this->guzzle = new \GuzzleHttp\Client();
		$this->webhook = $webhook;
		$this->roleId = $roleId;
        $this->username = $username;
        $this->avatarSrc = $avatarSrc;
        $this->level = Level::fromName($level);
        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function write(\Monolog\LogRecord $record): void
    {

        if($record['level']==Level::Debug->value || $record['level']==Level::Info->value){
            $color = hexdec("3366ff");
        }elseif($record['level']==Level::Error->value){
            $color = hexdec("f44545");
        }else{
            $color = hexdec("6aa84f");
        }

        // Set up the formatted log
        $log = [

            'avatar_url' => $this->avatarSrc,

            'username' => $this->username,

            'embeds' => [
                [
                    'title' => 'Log from ' . $this->name,
                    // Use CSS for the formatter, as it provides the most distinct colouring.
                    'description' => substr($record['message'], 0, 2030),
                    'color' => $color,

                ],
            ],
        ];

        // Tag a role if configured for it
        if($this->roleId) $log['content'] = "<@&" . $this->roleId . ">";

        // Send it to discord
        $this->guzzle->request('POST', $this->webhook, [
            RequestOptions::JSON => $log,
        ]);
    }
}

