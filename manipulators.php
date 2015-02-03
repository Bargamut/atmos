<?php include('top.php');?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=SITE_TITLE?></title>

    <link rel="stylesheet" href="css/commons.css" />
    <link rel="stylesheet" href="css/default.css" />
    <link rel="stylesheet" href="js/morris/morris.css" />

    <link rel="shortcut icon" href="<?=SITE_ICON?>" type="image/x-icon">

    <script src="js/jquery/jquery-2.1.3.min.js"></script>
    <script src="js/jquery/plugins/jquery.blockUI.min.js"></script>
    <script src="js/raphael/raphael-min.js"></script>
    <script src="js/morris/morris.min.js"></script>
    <script src="js/site/common.js"></script>
    <script src="js/site/controllers.js"></script>
    <script src="js/site/graphs.js"></script>
</head>

<body>
    <div class="main ">
        <div class="header">
            <a href="/"><?=SITE_LOGO?></a>
            <div class="annotate">
                ATMOS - система контроля и управления климатом домашних растений, аквариумов, террариумов.
            </div>
            <?php include('menu.php');?>
        </div>
        <div class="content">
            <?php
                $controllersList = $SITE->getControllersList();

                if (count($controllersList) != 0) {
                    if (gettype($controllersList[0]) != 'NULL') {
                        foreach ($controllersList as $v) {
                            echo '<table class="graphsList">' .
                                '<thead>' .
                                    '<tr>' .
                                        '<th>' . $v['ip'] . ': ' . $v['caption'] . '</th>' .
                                    '</tr>' .
                                '</thead>' .
                                '<tbody>' .
                                    '<tr>' .
                                        '<td class="graph" rel="' . $v['id'] . '">' .
                                            '<div id="area' . $v['id'] . '" class="epoch category10" style="height: 200px;"></div>' .
                                        '</td>' .
                                    '</tr>' .
                                    '<tr>' .
                                        '<td colspan="3">' .
                                            '<input class="getDatas" rel="' . $v['id'] . ':' . $v['ip'] . '" type="button" value="Опросить контроллеры" />' .
                                        '</td>' .
                                    '</tr>' .
                                '</tbody>' .
                            '</table>';
                        }
                    } else {
                        echo '<table class="graphsList">' .
                            '<thead>' .
                                '<tr>' .
                                    '<th>' . $controllersList['ip'] . ': ' . $controllersList['caption'] . '</th>' .
                                '</tr>' .
                            '</thead>' .
                            '<tbody>' .
                                '<tr>' .
                                    '<td class="graph" rel="' . $controllersList['id'] . '">' .
                                        '<div id="area' . $controllersList['id'] . '" class="epoch category10" style="height: 200px;"></div>' .
                                    '</td>' .
                                '</tr>' .
                                '<tr>' .
                                    '<td colspan="3">' .
                                        '<input class="getDatas" rel="' . $controllersList['id'] . ':' . $controllersList['ip'] . '" type="button" value="Опросить контроллеры" />' .
                                    '</td>' .
                                '</tr>' .
                            '</tbody>' .
                        '</table>';
                    }
                } else {
                    echo '<table class="graphsList">' .
                        '<tbody>' .
                            '<tr>' .
                                '<td>Контроллеры не установлены</td>' .
                            '</tr>' .
                        '</tbody>' .
                    '</table>';
                }
            ?>
        </div>
        <div class="push"></div>
    </div>
    <div class="footer">
        <hr />
        <?=DEVELOPERS?>
    </div>
</body>
</html>
<?php include('bottom.php');?>