<?php
namespace HelpScout\model\thread;

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Chat extends AbstractThread {

	public function __construct($data=null) {
		parent::__construct($data);
		$this->setType('chat');
	}
}
