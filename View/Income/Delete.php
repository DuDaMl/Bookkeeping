<?php
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Расходы</h1>
    <h2>Удаление расходов</h2>

    <p><?= $income->description; ?> </p>
    <p><?= $income->amount; ?> грн </p>
    <p><?= $income->date; ?> </p>
    <form action="/<?= $controller_name?>/delete/<?=$income->id?>" method="post">
        <input type="hidden" min="0" step="1" name="id" value="<?=$income->id?>">
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
