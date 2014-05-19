<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @var array       $config      Global blog configuration
 * @var string      $title       Page Title
 * @var string      $author      Material author
 * @var string      $description Meta description tag content
 * @var string      $keywords    Meta keywords tag content
 * @var string|View $content     Page content
 *
 * @author     Novichkov Sergey(Radik) <novichkovsergey@yandex.ru>
 * @copyright  Copyrights (c) 2012 Novichkov Sergey
 */
?><!DOCTYPE html>
<html>
<head>
    <title><?php /* echo isset($title) ? $title :  Arr::path($config, 'blog.name')  */ ?></title>

    <!-- Base URL -->
    <base href="<?php echo URL::base(true, false) ?>">

    <!-- System -->
    <meta name="author" content="<?php /* echo isset($author) ? $author : Arr::path($config, 'blog.author') */?>" />
    <meta name="description" content="<?php /* echo isset($description) ? $description : Arr::path($config, 'blog.description') */?>" />
    <meta name="keywords" content="<?php /* echo isset($keywords) ? $keywords : Arr::path($config, 'blog.keywords') */?>" />

    <!-- Twitter Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="assets/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="all" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>
<!-- Template Content  -->
<?php echo isset($content) ? $content : '' ?>

<!-- JS Code -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
</body>
</html>