<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	include_once "function.php";
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<script type="text/javascript">

function meTubeDropdown() {
    document.getElementById("meTubeDropdown").classList.toggle("show");
}

// function accDropdown(){
//     document.getElementById("accDropdown").classList.toggle("show");
// }

function accDropdown(id){
    document.getElementById(id).classList.toggle("show");
}

</script>


<style>

.para{
    font-size: 20px;
}

.button_blue {
    background-color: #2B65EC;
    border: none;
    color: white;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 2px 1px;
    cursor: pointer;
}
.button {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    border: none;
    /*color: white;
    padding: 5px 5px;*/
    /*text-align: center;*/
    /*text-decoration: none;*/
    /*display: inline-block;*/
    /*font-size: 16px;
    margin: 2px 1px;*/
    cursor: pointer;
}


/* Dropdown Button */
.dropbtn {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    /*color: white;*/
    /*padding: 16px;*/
    /*font-size: 16px;*/
    border: none;
    cursor: pointer;
}

/* Dropdown button on hover & focus */
.dropbtn:hover, .dropbtn:focus {
    /*background-color: #3e8e41;*/
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
    position: relative;
    /*display: inline-block;*/
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}

/* Show the dropdown menu (use JS to add this class to the .dropdown-content container when the user clicks on the dropdown button) */
.show {display:block;}

</style>



</head>

<body>


<table>
    <tr>
        <td>
            <div class="dropdown">
                <button onclick="meTubeDropdown()" class="dropbtn">
                    <img src="https://cdn1.iconfinder.com/data/icons/android-user-interface-vol-1/16/38_-_menu_bar_lines_option_list_hamburger_web-512.png" alt="menu" style="width:35px;height:30px;">
                </button>
                <div id="meTubeDropdown" class="dropdown-content">
                    <a href="browse.php">Home</a>
                    <a href="my_media.php">My media</a>
                    <a href="my_channel.php">My channel</a>
                    <a href="my_playlist.php">My playlist</a>
                    <a href="my_favorites.php">My favorites</a>
                    <hr>
                    <a href="browse.php?category=<?php echo "music"?>">Music</a>
                    <a href="browse.php?category=<?php echo "gaming"?>">Gaming</a>
                    <a href="browse.php?category=<?php echo "movies"?>">Movies</a>
                    <a href="browse.php?category=<?php echo "tv_shows"?>">TV shows</a>
                    <a href="browse.php?category=<?php echo "news"?>">News</a>
                    <a href="browse.php?category=<?php echo "pets"?>">Pets</a>
                    <a href="browse.php?category=<?php echo "comedy"?>">Comedy</a>
                    <hr>
                    <a href="index_header.php?logout=1">Logout</a>



                </div>
            </div>
        </td>
        <td>
            <img src="https://lh3.googleusercontent.com/M5xLaQ7FIVKL6t_h9XubU9-Lh7SDxhnPKTZpT7wihzHoHYKM4hVgEEgNIJYbVDGkexw=w300" alt="MeTube" style="width:55px;height:45px; background-color:transparent; border-color:transparent;cursor:pointer;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
            <form action="browse.php" method="post">
                <input type="text"  name="search"  placeholder="Search" style ="width:300pt; height:20pt; font-size:16px" >
                <!-- <input type="botton" name="submit" background = "icon/search.png"> -->
                <button type="submit" style="background-color:transparent; border-color:transparent;">
                    <img src="http://www.pic4ever.com/images/2mpe5id.gif" height="19"/>
                </button>
            </form>
        </td>
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
             <form action="advanced_search.php" method="post">
              	<!-- <input type="submit"  name="advanced_search" value ="Advanced Search"> -->
                <button class="button_blue">Advanced Search</button>
             </form>
        </td>
        <td width=22%>
            &nbsp;&nbsp;&nbsp;
        </td>
        <td>
            <div class="dropdown">
                <button onclick="accDropdown('accDropdown_account')" class="dropbtn">
                    <img src="https://cdn2.iconfinder.com/data/icons/roundies-3/32/user-new-512.png" height="26"/>
                </button>
                <div id="accDropdown_account" class="dropdown-content">
                    <a href="my_account.php">My account</a>
                    <a href="my_groups.php">My groups</a>
                    <a href="index_header.php?logout=1">Logout</a>
                </div>
            </div>
                <!-- <button type="submit" style="background-color:transparent; border-color:transparent;cursor:pointer;">
                    <img src="https://cdn2.iconfinder.com/data/icons/roundies-3/32/user-new-512.png" height="35"/>
                </button> -->
            <!-- </form> -->
        </td>
        <td>
            <!-- <form action="my_message.php" method="post"> -->
            <div class="dropdown">
                <!-- <button type="submit" style="background-color:transparent; border-color:transparent;cursor:pointer;"> -->
                <button onclick="accDropdown('accDropdown_message')" class="dropbtn">
                    <img src="http://www.connectmedia.co.ke/wp-content/uploads/2014/08/orange-message.png" height="26"/>
                </button>
                <div id="accDropdown_message" class="dropdown-content">
                    <a href="new_message.php" style="color:white; background-color:red;">Compose</a>
                    <a href="inbox.php">Inbox</a>
                    <a href="sent_message.php">Sent message</a>
                </div>
            <!-- </form> -->
            </div>
        </td>
        <td>
            <form action="media_upload.php" method="post">
                <!-- <button type="submit" style="background-color:transparent; border-color:transparent;cursor:pointer;"> -->
                <button type="submit" style="background-color:transparent; border-color:transparent;cursor:pointer;">
                    <img src="http://www.iconsdb.com/icons/preview/royal-blue/upload-2-xxl.png" height="27"/>
                </button>
            </form>
        </td>
        <!-- <td>
            <form action="index.php" method="post">
                <button type="submit" style="background-color:transparent; border-color:transparent;cursor:pointer;">
                    <img src="https://t4.ftcdn.net/jpg/01/17/68/49/240_F_117684961_9dgPlrJxF3KaUMAcy4jMk1ySFDiKe7Od.jpg" width="50" height="40"/>
                </button>
            </form>
        </td> -->
    </tr>
</table>

<hr/>


</body>
</html>
