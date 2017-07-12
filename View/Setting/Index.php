<?php
    // [date_start] => 2017-01-01 [date_end] => 2017-12-31 [format] => year
?>
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
                            <input type="radio" aria-label="1" name="format" value="day" <?php if($format == 'day'){ echo 'checked';} ?>>
                        </div>
                        <div class='col-lg-2'>
                            <span>За день: </span>
                        </div>
                        <div class='input-group date col-lg-3' id='datetimepicker_day'>
                            <input type='text' class="form-control" value="<?php if($format == 'day'){ echo $date_start;} else { echo date('Y-m-d');} ?>" name="day"/>
                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                        </div>
                    </li>
                    <hr/>
                    <!-- Поле выбора месяца -->
                    <li>
                        <div class='col-lg-1'>
                            <input type="radio" aria-label="2" name="format" value="month" <?php if($format == 'month'){ echo 'checked';} ?>>
                        </div>
                        <div class='col-lg-2'>
                            <span>За месяц: </span>
                        </div>
                        <div class='input-group date col-lg-3' id='datetimepicker_months'>
                            <input type='text' class="form-control"  value=" <?php if($format == 'month'){ echo substr($date_start, 0, 7);} else { echo date('Y-m');} ?>"  name="month"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </li>
                    <hr/>
                    <!-- Поле выбора года -->
                    <li>
                        <div class='col-lg-1'>
                            <input type="radio" aria-label="3" name="format" value="year" <?php if($format == 'year'){ echo 'checked';} ?>>
                        </div>
                        <div class='col-lg-2'>
                            <span>За год: </span>
                        </div>
                        <div class='input-group date col-lg-3' id='datetimepicker_years'>
                            <input type='text' class="form-control"  value="<?php if($format == 'year'){ echo substr($date_start, 0, 4);} else { echo date('Y');} ?>" name="year"/>
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