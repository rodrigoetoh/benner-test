<?php

class Timer implements Validate {
	private int $time = 0;

	public function __construct(int $time) {
		$this->setTime($time);
	}

	public function setTime($time) {
		$this->time = $time;
	}

	public function getTime() {
		return $this->time;
	}

	public function getFormattedTime($format = 'i:s') {
		return date($format, $this->getTime());
	}

	public function validate() {
		if($this->getTime() < 1 || $this->getTime() > 120) {
			throw new ValidationException('O tempo máximo é 2 minutos e o mínimo é 1 segundo!');
		}
		return true;
	}
}