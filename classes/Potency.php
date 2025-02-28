<?php

class Potency implements Validate {
	private int $factor = 0;

	public function __construct(int $factor) {
		$this->setFactor($factor);
	}

	public function setFactor($factor) {
		$this->factor = $factor;
	}

	public function getFactor() {
		return $this->factor;
	}

	public function validate() {
		if($this->getFactor() < 1 || $this->getFactor() > 10) {
			throw new ValidationException('Informe uma potência válida! (Máx: 10 / Min: 1)');
		}
		return true;
	}
}