<?php
session_start();

include_once "function.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Advanced Search</title>

<style>

.required:after {
    color: red;
    content: '*';
}

</style>

</head>

<body>


<?php
if (isset($_SESSION["username"])){
    echo "<form method= 'POST' action='browse.php?advanced_search=1' id ='advanced_search' name='advanced_search' >";
}
else {
    echo "<form method= 'POST' action='index_header.php?advanced_search=1' id ='advanced_search' name='advanced_search' >";
}

if (isset($_POST['cancel'])){
    header('Location: browse.php');
}
?>
<!-- <form method="POST" action="advanced_search.php" id="advanced_search" name="advanced_search" > -->

    <table border="0" align="center" cellpadding='6'>
        <tr>
            <th bgcolor="grey" colspan="2"><font color="white" size=10>Advanced Search<font/></th>
        <tr>
            <td ><textarea rows="3" cols="75" name="search_title" form="advanced_search" placeholder="Title" style="font-size:14px"></textarea></td>
        <tr>
        <tr>
            <td >
                <label class="required">Category</label>
                <!-- Category&nbsp;&nbsp; -->
                <select name="search_category" required>
                    <option value="music">Music</option>
                    <option value="gaming">Gaming</option>
                    <option value="movies">Movies</option>
                    <option value="tv">TV shows</option>
                    <option value="news">News</option>
                    <option value="pets">Pets/Animals</option>
                    <option value="comedy">Comedy</option>
                </select><br />
            </td>
        </tr>
        <tr>
            <td >Type&nbsp;&nbsp;
                <select name="search_type">
                    <option value="DEFAULT"></option>
                    <option value="mp4">mp4</option>
                    <option value="wmv">wmv</option>
                    <option value="mov">mov</option>
                </select><br/>
            </td>
        </tr>
        <tr>
            <td >Maximum size&nbsp;&nbsp;
                <select name="search_size">
                    <option value=30 DEFAULT>30 MB</option>
                    <option value=1>1 MB</option>
                    <option value=10>10 MB</option>
                    <option value=20>20 MB</option>
                </select><br/>
            </td>
        </tr>
        <tr>
            <td >
                <div id="search_upload_time">
                    Uploaded Time <br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;<label class="required">Before</label><input type="date" name="search_before" required>
                    &nbsp;<label class="required">After</label> <input type="date" name="search_after" required>
                        <select name="search_order">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select><br/>

                    <!-- <textarea rows="3" cols="70" name="before_time" form="advanced_search" placeholder="Before mm/dd/yy"></textarea><br/>
                    <textarea rows="3" cols="70" name="after_time" form="advanced_search" placeholder="After mm/dd/yy"></textarea><br/> -->
                </div>
            </td>
        </tr>
        <!-- <tr>
            <td >
                <textarea rows="6" cols="70" name="search_keywords" form="advanced_search" placeholder="Key Words"></textarea>
            </td>
        </tr> -->
        <tr>
            <td>
                <textarea rows="10" cols="75" name="search_description" form="advanced_search" placeholder="Description" style="font-size:14px"></textarea>
            </td>
        </tr>
        <tr>
			<td><input name="submit" type="submit" value="Submit"><input name="reset" type="reset" value="Reset"><input name="cancel" type="submit" value="Cancel" formnovalidate><br/></td>
		</tr>
    </table>

 </form>

</body>
</html>
