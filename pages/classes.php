<?php

class Tools {
    static function connect($host="localhost:3306", $user='root', $pass='123456', $dbname='shop') {
        // PDO (PHP data object) - механизм взаимодйствия с СУБД(система управления базами данных)
        // PDO - позволяет облегчить рутинные задачи при выполнении запросов и содержит защитные механизмы при работе с СУБД

        // определим DSN(Data source name) - сведения для подключения к БД.
        $cs = "mysql:host=$host;dbname=$dbname;charset=utf8";

        // массив опций для создания PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
        ];

        try {
            $pdo = new PDO($cs, $user, $pass, $options);
            return $pdo;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}


class Customer {
    public $id;
    public $login;
    public $pass;
    public $roleid;
    public $discount;
    public $total;
    public $imagepath;

    function __construct($login, $pass, $imagepath, $id = 0) {
        $this->login = trim($login);
        $this->pass = trim($pass);
        $this->imagepath = $imagepath;
        $this->id = $id;

        $this->total = 0;
        $this->discount = 0;
        $this->roleid = 2;
    }


    function register() {
        if($this->login === '' || $this->pass === '') {
            echo "<h3 class='text-danger'>Заполните все поля</h3>";
            return false;
        }

        if(strlen($this->login) < 3 || strlen($this->login) > 32 || strlen($this->pass) < 3 || strlen($this->pass) > 128 ) {
            echo "<h3 class='text-danger'>Не корректная длина полей</h3>";
            return false;
        }

        $this->intoDb();

        return true;
    }

    function intoDb() {
        try {
            $pdo = Tools::connect();
            // подготовим(prepare) запрос за добавление пользователя
            $ps = $pdo->prepare("INSERT INTO customers(login, pass, roleid, discount, total, imagepath) VALUES (:login, :pass, :roleid, :discount, :total, :imagepath)");

            // разименовывание объета this, и преобразование к массиву
            $ar = (array)$this; // $ar = [:id, :login, :pass, :roleid, :discount, :total, :imagepath]
            array_shift($ar); // удаляем первый элемент массива, т.е. :id
            // ar = :login, :pass, :roleid, :discount, :total, :imagepath
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}


class Item {
    public $id;
    public $itemname;
    public $catid;
    public $pricein;
    public $pricesale;
    public $info;
    public $imagepath;
    public $rate;
    public $action;

    function __construct($itemname, $catid, $pricein, $pricesale, $info, $imagepath, $rate=0, $action=0, $id=0) {
        $this->id = $id;
        $this->itemname = $itemname;
        $this->catid = $catid;
        $this->pricein = $pricein;
        $this->pricesale = $pricesale;
        $this->info = $info;
        $this->imagepath = $imagepath;
        $this->rate = $rate;
        $this->action = $action;
    }

    function intoDb() {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO items(itemname, catid, pricein, pricesale, info, imagepath, rate, action) VALUES (:itemname, :catid, :pricein, :pricesale, :info, :imagepath, :rate, :action)");
            $ar = (array)$this;
            array_shift($ar);
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function fromDb($id) {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM items WHERE id=?");
            $ps->execute([$id]);
            $row = $ps->fetch();
            $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagepath'], $row['rate'], $row['action'], $row['id']);
            return $item;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // метод формирования списка товаров
    static function getItems($catid = 0) {
        try {
            $pdo = Tools::connect();
            // если категория не выбрана на странцие catalog, то выбираем все товары
            // если $catid была передана, то выбираем по конкретной категории
            if($catid === 0) {
                $ps = $pdo->query("SELECT * FROM items");
            } else {
                $ps = $pdo->prepare("SELECT * FROM items WHERE catid=?");
                $ps->execute([$catid]);
            }

            while ($row = $ps->fetch()) {
                $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagepath'], $row['rate'], $row['action'], $row['id']);
                $items[] = $item; // создадим массив экземпляров(объектов) класса Item
            }
            return $items;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // метод отрисовки товаров
    function drawItem() {
        echo '<div class="col-sm-6 col-md-3 border item-card">';
        // шапка товара
        echo '<div class="mt-1 bg-dark item-card__header">';
        echo "<a href='pages/iteminfo.php?name={$this->id}' target='_blank' class='ml-2 float-left'>$this->itemname</a>";
        echo "<span class='mr-2 float-right'>$this->rate</span>";
        echo '</div>';

        // изображение товара
        echo '<div class="mt-1 item-card__img">';
        echo "<img src='{$this->imagepath}' alt='image' class='img-fluid'>";
        echo '</div>';
        // цена товара
        echo '<div class="mt-1 bg-primary text-center item-card__price">';
        echo "<span class='lead text-white '>$this->pricesale рублей</span>";
        echo '</div>';

        // описание товара
        echo '<div class="mt-1 text-center item-card__info">';
        echo "<span class='lead'>$this->info</span>";
        echo '</div>';

        // кнопка добавления в корзину
        echo '<div class="mt-1 text-center">';

        $ruser = 'cart_'.$this->id;
        echo "<button class='btn btn-primary btn-lg btn-block' onclick=createCookie('".$ruser."','".$this->id."')>Add to cart</button>";
        echo '</div>';

        echo '</div>';
    }

    function drawItemAtCart() {
        echo '<div class="row m-2">';
        echo "<span class='col-1'>$this->id</span>";
        echo "<img src='{$this->imagepath}' alt='image' class='col-1 img-fluid'>";
        echo "<span class='col-3'>$this->itemname</span>";
        echo "<span class='col-3'>$this->pricesale</span>";
        $ruser = 'cart_'.$this->id;
        echo "<button class='btn btn-danger' onclick=eraseCookie('".$ruser."')>X</button>";
        echo '</div>';
    }

    function sale() {
        try {
            $pdo = Tools::connect();
            $upd = "UPDATE customers SET total=total+? WHERE login=?";
            $ps = $pdo->prepare($upd);
            $login = 'admin';
            $ps->execute([$this->pricesale, $login]);

            // добавить логи для покупки на сайте
            $ins = "INSERT INTO sales(customername, itemname, pricein, pricesale, datesale) VALUES (?,?,?,?,?)";
            $ps = $pdo->prepare($ins);
            $ps->execute([$login, $this->itemname, $this->pricein, $this->pricesale, @date("Y/m/d H:i:s")]);
            return $this->id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function SMTP($id_result) {
        require_once ("PHPMailer/PHPMailerAutoload.php");

        $mail = new PHPMailer;
        // настройка протокола SMTP(протокол передачи данных почтовых сообщений)
        $mail->CharSet = "UTF-8";
        $mail->isSMTP();

        // аутентификация
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->Username=MAIL;
        $mail->Password=PASS;



    }
}
