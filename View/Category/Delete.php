<?php include_once(__DIR__ . '\..\Header.php'); ?>

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


    /*f($error['error'] == 1) {
        echo '<div class="alert alert-danger" role="alert">' . $error['text'] . '</div>';
    }
    */
    if($error['error'] == 1) {
        unset($error['error']);
        foreach ($error as $item){
            echo '<div class="alert alert-danger" role="alert">' . $item . '</div>';
        }
    }

    ?>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '\..\Footer.php'); ?>


</body>
</html>