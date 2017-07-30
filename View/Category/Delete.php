
<?php
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Категория</h1>
    <h2>Удаление Категории</h2>

    <p><?= $category->name; ?>   </p>
    <form action="/Category/delete/<?=$category->id?>" method="post">
        <input type="hidden" min="0" step="1" name="id" value="<?=$category->id?>">
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
