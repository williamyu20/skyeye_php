<?php
/**
 * Created by PhpStorm.
 * User: Wilson
 * Date: 2/26/2019
 * Time: 16:04
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php include "header.php"; ?>
    <title>Finished Mission - SkyEye</title>
</head>
<body>
<?php
include "checkIsLogined.php";
include "Missionpagemenu.php";
include "missionListGenerator.php";
getMissionList(missionPageType::finishedMission);
?>

</body>
</html>
