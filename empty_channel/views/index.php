<?php if (count($channels) > 0): ?>
<?=form_open($action_url, '', $form_hidden)?>


<?php
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('channel_name'),
        lang('channel_title')
        ,''//        form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"')
        );

    foreach($channels as $channel)
    {
        $this->table->add_row(
                $channel['channel_name'],
                $channel['channel_title']
                ,                form_checkbox($channel['toggle'])
            );
    }

echo $this->table->generate();

?>

<div class="tableFooter">
    <div class="tableSubmit">
        <?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')).NBS.NBS.form_dropdown('action', $options)?>
    </div>

    <span class="js_hide"><?=$pagination?></span>    
    <span class="pagination" id="filter_pagination"></span>
</div>    

<?=form_close()?>

<?php else: ?>
<?=lang('no_matching_files')?>
<?php endif; ?>