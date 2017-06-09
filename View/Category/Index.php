<?php include_once(__DIR__ . '\..\Header.php'); ?>


<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Категории</h1>

    <form action="/Category/" method="post">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <input type="text" min="0" step="1" class="form-control" id="exampleInputEmail1" placeholder="Название категории" name="name">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Добавить</button>
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
    <div class="container">
        <div class="row">
            <div class='col-sm-6'>

            </div>

        </div>
    </div>
    <ul class="list-group">
        <?php
        /*
        ?><span href="#" class="list-group-item active">
            Cras justo odio
        </span>
        <?php */ ?>

        <?php
        $output = '';
        $date_current = 0;
        foreach ($categories as $category){

            $output .= '<li class="list-group-item">
                <a href="/Category/edit/' . $category->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                <a href="/Category/delete/' . $category->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                 <strong>' . $category->name . '</strong> </li>';
        }
        echo $output;
        ?>
    </ul>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '\..\Footer.php'); ?>


</body>
</html>