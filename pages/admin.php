<h3>Admin forms</h3>
<?php
if(!isset($_POST['addbtn'])) {
?>
    <form action="index.php?page=4" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category">Category:
                <select name="catid" id="category">
                    <?php
                    $pdo = Tools::connect();
                    $ps = $pdo->query("SELECT * FROM categories"); // выполнить запрос(вместо prepare() и execute())
                    while ($row = $ps->fetch()) {
                        echo "<option value='{$row['id']}'>{$row['category']}</option>";
                    }
                    ?>
                </select>
            </label>
        </div>
        <hr>

        <div class="form-group">
            <label for="name">Name:
                <input type="text" name="name" id="name">
            </label>
        </div>
        <hr>
        <div class="form-group">
            <p>Incoming price and sale price</p>
            <div>
                <label for="pricein">PriceIn:
                    <input type="number" name="pricein" id="pricein">
                </label>
                <label for="pricesale">PriceSale:
                    <input type="number" name="pricesale" id="pricesale">
                </label>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label for="info">Info:
                <textarea name="info" id="info" cols="30" rows="2"></textarea>
            </label>
        </div>
        <hr>
        <div class="form-group">
            <label for="imagepath">Image:
                <input type="file" accept="image/*" name="imagepath" id="imagepath">
            </label>
        </div>

        <input type="submit" class="btn btn-primary" name="addbtn" value="Add good">
    </form>
<?php
} else {
    if(is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
        $path = "images/goods/".$_FILES['imagepath']['name'];
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    $item = new Item(trim($_POST['name']), $_POST['catid'], $_POST['pricein'], $_POST['pricesale'], $_POST['info'], $path);
    $item->intoDb();
}
?>
