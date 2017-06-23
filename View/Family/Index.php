<?php include_once(__DIR__ . '/../Header.php'); ?>
<?php
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Добавить члена семьи</h1>
    <form action="/<?= $controller_name ?>/" method="post">
        <div class="col-lg-3">
            <div class="form-group">
                <input type="text" class="form-control" id="" placeholder="Описание" name="email">
            </div>
        </div>
        <button type="submit" class="btn btn-default">Отправить</button>
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
    <ul class="list-group">
        <?php
        $output = '';
        $date_current = 0;
        foreach ($waiting_request as $request){

            $date = $request->date;
            if($date != $date_current){
                $output .= ' <span href="#" class="list-group-item active">
                           ' . $request->date . '
                        </span>';
            }

            $output .= '<li class="list-group-item">
                <a href="/' . $controller_name . '/edit/' . $request->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                <a href="/' . $controller_name . '/delete/' . $request->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                <span>' . $request->family_name . " " .  $request->given_name . '</span> <img src="' . $request->picture . '" style="max-width: 20px; float: right">';

            if(! empty($request->description))
            {
                $output .= ' ' . $request->description;
            }

            $output .= '</li>';
            $date_current = $request->date;
        }
        echo $output;
        ?>
    </ul>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '/../Footer.php'); ?>


</body>
</html>