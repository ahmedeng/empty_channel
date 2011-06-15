<?=form_open($form_action)?>
<?php foreach($damned as $file):?>
	<?=form_hidden('empty[]', $file)?>
<?php endforeach;?>

<p class="notice"><?=lang('action_can_not_be_undone')?></p>

<h3><?=lang('empty_channel_question')?></h3>

<p>
	<?=form_submit(array('name' => 'submit', 'value' => lang('empty'), 'class' => 'submit'))?>
</p>

<?=form_close()?>