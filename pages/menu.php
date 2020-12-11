<!--<ul class="nav nav-pills justify-content-around w-100">-->
<!--    <li><a href="index.php?page=1" class="nav-link active--><?php //echo ($_GET['page'] === '1' OR isset($_GET['page'])? "active":"") ?><!--">Catalog</a></li>-->
<!--    <li><a href="index.php?page=2" class="nav-link --><?php //echo ($_GET['page'] === '2')? "active":"" ?><!--">Cart</a></li>-->
<!--    <li><a href="index.php?page=3" class="nav-link --><?php //echo ($_GET['page'] === '3')? "active":"" ?><!--">Registration</a></li>-->
<!--    <li><a href="index.php?page=4" class="nav-link --><?php //echo ($_GET['page'] === '4')? "active":"" ?><!--">Admin Forms</a></li>-->
<!--</ul>-->

<ul class="nav nav-pills justify-content-around w-100">
    <li class="nav-item"><a href="index.php?page=1" class="nav-link" data-link="1">Catalog</a></li>
    <li class="nav-item"><a href="index.php?page=2" class="nav-link" data-link="2">Cart</a></li>
    <li class="nav-item"><a href="index.php?page=3" class="nav-link" data-link="3">Registration</a></li>
    <li class="nav-item"><a href="index.php?page=4" class="nav-link" data-link="4">Admin Forms</a></li>
</ul>

<script>

    $(function () {
        $("a").click(function() {
            let linkNumber = $(this).attr("href").slice(-1);
            $("a").removeClass('active');
            $(this).addClass('active');
            localStorage.active = linkNumber;
        });

        $(`a[data-link="${localStorage.active}"]`).addClass('active');
    });
</script>