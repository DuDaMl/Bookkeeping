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
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h2>Подтверждение запроса для:</h2>

    <p><?=$relationship->given_name; ?></p>
    <p><?=$relationship->family_name; ?></p>
    <img src="<?= $relationship->picture; ?>">

    <form action="/<?= $controller_name ?>/confirm/<?=$relationship->id?>" method="post">
        <input type="hidden" min="0" step="1" name="relationship_id" value="<?=$relationship->id?>">
        <button type="submit" class="btn btn-default">Подтвердить</button>
        <br/>
        <br/>
    </form>

</div>

</div> <!-- /container -->
