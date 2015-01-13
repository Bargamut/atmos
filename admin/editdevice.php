<?php include('top.php');?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../css/default.css" />
        <link rel="shortcut icon" href="<?=SITE_ICON?>" type="image/x-icon">
        <title><?=SITE_TITLE?></title>
        <script type="text/javascript" src="../js/jquery/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="../js/admin/common.js"></script>
    </head>

    <body>
    <div class="main ">
        <div class="header">
            <a href="/"><?=SITE_LOGO?></a>
            <div class="annotate">
                Разработка, сборка и установка систем контроля доступа, видеонаблюдения и пожароохранных систем.
            </div>
            <?php include('menu.php');?>
        </div>
        <div class="content">
            <form id="devices" action="/admin/editdevice.php?type=<?=$_GET['type'];?>&device=<?=$_GET['device'];?>" method="post" enctype="multipart/form-data">
                <?=$SITE->getProductsOpts($_GET['device'], $_GET['type']);?>
                <input class="btnOk" type="submit" value="Ок" />
            </form>
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