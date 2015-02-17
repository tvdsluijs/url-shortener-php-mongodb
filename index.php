<?php
define('_SHORT_INCLUDE_ONLY', 1);
setlocale(LC_TIME, 'NL_nl');
date_default_timezone_set('Europe/Amsterdam');

require_once('config.php');

if(SHOW_ERRORS){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once('functions.php');
require_once('mongodb.php');

if(isset($LIMIT_TO_IP) && !allowedIP($LIMIT_TO_IP))
{
    header("HTTP/1.0 404 Not Found");
    die();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Europe's first free URL Shortener">
    <meta name="author" content="TS Intermedia">

    <title><?php echo SITE_TITLE;?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo BASE_HREF;?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo BASE_HREF;?>css/stylish-portfolio.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/prism.css" data-noprefix/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .header {
            display: table;
            position: relative;
            width: 100%;
            height: 100%;
            background: url(<?php echo BACKGROUND_IMAGE;?>) no-repeat center center scroll;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
            -o-background-size: cover;
        }
    </style>
</head>

<body>
<?php

if(NULL !== GOOGLE_ANALYTICS_ID){
    ?>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', '<?php echo GOOGLE_ANALYTICS_ID;?>', 'auto');
        ga('send', 'pageview');

    </script>
<?php
}

$MG_DB = new M_DB(MONGO_COLLECTION_MAIN);
$nr = $MG_DB->getNrRecords();
if (FAKE_URL_FRONTPAGE_COUNT) {
    $start = mktime(date("H"), 0, 0);
    $between = (int)substr($start + (60 * 60), 3);
    $nr = $between + $nr;
}
$nr = number_format($nr, 0, ',', '.');
?>
<!-- Navigation -->


<!-- Header -->
<header id="top" class="header">
    <div class="text-vertical-center">
        <h1><?php echo SITE_NAME;?></h1>

        <h3><?php echo SITE_SUBTITLE;?></h3>
        <br>

        <form method="post" action="shorten.php" id="shortener" class="form-inline">
            <div class="form-group">
                <label for="longurl">URL to shorten</label> <br/>
                <input type="text" class="form-control" style="min-width:350px" name="longurl" id="longurl"
                       placeholder="http://Your Url" autocomplete="off">

                <?php
                if ((isset($SHOW_OWN_SHORT_FIELD) && $SHOW_OWN_SHORT_FIELD == true) || (isset($SHOW_OWN_SHORT_FIELDBY_IP) && allowedIP($SHOW_OWN_SHORT_FIELDBY_IP))) {
                    ?>
                    <br/><label for="longurl"><?php echo BASE_HREF?></label>
                    <input type="text" class="form-control" style="min-width:150px" name="shorturl" id="shorturl"
                           placeholder="short part" autocomplete="off">
                <?php
                } ?>

                <input type="submit" value="Shorten" class="btn btn-success">

            </div>
        </form>
        <?php if(SHOW_URL_FRONTPAGE_COUNT) { ?>
            <h4><?php echo $nr; ?> url's already shortened!</h4>
        <?php }
        ?>
    </div>
</header>

<!-- jQuery -->
<script src="<?php echo BASE_HREF;?>js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="<?php echo BASE_HREF;?>js/bootstrap.min.js"></script>

<script src="<?php echo BASE_HREF;?>js/prism.js"></script>
<!-- Custom Theme JavaScript -->
<script>
    // Closes the sidebar menu
    $("#menu-close").click(function (e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });

    // Opens the sidebar menu
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });

    $(function () {
        $('#shortener').submit(function () {
            $.ajax({
                <?php
                if(SHOW_OWN_SHORT_FIELD){?>
                    data: {longurl: $('#longurl').val(), shorturl: $('#shorturl').val()},
                <?php
                 }else{
                 ?>
                    data: {longurl: $('#longurl').val()},
                <?php }
                ?>
                url: 'shorten.php',
                complete: function (XMLHttpRequest, textStatus) {
                    $('#longurl').val(XMLHttpRequest.responseText);
                    $('#shorturl').val('');

                }
            });
            return false;
        });
    });

    // Scrolls to the selected menu item on the page
    $(function () {
        $('a[href*=#]:not([href=#])').click(function () {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return false;
                }
            }
        });
    });
</script>
</body>
</html>