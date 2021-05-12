<?php

//echo header bar
echo '
<nav class="navbar navbar-dark pageHeaderBar sticky-top navbar-expand-lg">

<a class="navbar-brand" href="#" data-toggle="modal" data-target="#personalInformationModal">
<img src="image/skyeye_logo.png" width="50%" height="50%" alt="">
</a> 
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="createUser.php" class="nav-link">Create User</a>
        </li>
        <li class="nav-item">
            <a href="updateUser.php" class="nav-link">Update User</a>
        </li>
        <li class="nav-item">
            <a href="deleteUser.php" class="nav-link">Delete User</a>
        </li>
    </ul>
</div>

</nav>
';

//get personal information
$url = "http://ec2-54-179-174-219.ap-southeast-1.compute.amazonaws.com/application/user/get.php/?token=" . $_SESSION["userDetails"]["userToken"];
        //$url = 'http://' . $_SERVER['HTTP_HOST'] . "/testLogin.php"; //For relative path usage
        //$url = "https://dsafyp1819skyeye.azurewebsites.net/testLogin.php";
        $json = file_get_contents($url, true);
        $obj = json_decode($json, true);
        $_SESSION["SID"] = $obj['result'][0]['SID'];
//echo personal information and logout btn
echo '
<!-- Modal -->
<div class="modal fade" id="personalInformationModal" tabindex="-1" role="dialog" aria-labelledby="personalInfoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title blackText" id="personalInfoTitle">Personal Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body blackText">
                <b>Member ID:</b> '. $obj['result'][0]['USERID'].'<br>
                <b>Name:</b> '. $obj['result'][0]['UNAME'] .'<br>
                <b>Role:</b> '. $_SESSION["userDetails"]["mode"] .'<br>
                
            </div>
            <div class="modal-footer">
                <button onclick="changeMissionPage()" type="button" class="btn btn-secondary" data-dismiss="modal">Mission Management</button>
                <button onclick="changeUserPage()" type="button" class="btn btn-secondary" data-dismiss="modal">Drone Management</button>
                <form action="checkIsLogined.php" method="post">
                    <button type="submit" name="logout_btn" value="logout" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function changeMissionPage() {
      location.replace("OngoingMission.php")
    }
    function changeUserPage() {
      location.replace("CreateDrone.php")
    }
</script>
';
