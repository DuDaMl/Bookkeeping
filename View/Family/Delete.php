
<?php
//stdClass Object ( [id] => 10 [date] => 2017-06-29 [family_name] => Dtest [given_name] => test [picture] => http://s1.iconbird.com/ico/2013/12/505/w450h4001385925286User.png )
//print_r($data);
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Удаление запроса</h1>

    <p><?= $deleting_user->family_name; ?> <?= $deleting_user->given_name; ?></p>
    <p><img src="<?= $deleting_user->picture; ?>" ></p>
    <form action="/<?= $controller_name ?>/delete/<?=$relationship->id?>" method="post">
        <input type="hidden" min="0" step="1" name="relationship_id" value="<?=$relationship->id?>">
        <button type="submit" class="btn btn-default">Удалить</button>
        <br/>
        <br/>
    </form>
    <?php
    // Отображение ошибок добавления новой записи
    if(isset($data['error']))
    {
        $form_error = $data['error'];

        if($form_error['error'] == 1)
        {
            unset($form_error['error']);
            foreach ($form_error as $item){
                echo '<div class="alert alert-danger" role="alert">' . $item . '</div>';
            }
        }
    }
    ?>
</div>

</div> <!-- /container -->
