<?php

class Microwave implements Validate {

	private Timer $timer;
	private Potency $potency;

	public function __construct(int $timer = null, int $potency = null) {
		$this->setTimer(new Timer($timer));
		$this->setPotency(new Potency($potency));
	}

	public function setTimer(Timer $timer) {
		$this->timer = $timer;
	}

	public function getTimer() {
		return $this->timer;
	}

	public function setPotency(Potency $potency) {
		$this->potency = $potency;
	}

	public function getPotency() {
		return $this->potency;
	}

	public function validate() {
		$this->getTimer()->validate();
		$this->getPotency()->validate();
		return true;
	}
}