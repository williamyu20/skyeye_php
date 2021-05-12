<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php include "header.php"; ?>
    <title>Mission Report- SkyEye</title>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXYPXriGjkZIuRcm7A6AqV9TWY7ro0uE4"></script>

  </head>
  <body>
    <?php
        include "checkIsLogined.php";
        include "Missionpagemenu.php";
        include "ReportGen.php";
    ?>
  </body>
</html>