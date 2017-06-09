<?php include_once(__DIR__ . '\..\Header.php'); ?>

<?php

?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Категории</h1>

    <form action="/Category/" method="post">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <input type="text" min="0" step="1" class="form-control" id="exampleInputEmail1"  name="name" value="<?=$category->name?>">
                    <input type="hidden" min="0" step="1" class="form-control" name="id"  value="<?=$category->id?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Редактировать</button>
        <br/>
        <br/>
    </form>
    <?php

    $form_error = $data['error'];
    if($form_error['error'] == 1) {
        unset($form_error['error']);
        foreach ($form_error as $item){
            echo '<div class="alert alert-danger" role="alert">' . $item . '</div>';
        }
    }
    ?>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '\..\Footer.php'); ?>


</body>
</html>