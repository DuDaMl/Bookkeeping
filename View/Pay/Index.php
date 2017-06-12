<?php include_once(__DIR__ . '\..\Header.php'); ?>


<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="row">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <span class="glyphicon glyphicon-cog"></span>
        </button>
        <br/>
        <div class="collapse" id="collapseExample">
            <div class="well">




    <form action="/<?= $controller_name ?>/settings/" method="post">
    <h2>Отображать расходы за:</h2>
        <hr/>
    <ul>
        <li>
            <div class='col-lg-1'>
               <input type="radio" aria-label="1" name="format">
            </div>
            <div class='col-lg-2'>
                <span>За день: </span>
            </div>
            <div class='input-group date col-lg-3' id='datetimepicker_day'>
                <input type='text' class="form-control" value="<?=date('Y-m-d');?>" name="date"/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
            </div>
        </li>
        <hr/>
        <li>
            <div class='col-lg-1'>
                <input type="radio" aria-label="2" name="format">
            </div>
            <div class='col-lg-2'>
                <span>За месяц: </span>
            </div>
            <div class='input-group date col-lg-3' id='datetimepicker_months'>
                <input type='text' class="form-control"  value="<?=date('Y-m');?>"  name="date"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </li>
        <hr/>
        <li>
            <div class='col-lg-1'>
                <input type="radio" aria-label="3" name="format">
            </div>
            <div class='col-lg-2'>
                <span>За год: </span>
            </div>
            <div class='input-group date col-lg-3' id='datetimepicker_years'>
                <input type='text' class="form-control"  value="<?=date('Y');?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar">
                    </span>
                </span>
            </div>
        </li>
        <?php //todo сделать возможность выбора диапазона выборки данных  ?>

    </ul>
        <button type="submit" class="btn btn-default">Показать</button>
    </form>


            </div>
        </div>
    </div>





    <h1>Расходы</h1>
    <form action="/" method="post">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker1'>
                        <input type='text' class="form-control" value="<?=date('Y-m-d');?>" name="date"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <input type="number" min="0" step="1" class="form-control" id="" placeholder="Сумма" name="amount">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="dropdown">
                    <select class="selectpicker" data-live-search="true" name="category_id" data-width="100%">

                        <?php
                            $output = '';
                            foreach ($categories as $category){
                                $output .= ' <option data-tokens="' . $category->name . '" value="' . $category->id . '">' . $category->name . '</option>';
                            }
                            echo $output;
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="" placeholder="Описание" name="description">
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
    <ul class="list-group">
        <?php
        $output = '';
        $date_current = 0;
        foreach ($pays as $pay){

            $date = $pay->date;
            if($date != $date_current){
                $output .= ' <span href="#" class="list-group-item active">
                           ' . $pay->date . '
                        </span>';
            }

            $output .= '<li class="list-group-item">
                <a href="/' . $controller_name . '/edit/' . $pay->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                <a href="/' . $controller_name . '/delete/' . $pay->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                <span class="badge">' . $pay->amount . '</span> <strong>' . $pay->name . '</strong>';

            if(! empty($pay->description))
            {
                $output .= ' ' . $pay->description;
            }

            $output .= '</li>';
            $date_current = $pay->date;
        }
        echo $output;
        ?>
    </ul>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '\..\Footer.php'); ?>


</body>
</html>