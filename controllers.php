<?php include('top.php');?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=SITE_TITLE?></title>

    <link rel="stylesheet" href="css/commons.css" />
    <link rel="stylesheet" href="css/default.css" />
    <link rel="shortcut icon" href="<?=SITE_ICON?>" type="image/x-icon" />

    <script src="js/jquery/jquery-2.1.3.min.js"></script>
    <script src="js/jquery/plugins/jquery.blockUI.min.js"></script>
    <script src="js/site/common.js"></script>
    <script src="js/site/controllers.js"></script>
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
            <table class="controllersList">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Наименование</th>
                        <th>Описание</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $controllersList = $SITE->getControllersList();

                        if (count($controllersList) != 0) {
                            if (gettype($controllersList[0]) != 'NULL') {
                                foreach ($controllersList as $v) {
                                    echo '<tr>' .
                                            '<td>' . $v['ip'] . '</td>' .
                                            '<td>' . $v['caption'] . '</td>' .
                                            '<td>' . $v['description'] . '</td>' .
                                            '<td class="set"><img src="/img/default/settings.png" /></td>' .
                                            '<td class="delController"><img rel="' . $v['id'] . '" src="/img/default/delete.png" /></td>' .
                                        '</tr>';
                                }
                            } else {
                                echo '<tr>' .
                                        '<td>' . $controllersList['ip'] . '</td>' .
                                        '<td>' . $controllersList['caption'] . '</td>' .
                                        '<td>' . $controllersList['description'] . '</td>' .
                                        '<td class="set"><img src="/img/default/settings.png" /></td>' .
                                        '<td class="delController"><img rel="' . $controllersList['id'] . '" src="/img/default/delete.png" /></td>' .
                                    '</tr>';
                            }
                        } else {
                            echo '<td colspan="5">Контроллеры не установлены</td>';
                        }
                    ?>
                    <tr>
                        <td colspan="6">
                            <input id="findController" type="button" value="Найти контроллер" />
                        </td>
                    </tr>
                </tbody>
            </table>
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