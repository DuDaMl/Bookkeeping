<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->

    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-datepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <link href="/assets/css/style.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php

    if(isset($js[0]))
    {
        foreach($js as $index => $script)
        {
            echo  '<script src="' . $script . '"></script>';
        }

    }
    ?>



</head>

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
                <a class="navbar-brand" href="/">Bookkeeping</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li <?php if($current_page == 'Pay') { echo 'class="active"';}?>><a href="/Pay">Расходы</a></li>
                    <li <?php if($current_page == 'Income') { echo 'class="active"';}?>><a href="/Income">Доходы</a></li>
                    <li <?php if($current_page == 'Category') { echo 'class="active"';}?>><a href="/Category">Категории</a></li>
                    <li <?php if($current_page == 'Family') { echo 'class="active"';}?>><a href="/Family">Семья</a></li>
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

                    <?php

                    if ($user == false)
                    {
                    ?>
                        <li><a href="/index/login">Аутентификация через Google</a></li>
                    <?php
                    } else {
                    ?>
                        <li><a href="/index/logout">Logout</a></li>
                    <?php
                    }
                    ?>

                    <!--<li><a href="https://accounts.google.com/o/oauth2/auth?redirect_uri=http://localhost/bookkeeping.com/index&amp;response_type=code&amp;client_id=344999880236-avemoshdfile1s78mqugngj1suf0urrq.apps.googleusercontent.com&amp;scope=https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile">Аутентификация через Google</a></li>-->

                    <li><a href="../navbar-fixed-top/">
                            <?php

                            if(isset($user) && ! empty($user))
                            {
                                echo "<img src='" . $user->picture . "' height=18px> ";
                                //echo $user->given_name . " " .$user->family_name;
                                // todo exit();
                            }



                            ?>
                        </a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>

    <?php // echo $current_page;?>