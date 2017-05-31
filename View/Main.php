<?php include_once(__DIR__ . '\Header.php'); ?>
<body>
<div class="container">

<!-- Static navbar -->
<nav class="navbar navbar-default">
    <div class="container-fluid">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar">1</span>
                <span class="icon-bar">2</span>
                <span class="icon-bar">3</span>
            </button>
            <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="/">Расходы</a></li>
                <li><a href="#">Доходы</a></li>
                <li><a href="/Category">Категории</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="./">Default <span class="sr-only">(current)</span></a></li>
                <li><a href="../navbar-static-top/">Static top</a></li>
                <li><a href="../navbar-fixed-top/">Fixed top</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>

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


            $output .= '<li class="list-group-item"> <span class="badge">' . $pay->amount . '</span> <strong>' . $pay->name . "</strong> Описанеи:'" . $pay->description .'\'</li>';
            $date_current = $pay->date;
        }
        echo $output;
        ?>
    </ul>

</div>

</div> <!-- /container -->

<?php include_once(__DIR__ . '\Footer.php'); ?>


</body>
</html>