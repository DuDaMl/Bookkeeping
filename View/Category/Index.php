<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Категории</h1>

    <form action="/<?= $controller_name ?>/" method="post">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <input type="text" min="0" step="1" class="form-control" id="exampleInputEmail1" placeholder="Название категории" name="name">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="radio-inline"><input type="radio" name="type" value="Pay" checked>Расходы</label>
                    <label class="radio-inline"><input type="radio" name="type" value="Income">Доходы</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Добавить</button>
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
    <div class="container">
        <div class="row">
            <div class='col-sm-6'>
                <h2>Расходы: </h2>

                <ul class="list-group">
                    <?php
                    $output = '';
                    $date_current = 0;
                    foreach ($categories_pays as $category){

                        $output .= '<li class="list-group-item">
                                    <a href="/'. $controller_name .'/edit/' . $category->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                    <a href="/'. $controller_name .'/delete/' . $category->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                    <strong>' . $category->name . '</strong> </li>';
                    }
                    echo $output;
                    ?>
                </ul>
            </div>
            <div class='col-sm-6'>
                <h2>Доходы: </h2>

                <ul class="list-group">
                    <?php
                    $output = '';
                    $date_current = 0;
                    foreach ($categories_incomes as $category){

                        $output .= '<li class="list-group-item">
                                    <a href="/'. $controller_name .'/edit/' . $category->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                    <a href="/'. $controller_name .'/delete/' . $category->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                    <strong>' . $category->name . '</strong> </li>';
                    }
                    echo $output;
                    ?>
                </ul>
            </div>

        </div>
    </div>


</div>

</div> <!-- /container -->
