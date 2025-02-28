<?php
	$programas = getAllMicrowavePrograms();
?>
<?php get_header(); ?>

<main class="content">
	<div class="container">
		<div id="debug"></div>
	</div>
	<div class="container">
		<form class="form form-ajax" action="<?= esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
			<input type="hidden" name="action" value="microondas_post">
			<div class="microondas">
				<div class="inputs">
					<div class="visor-container">
						<input id="visor" type="text" placeholder="00:00" disabled>
						<input id="timer" type="text" name="timer" placeholder="Timer">
					</div>
					<input id="potencia" type="text" name="potencia" placeholder="Potência">
				</div>
				<div class="keyboard">
					<button id="btn7" type="button" data-add_time="7">7</button>
					<button id="btn8" type="button" data-add_time="8">8</button>
					<button id="btn9" type="button" data-add_time="9">9</button>
					<button id="btn4" type="button" data-add_time="4">4</button>
					<button id="btn5" type="button" data-add_time="5">5</button>
					<button id="btn6" type="button" data-add_time="6">6</button>
					<button id="btn1" type="button" data-add_time="1">1</button>
					<button id="btn2" type="button" data-add_time="2">2</button>
					<button id="btn3" type="button" data-add_time="3">3</button>
					<button id="btnCanc" type="button">Cancelar</button>
					<button id="btn0" type="button" data-add_time="0">0</button>
					<button id="btnAqc" type="submit">Aquecimento (+30s)</button>
				</div>
				<div class="status"></div>
			</div>
		</form>
		<form class="form form-ajax" action="<?= esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
			<input type="hidden" name="action" value="microondas_program_post">
			<div class="programas">
				<ul>
				<?php foreach ($programas as $p): ?>
					<li>
						<div class="programa" data-html="<?php echo htmlentities(sprintf('<div>
								<p>Alimento: %s</p>
								<p>Instruções: %s</p>
							</div>', $p->getFoodDescription(), $p->getInstructions())) ?>">
							<p>
								<label data-programa="<?php echo $p->getSanitizedTitle() ?>">
									<input type="radio" name="programa" value="<?php echo $p->getSanitizedTitle() ?>">
									<span>
										<?php if (!$p->isDefaultProgram()): ?>
											<i><?php echo $p->getTitle() ?></i>
										<?php else: ?>
											<?php echo $p->getTitle() ?>
										<?php endif ?>
									</span>
								</label>
							</p>
						</div>
					</li>
				<?php endforeach ?>
				</ul>
				<a href="#cadastro" rel="modal:open">
					<button id="btnCadastroNovo" type="button">Cadastrar Novo Programa</button>
				</a>
				<div id="programaDescricao"></div>
			</div>
		</form>
	</div>
</main>

<div id="cadastro" class="modal">
	<form class="form form-ajax" action="<?= esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
		<input type="hidden" name="action" value="microondas_new_program_post">

		<div>
			<label for="new_title">Nome</label>
			<div>
				<input id="new_title" type="text" name="title" required>
			</div>
		</div>

		<div>
			<label for="new_food_description">Alimento</label>
			<div>
				<input id="new_food_description" type="text" name="food_description" required>
			</div>
		</div>

		<div>
			<label for="new_timer">Tempo (em segundos)</label>
			<div>
				<input id="new_timer" type="text" name="timer" required>
			</div>
		</div>

		<div>
			<label for="new_potency">Potência (Preencha de 1 até 10)</label>
			<div>
				<input id="new_potency" type="text" name="potency" required>
			</div>
		</div>

		<div>
			<label for="new_instructions">Instruções</label>
			<div>
				<textarea id="new_instructions" name="instructions"></textarea>
			</div>
		</div>

		<div>
			<label for="new_custom_heating_character">Caractere customizado (1 caractere apenas)</label>
			<div>
				<input id="new_custom_heating_character" type="text" name="custom_heating_character" required>
			</div>
		</div>


  		<button type="submit">Cadastrar</button>
  		<a href="#" rel="modal:close">Cancelar</a>
	</form>
</div>

<?php get_footer();
