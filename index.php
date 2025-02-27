<?php get_header(); ?>

<main class="content">
	<div class="container">
		<form action="<?= esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
			<input type="hidden" name="action" value="microondas_post">
			<div class="microondas">
				<div class="inputs">
					<input id="timer" type="text" name="timer" placeholder="Timer">
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
					<button id="btn0" type="button" data-add_time="0">0</button>
					<button id="btnAqc" type="submit">Aquecimento (+30s)</button>
				</div>
			</div>
		</form>
	</div>
</main>

<?php get_footer();
