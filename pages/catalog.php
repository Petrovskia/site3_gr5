<h3>Catalog page</h3>
<?php

echo '<div id="result" class="row">';
$items = Item::getItems();
foreach ($items as $item) {
    $item->drawItem();
}
echo '</div>';

?>

<script>
    function createCookie(ruser, id) {
        $.cookie(ruser, id, { expires: 2, path: '/' });
    }
</script>
