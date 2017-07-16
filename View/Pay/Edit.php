<?php include_once(__DIR__ . '/../Header.php'); ?>

<?php
?>
<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <h1>Расходы</h1>
    <h2>Редактирование расходов</h2>

    <?php
    // Скрыть форму для редактирования
    if(! isset($data['error']))
    {
    ?>
        <form action="/<?= $controller_name ?>/edit/" method="post">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <div class='input-group date' id='datetimepicker1'>
                            <input type='text' class="form-control" value="<?= $pay->date ?>" name="date"/>
                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="hidden" min="0" step="1" name="id" value="<?= $pay->id ?>">
                        <input type="number" min="0" step="1" class="form-control" id="exampleInputEmail1"
                               placeholder="Сумма" name="amount" value="<?= $pay->amount ?>">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="dropdown">
                        <select class="selectpicker" data-live-search="true" name="category_id" data-width="100%">

                            <?php
                            $output = '';
                            foreach ($categories as $category) {
                                if ($category->id == $pay->category_id) {
                                    $output .= ' <option data-tokens="' . $category->name . '" value="' . $category->id . '" selected> ' . $category->name . '</option>';
                                } else {
                                    $output .= ' <option data-tokens="' . $category->name . '" value="' . $category->id . '">' . $category->name . '</option>';
                                }

                            }
                            echo $output;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Описание"
                               name="description" value="<?= $pay->description ?>">
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-default">Submit</button>
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