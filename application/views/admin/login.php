<?php defined('SYSPATH') or die('No direct script access.');?>

<style type="text/css">
    .form-signin
    {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .form-signin-heading, .form-signin .checkbox
    {
        margin-bottom: 10px;
    }
    .form-signin .checkbox
    {
        font-weight: normal;
    }
    .form-signin .form-control
    {
        position: relative;
        font-size: 16px;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .form-signin .form-control:focus
    {
        z-index: 2;
    }
    .form-signin input[type="text"]
    {
        margin-bottom: -1px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .form-signin input[type="password"]
    {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .account-wall
    {
        margin-top: 20px;
        padding: 40px 0px 20px 0px;
        background-color: #f7f7f7;
        -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    }
    .login-title
    {
        color: #555;
        font-size: 18px;
        font-weight: 400;
        display: block;
    }
    .profile-img
    {
        width: 96px;
        height: 96px;
        margin: 0 auto 10px;
        display: block;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
    }
    .need-help
    {
        margin-top: 10px;
    }
    .new-account
    {
        display: block;
        margin-top: 10px;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <h1 class="text-center login-title">Insales Admin</h1>
            <div class="account-wall">
                <img class="profile-img" src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=120"
                     alt="">
                <form class="form-signin" action="admin/auth/login" method="post" role="form">
                    <input type="text" name="login" class="form-control" placeholder="login" required autofocus>
                    <input type="password" name="password"  class="form-control" placeholder="Password" required>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                    <!--
                    <label class="checkbox pull-left">
                        <input type="checkbox" value="remember-me">
                        Remember me
                    </label>
                    <a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span>
                    -->
                </form>
            </div>
            <!--<a href="#" class="text-center new-account">Create an account </a>-->
        </div>
    </div>
</div>


<?php /* ?>
<form class="form-signin" action="admin/auth/login" method="post" role="form">
    <div class="col-sm-4" style="margin: 0 auto;">
        <h2 class="form-signin-heading">Вход</h2>
        <input type="text" name="login" class="form-control" placeholder="Логин" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="Пароль" required>
        <!--
        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Remember me
        </label>
        -->
        <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>
    </div>
</form>
<?php */ ?>
    <?php
    /*
    <div class="row">
        <div class="span5 offset3 well">
            <form class="form-horizontal" action="" method="post">
                <legend>Authorization</legend>

                <?php if (isset($errors['common'])) : ?>
                    <div class="alert alert-error">
                        <a href="#" class="close" data-dismiss="alert">×</a>
                        <?php echo $errors['common'] ?>
                    </div>
                <?php endif; ?>

                <div class="control-group pull-right clearfix">
                    <?php if (isset($errors['login'])) : ?>
                        <div class="alert alert-error">
                            <a href="#" class="close" data-dismiss="alert">×</a>
                            <?php echo $errors['login'] ?>
                        </div>
                    <?php endif; ?>
                    <label class="control-label" for="alogin">Email or Username</label>
                    <div class="controls"><input type="text" name="login" id="alogin" placeholder="Type your email or username"></div>
                </div>

                <div class="control-group pull-right">
                    <?php if (isset($errors['password'])) : ?>
                        <div class="alert alert-error">
                            <a href="#" class="close" data-dismiss="alert">×</a>
                            <?php echo $errors['password'] ?>
                        </div>
                    <?php endif; ?>
                    <label class="control-label" for="apassword">Password</label>
                    <div class="controls"><input type="password" name="password" id="apassword"  placeholder="Type your password"></div>
                </div>

                <div class="control-group row-fluid">
                    <label class="checkbox pull-right">
                        <input name="remember" type="checkbox" value="1"> Remember me
                    </label>
                </div>

                <button class="btn btn-primary  pull-right" type="submit">Login</button>
            </form>
        </div>
    </div>
<?php */ ?>
<style type="text/css">
    legend{ margin: 0; }
    .well{ margin-top: 100px; }
</style>