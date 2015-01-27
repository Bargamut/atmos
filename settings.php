<?php include('top.php');?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/commons.css" />
    <link rel="stylesheet" href="css/default.css" />
    <link rel="shortcut icon" href="<?=SITE_ICON?>" type="image/x-icon" />
    <title><?=SITE_TITLE?></title>
    <script src="js/jquery/jquery-1.7.2.min.js"></script>
    <script src="js/site/common.js"></script>
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
                        <th>№</th>
                        <th>IP</th>
                        <th>Наименование</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $controllersList = $SITE->getControllersList();

                        foreach ($controllersList as $v) {
                            echo '<tr>';

                            if (gettype($v) == 'array') {
                                foreach ($v as $v2) {
                                    echo '<td>' . $v2 . '</td>';
                                }
                            } else {
                                echo '<td>' . $v . '</td>';
                            }

                            echo '</tr>';
                        }
                    ?>
                    <tr>
                        <td colspan="4">
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