<?php

namespace antotg\LoggerDiscordChannel;

use Monolog\Level;
use Monolog\Logger;

class DiscordLogger
{
	/**
	 * Create a custom Monolog instance.
	 *
	 * @param  array $config
	 * @return \Monolog\Logger
	 */
	public function __invoke(array $config)
	{
		$log = new Logger('discord');

		if($config['active']){
			$log->pushHandler(new DiscordHandler($config['webhook'], config('app.name'), null, $config['level'] ?? Level::Debug, true, $config['role_id'] ?? null, $config['username'], $config['avatar_src']));
		}

		return $log;
	}
}

