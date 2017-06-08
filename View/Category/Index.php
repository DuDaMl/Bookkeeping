<?php include_once(__DIR__ . '\..\Header.php'); ?>


<!-- Main component for a primary marketing message or call to action -->
<div class="jumbotron">
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


        <button type="submit" class="btn btn-default">Submit</button>
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
        foreach ($pays as $pay){

            $date = $pay->date;
            if($date != $date_current){
                $output .= ' <span href="#" class="list-group-item active">
                           ' . $pay->date . '
                        </span>';
            }

            $output .= '<li class="list-group-item">
                <a href="/edit/' . $pay->id . '"><span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                <a href="/delete/' . $pay->id . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                <span class="badge">' . $pay->amount . '</span> <strong>' . $pay->name . "</strong> Описание:'" . $pay->description .'\'
            
            </li>';
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