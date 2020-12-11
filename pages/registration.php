<h3>Registration form</h3>
<?php
if(!isset($_POST['regbtn'])) {
?>
    <form action="index.php?page=3" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="login">Login:
                <input type="text" class="form-control" name="login" id="login" autocomplete="off">
            </label>
        </div>
        <div class="form-group">
            <label for="pass1">Password:
                <input type="password" class="form-control" name="pass1" id="pass1">
            </label>
        </div>
        <div class="form-group">
            <label for="pass2">Password confirm:
                <input type="password" class="form-control" name="pass2" id="pass2">
            </label>
        </div>

        <div class="form-group">
            <label for="imagepath">Upload image:
                <input type="file" class="form-control" name="imagepath" id="imagepath">
            </label>
        </div>
        <input type="submit" name="regbtn" class="btn btn-primary" value="Register">
    </form>

    <?php
} else {
    if(is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
        $path = "images/users/".$_FILES['imagepath']['name'];
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    if($_POST['pass1'] === $_POST['pass2']) {
        $customer = new Customer($_POST['login'], $_POST['pass1'], $path);
        if($customer->register()) {
            echo "<h3 class='text-success'>Пользователь добавлен</h3>";
        }
    }
}

