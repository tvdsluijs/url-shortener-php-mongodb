<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 10/02/15
 * Time: 16:12
 *
 * Backend theme by Carlos Alvarez
 * Site: http://Alvarez.is
 *
 */
define('_SHORT_INCLUDE_ONLY', 1);
setlocale(LC_TIME, 'NL_nl');
date_default_timezone_set('Europe/Amsterdam');

require_once('../config.php');

if (SHOW_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once('../functions.php');
require_once('../mongodb.php');

if (isset($LIMIT_BACKEND_TO_IP) && !in_array($_SERVER['REMOTE_ADDR'], $LIMIT_BACKEND_TO_IP)) {
    header("HTTP/1.0 404 Not Found");
    die();
}

// this is where all the fancy magic stuff happens!
require_once('backend_functions.php');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo SITE_NAME; ?> Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/font-style.css" rel="stylesheet">
    <link href="assets/css/flexslider.css" rel="stylesheet">
    <link href="assets/css/table.css" rel="stylesheet">
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>

    <style type="text/css">
        body {
            padding-top: 60px;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

    <!-- Google Fonts call. Font Used Open Sans & Raleway -->
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,300" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

    <script type="text/javascript">
        $(document).ready(function () {


        });

        $(window).load(function () {

            $('.flexslider').flexslider({
                animation: "slide",
                slideshow: true,
                start: function (slider) {
                    $('body').removeClass('loading');
                }
            });
        });

    </script>

</head>
<body>

<!-- NAVIGATION MENU -->

<div class="navbar-nav navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.html"><img src="assets/img/logo30.png" alt=""> <?php echo SITE_NAME; ?>
                Dashboard</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php"><i class="icon-home icon-white"></i> Home</a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>

<div class="container">

    <!-- FIRST ROW OF BLOCKS -->
    <div class="row">

        <div class="col-sm-3 col-lg-3">

            <!-- Number or Rows -->

            <div class="half-unit">
                <dtitle>Database Size</dtitle>
                <hr>
                <div class="cont">
                    <p>
                        <bold><?php echo $dbsize; ?></bold>
                    </p>
                </div>
            </div>

            <div class="half-unit">
                <dtitle>Nr of Short urls</dtitle>
                <hr>
                <div class="cont">
                    <p>
                        <bold><?php echo $nr_of_rows; ?></bold>
                    </p>
                    <p><?php echo $rows_today ?> created Today</p>
                </div>
            </div>

        </div>

        <!-- LAST MONTHs added urls -->
        <div class="col-sm-3 col-lg-3">
            <div class="dash-unit">
                <dtitle>Last 3 Months Urls</dtitle>
                <hr>
                <div class="cont">
                    <p>
                        <bold><?php echo $thismonth_urls; ?></bold>
                        |
                        <ok><?php echo $this_month_name; ?></ok>
                    </p>
                    <br>

                    <p>
                        <bold><?php echo $lastmonth_urls; ?></bold>
                        | <?php echo $last_month_name; ?></p>
                    <br>

                    <p>
                        <bold><?php echo $prelastmonth_urls; ?></bold>
                        | <?php echo $prethis_month_name; ?></p>
                    <br>

                    <p>
                        <?php
                        if ($difference_day_urls > 0) {
                            echo '<img src="assets/img/up-small.png" alt=""> ';
                        } else {
                            echo '<img src="assets/img/down-small.png" alt=""> ';
                        }
                        echo $difference_day_urls; ?> Today Last Month</p>

                </div>

            </div>
        </div>

        <!-- LAST MONTHs Clicks -->
        <div class="col-sm-3 col-lg-3">
            <div class="dash-unit">
                <dtitle>Last 3 Month Clicks</dtitle>
                <hr>
                <div class="cont">
                    <p>
                        <bold><?php echo $thismonth_clicks; ?></bold>
                        |
                        <ok><?php echo $this_month_name; ?></ok>
                    </p>
                    <br>

                    <p>
                        <bold><?php echo $lastmonth_clicks; ?></bold>
                        | <?php echo $last_month_name; ?></p>
                    <br>

                    <p>
                        <bold><?php echo $prelastmonth_clicks; ?></bold>
                        | <?php echo $prethis_month_name; ?></p>
                    <br>

                    <p>
                        <?php
                        if ($difference_day_clicks > 0) {
                            echo '<img src="assets/img/up-small.png" alt=""> ';
                        } else {
                            echo '<img src="assets/img/down-small.png" alt=""> ';
                        }
                        echo $difference_day_clicks; ?> Today Last Month</p>

                </div>

            </div>
        </div>

        <div class="col-sm-3 col-lg-3">

            <!-- LOCAL TIME BLOCK -->
            <div class="half-unit">
                <dtitle>Local Time</dtitle>
                <hr>
                <div class="clockcenter">
                    <digiclock>12:45:25</digiclock>
                </div>
            </div>

            <!-- SERVER UPTIME -->
            <div class="half-unit">
                <dtitle>Server Uptime</dtitle>
                <hr>
                <div class="cont">
                    <p><img src="assets/img/up.png" alt="">
                        <bold>Up</bold>
                        | <?php echo $server_uptime; ?></p>
                </div>
            </div>

        </div>
    </div>
    <!-- /row -->


    <!-- SECOND ROW OF BLOCKS -->
    <div class="row">

        <!-- GRAPH CHART - lineandbars.js file -->
        <div class="col-sm-3 col-lg-3">
            <div class="dash-unit">
                <dtitle>Urls & Clicks last 30 days</dtitle>
                <hr>
                <div class="section-graph" >
                    <div id="importantchart" style="min-width: 250px; height: 240px; margin: 0 auto"></div>
                </div>
            </div>
        </div>

        <!-- DONUT CHART BLOCK -->
        <div class="col-sm-3 col-lg-3">
            <div class="dash-unit">
                <dtitle>Avg dayclicks</dtitle>
                <hr>
                <div id="load"></div>
                <h2>total <?php echo $today_clicks; ?> today</h2>
            </div>
        </div>

        <!-- 30 DAYS STATS - CAROUSEL FLEXSLIDER -->
        <div class="col-sm-3 col-lg-3">
            <div class="dash-unit">
                <dtitle>Urls & Clicks last 7 days</dtitle>
                <hr/>
                <div class="flexslider">
                    <ul class="slides">
                        <li><div id="containerClicks" style="min-width: 250px; height: 240px; margin: 0 auto"></div></li>
                        <li><div id="containerUrls" style="min-width: 250px; height: 240px; margin: 0 auto"></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="dash-unit table" style="overflow:hidden;height:100%;padding-left:10px;padding-right:10px">
                <dtitle>Urls & Clicks last 30 days</dtitle>
                <hr>
                <h4><strong>Short Url Clicks</strong></h4>
                <table id="tdt" class="display" cellspacing="0" width="95%">
                    <thead>
                    <tr>
                        <th>Short Url</th>
                        <th>Long Url</th>
                        <th>Nr of Clicks</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th>Short Url</th>
                        <th>Long Url</th>
                        <th>Nr of Clicks</th>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

</div>
<!-- /container -->
<div id="footerwrap">
    <footer class="clearfix"></footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <p><?php echo SITE_NAME; ?> - Copyrights <?php echo date('Y'); ?></p>
            </div>

        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /footerwrap -->


<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="assets/js/bootstrap.js"></script>

<!-- NOTY JAVASCRIPT -->
<script type="text/javascript" src="assets/js/noty/jquery.noty.js"></script>
<script type="text/javascript" src="assets/js/noty/layouts/top.js"></script>
<script type="text/javascript" src="assets/js/noty/layouts/topLeft.js"></script>
<script type="text/javascript" src="assets/js/noty/layouts/topRight.js"></script>
<script type="text/javascript" src="assets/js/noty/layouts/topCenter.js"></script>

<script type="text/javascript" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('#tdt').dataTable({
            "ajax": 'getUrlList.php',
            "columns": [
                {"data": "short_url"},
                {"data": "long_url"},
                {"data": "count"}
            ]
        });

        info = new Highcharts.Chart({
            chart: {
                renderTo: 'load',
                margin: [0, 0, 0, 0],
                backgroundColor: null,
                plotBackgroundColor: 'none'
            },

            title: {
                text: null
            },

            tooltip: {
                formatter: function () {
                    return this.point.name + ': ' + this.y + ' %';

                }
            },
            credits: {
                enabled: false
            },
            series: [
                {
                    borderWidth: 2,
                    borderColor: '#F1F3EB',
                    shadow: false,
                    type: 'pie',
                    name: 'Income',
                    innerSize: '65%',
                    data: [
                        {name: 'visit percentage', y: <?php echo $avg_today;?>.0, color: '#b2c831'},
                        {name: 'rest', y: <?php echo $avg_not_today;?>.0, color: '#3d3d3d'}
                    ],
                    dataLabels: {
                        enabled: false,
                        color: '#000000',
                        connectorColor: '#000000'
                    }
                }]
        });

        function generateNumber(min, max) {
            min = typeof min !== 'undefined' ? min : 1;
            max = typeof max !== 'undefined' ? max : 100;

            return Math.floor((Math.random() * max) + min);
        }

        var chart,
            categories = [<?php echo "'" . implode("','",$seriesdates). "'";?>],
            serie1 = [<?php echo implode(',',$series1);?>],
            serie2 = [<?php echo implode(',',$series2);?>],
            $aapls;


        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'importantchart',
                type: 'column',
                backgroundColor: 'transparent'
            },
            title: {
                text: null
            },
            xAxis: {
                lineWidth: 0,
                tickWidth: 0,
                labels: {
                    enabled: false
                },
                categories: categories
            },
            yAxis: {
                labels: {
                    enabled: false
                },
                gridLineWidth: 0,
                title: {
                    text: null
                }
            },
            series: [{
                name: 'Urls',
                data: serie1
            }, {
                name: 'Clicks',
                color: '#fff',
                type: 'line',
                data: serie2
            }],
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                column: {
                    borderWidth: 0,
                    color: '#b2c831',
                    shadow: false
                },
                line: {
                    marker: {
                        enabled: false
                    },
                    lineWidth: 3
                }
            },
            tooltip: {
              //  pointFormat: '<b>{point.x}</b> Urls <br/>  <b>{point.y:,.0f}</b> clicks'
            }
        });

        setInterval(function () {
            chart.series[0].addPoint(generateNumber(), true, true);
            chart.series[1].addPoint(generateNumber(50, 150), true, true);
        }, 1000);


        setInterval(function () {
            $('.info-aapl span').each(function (index, elem) {
                $(elem).animate({
                    height: generateNumber(1, 40)
                });
            });

        }, 3000);


    $('#containerClicks').highcharts({
        colors: ["#c6dc31","#8085e9","#68771e","#7798BF","#fff","#b2c831","#e7fd35","#55BF3B","#cae031","#7798BF","#fff"],
        chart: {
            type: 'column',
            backgroundColor: 'transparent'
        },
        title: {
            text: 'Clicks',
            style: {
                color: '#FFF'
            }
        },
        subtitle: {
            text: null
        },
        credits: {
            enabled: false
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif',
                    color: '#FFF'
                }
            }
        },
        yAxis: {
            min: 0,
            labels: {
                style: {
                    color: '#FFF'
                }
            },
            title: {
                text: null
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Clicks created <b>{point.y:.1f}</b>'
        },
        series: [{
            name: 'Clicks',
            data: [<?php echo $Clicks_last_seven_days;?>],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                x: 4,
                y: 10,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif',
                    textShadow: '0 0 3px black'

                }
            }
        }]
    });

        $('#containerUrls').highcharts({
            colors: ["#c6dc31","#8085e9","#68771e","#7798BF","#fff","#b2c831","#e7fd35","#55BF3B","#cae031","#7798BF","#fff"],
            chart: {
                type: 'column',
                backgroundColor: 'transparent'
            },
            title: {
                text: 'Urls',
                style: {
                    color: '#FFF'
                }
            },
            subtitle: {
                text: null,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif',
                    color: '#FFF'
                }
            },
            credits: {
                enabled: false
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif',
                        color: '#FFF'
                    }
                }
            },
            yAxis: {
                min: 0,
                labels: {
                    style: {
                        color: '#FFF'
                    }
                },
                title: {
                    text: null
                }
            },plotOptions: {
                    boxplot: {
                        fillColor: '#b2c831'
                    },
                    candlestick: {
                        lineColor: 'pink'
                    }
                },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Urls created <b>{point.y:.1f}</b>'
            },
            series: [{
                name: 'Urls',
                data: [<?php echo $Urls_last_seven_days;?>],
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    x: 4,
                    y: 10,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px black'
                    }
                }
            }]
        });

    });

</script>

<!-- You can add more layouts if you want -->
<script type="text/javascript" src="assets/js/noty/themes/default.js"></script>
<!-- <script type="text/javascript" src="assets/js/dash-noty.js"></script> This is a Noty bubble when you init the theme-->
<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
<script src="assets/js/jquery.flexslider.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/js/admin.js"></script>

</body>
</html>