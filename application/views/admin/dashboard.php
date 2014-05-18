<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @var array $config Global blog configuration
 * @var Model_User $user Global Kohana user object
 *
 * @author     Novichkov Sergey(Radik) <novichkovsergey@yandex.ru>
 * @copyright  Copyrights (c) 2012 Novichkov Sergey
 */
?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Authorized as: <?php //echo $user->username ?></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo URL::site('/admin/auth/logout') ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container jumbotron">
    <h1>Congratulations</h1>
</div>

<style type="text/css">
    .jumbotron{ padding-top: 40px; }
</style>