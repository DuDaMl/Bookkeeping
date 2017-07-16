<?php include_once(__DIR__ . '/../Header.php'); ?>

<?php
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Расходы</h1>
    <h2>Удаление расходов</h2>
    <?php
    // Отображение ошибок добавления новой записи
    if(! isset($data['error']))
    {
    ?>
    <p><?= $pay->description; ?> </p>
    <p><?= $pay->amount; ?> грн </p>
    <p><?= $pay->date; ?> </p>
    <form action="/<?= $controller_name ?>/delete/<?= $pay->id ?>" method="post">
        <input type="hidden" min="0" step="1" name="id" value="<?= $pay->id ?>">
        <button type="submit" class="btn btn-default">Удалить</button>
        <br/>
        <br/>
    </form>
    <?php
    }
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

<?php include_once(__DIR__ . '/../Footer.php'); ?>


</body>
</html>