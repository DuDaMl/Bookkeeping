

<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">

    <div class="row">
        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <span class="glyphicon glyphicon-cog"></span>
        </button>
        <br/>
        <div class="collapse" id="collapseExample">
            <div class="well">
                <!-- Форма установки настроек -->
                <form action="/<?= $controller_name ?>/setting/" method="post">
                    <input type="hidden" name="settings" value="1">
                    <h2>Отображать расходы за:</h2>
                    <hr/>
                    <ul>
                        <!-- Поле выбора дня -->
                        <li>
                            <div class='col-lg-1'>
                                <input type="radio" aria-label="1" name="format" value="day" <?php if($settings->format == 'day'){ echo 'checked';} ?>>
                            </div>
                            <div class='col-lg-2'>
                                <span>За день: </span>
                            </div>
                            <div class='input-group date col-lg-3' id='datetimepicker_day'>
                                <input type='text' class="form-control" value="<?php if($settings->format == 'day'){ echo $settings->date_start;} else { echo date('Y-m-d');} ?>" name="day"/>
                                <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                            </div>
                        </li>
                        <hr/>
                        <!-- Поле выбора месяца -->
                        <li>
                            <div class='col-lg-1'>
                                <input type="radio" aria-label="2" name="format" value="month" <?php if($settings->format == 'month'){ echo 'checked';} ?>>
                            </div>
                            <div class='col-lg-2'>
                                <span>За месяц: </span>
                            </div>
                            <div class='input-group date col-lg-3' id='datetimepicker_months'>
                                <input type='text' class="form-control"  value=" <?php if($settings->format == 'month'){ echo substr($settings->date_start, 0, 7);} else { echo date('Y-m');} ?>"  name="month"/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </li>
                        <hr/>
                        <!-- Поле выбора года -->
                        <li>
                            <div class='col-lg-1'>
                                <input type="radio" aria-label="3" name="format" value="year" <?php if($settings->format == 'year'){ echo 'checked';} ?>>
                            </div>
                            <div class='col-lg-2'>
                                <span>За год: </span>
                            </div>
                            <div class='input-group date col-lg-3' id='datetimepicker_years'>
                                <input type='text' class="form-control"  value="<?php if($settings->format == 'year'){ echo substr($settings->date_start, 0, 4);} else { echo date('Y');} ?>" name="year"/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                            </div>
                        </li>
                        <hr/>
                        <?php //todo сделать возможность выбора диапазона выборки данных  ?>
                    </ul>
                    <button type="submit" class="btn btn-default">Показать</button>
                </form>
            </div>
        </div>
    </div>
    <h1>Доходы</h1>
    <form action="/<?= $controller_name ?>/" method="post">
        <input type='hidden' class="form-control" value="1" name="add"/>
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
                    <input type="number" min="0" step="1" class="form-control" id="exampleInputEmail1" placeholder="Сумма" name="amount">
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
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Описание" name="description">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Добавить</button>
        <br/>
        <br/>
    </form>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger">
            <?php echo $error->getMessage(); ?>
        </div>
    <?php endforeach; ?>
    <div class="container">
        <div class="row">
            <div class='col-sm-6'>

            </div>

        </div>
    </div>
    <ul class="list-group">
        <?php
        // проверка существования $income
        if(! empty($incomes))
        {

        $output = '';
        $date_current = 0;
        foreach ($incomes as $income)
        {

            $date = $income->date;
            if($date != $date_current){
                $output .= ' <span href="#" class="list-group-item active">
                           ' . $income->date . '
                        </span>';
            }

            $output .= '<li class="list-group-item">
                <a href="/' . $controller_name .'/edit/' . $income->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                <a href="/' . $controller_name .'/delete/' . $income->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                <span class="badge">' . $income->amount . '</span> <strong>' . $income->name . '</strong>';

            if(! empty($income->description))
            {
                $output .= ' ' . $income->description;
            }

            $output .= '</li>';
            $date_current = $income->date;
        }
        echo $output;
        } else {
            echo '<div class="alert alert-info" role="alert"><p>За данный период нет данных</p></div>';
        }
        ?>
    </ul>

</div>

</div> <!-- /container -->
