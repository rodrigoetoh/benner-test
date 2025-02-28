<?php

class MicrowaveProgram extends Microwave {

	private string $title;
	private string $food_description;
	private string $instructions;
	private string $custom_heating_character;

	public function __construct(string $title, string $foodDescription, int $timer = null, int $potency = null, string $instructions, string $customHeatingCharacter) {
		$this->setTitle($title);
		$this->setFoodDescription($foodDescription);
		$this->setTimer(new Timer($timer));
		$this->setPotency(new Potency($potency));
		$this->setInstructions($instructions);
		$this->setCustomHeatingCharacter($customHeatingCharacter);
	}

	public function setTitle(string $title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getSanitizedTitle() {
		return sanitize_title($this->getTitle());
	}

	public function setFoodDescription(string $food_description) {
		$this->food_description = $food_description;
	}

	public function getFoodDescription() {
		return $this->food_description;
	}

	public function setInstructions(string $instructions) {
		$this->instructions = $instructions;
	}

	public function getInstructions() {
		return $this->instructions;
	}

	public function setCustomHeatingCharacter(string $custom_heating_character) {
		$this->custom_heating_character = $custom_heating_character;
	}

	public function getCustomHeatingCharacter() {
		return $this->custom_heating_character;
	}

	public function toArray(){
		return [
			'title' => $this->getTitle(),
			'food_description' => $this->getFoodDescription(),
			// 'timer' => $this->getTimer()->getTime(),
			// 'potency_factor' => $this->getPotency()->getFactor(),
			'instructions' => $this->getInstructions(),
			// 'custom_heating_character' => $this->getCustomHeatingCharacter(),
		];
	}
}