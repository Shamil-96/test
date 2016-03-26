<?php
 	session_start();
   include("include/product_conect.php");
   include("functions/function.php");
   
     $id = clear_string($_GET["id"]);
     $action = clear_string($_GET["action"]);
     
   switch ($action) {
 
        case 'clear':
        $clear = mysql_query("DELETE FROM cart WHERE cart_ip = '{$_SERVER['REMOTE_ADDR']}'",$link);     
        break;
         
        case 'delete':     
        $delete = mysql_query("DELETE FROM cart WHERE cart_id = '$id' AND cart_ip = '{$_SERVER['REMOTE_ADDR']}'",$link);        
        break;
         
    }
     
if (isset($_POST["submitdata"]))
{
if ( $_SESSION['auth'] == 'yes_auth' ) 
 {
         
    mysql_query("INSERT INTO orders(order_datetime,order_dostavka,order_fio,order_address,order_phone,order_note,order_email)
                        VALUES( 
                             NOW(),
                            '".$_POST["order_delivery"]."',                 
                            '".$_SESSION['auth_surname'].' '.$_SESSION['auth_name'].' '.$_SESSION['auth_patronumic']."',
                            '".$_SESSION['auth_address']."',
                            '".$_SESSION['auth_phone']."',
                            '".$_POST['order_note']."',
                            '".$_SESSION['auth_email']."'                             
                            )",$link);         
 
 }else
 {
$_SESSION["order_delivery"] = $_POST["order_delivery"];
$_SESSION["order_fio"] = $_POST["order_fio"];
$_SESSION["order_email"] = $_POST["order_email"];
$_SESSION["order_phone"] = $_POST["order_phone"];
$_SESSION["order_address"] = $_POST["order_address"];
$_SESSION["order_note"] = $_POST["order_note"];
 
    mysql_query("INSERT INTO orders(order_datetime,order_dostavka,order_fio,order_address,order_phone,order_note,order_email)
                        VALUES( 
                             NOW(),
                            '".clear_string($_POST["order_delivery"])."',                   
                            '".clear_string($_POST["order_fio"])."',
                            '".clear_string($_POST["order_address"])."',
                            '".clear_string($_POST["order_phone"])."',
                            '".clear_string($_POST["order_note"])."',
                            '".clear_string($_POST["order_email"])."'                  
                            )",$link);    
 }
 
                           
 $_SESSION["order_id"] = mysql_insert_id();                          
                             
$result = mysql_query("SELECT * FROM cart WHERE cart_ip = '{$_SERVER['REMOTE_ADDR']}'",$link);
If (mysql_num_rows($result) > 0)
{
$row = mysql_fetch_array($result);    
 
do{
 
    mysql_query("INSERT INTO buy_products(buy_id_order,buy_id_product,buy_count_product)
                        VALUES( 
                            '".$_SESSION["order_id"]."',                    
                            '".$row["cart_id_products"]."',
                            '".$row["cart_count"]."'                  
                            )",$link);
 
 
 
} while ($row = mysql_fetch_array($result));
}
                             
  header("Location: cart.php?action=completion");
}      
 
 
$result = mysql_query("SELECT * FROM cart,products WHERE cart.cart_ip = '{$_SERVER['REMOTE_ADDR']}' AND products.product_id = cart.cart_id_products",$link);
If (mysql_num_rows($result) > 0)
{
$row = mysql_fetch_array($result);
 
do
{ 
$int = $int + ($row["price"] * $row["cart_count"]); 
}
 while ($row = mysql_fetch_array($result));
  
 
   $itogpricecart = $int;
}     
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Корзина заказов</title>
	<link rel="stylesheet" href="trackbar/trackbar.css">
	<!-- <link rel="stylesheet" href="trackbar/trackbar2.css"> -->
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/style.css">
	<link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'>
	<script src="js/jquery-1.8.2.min.js"></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js'></script>
	<script src="js/shop-script.js"></script>
	<script src="trackbar/jquery.trackbar.js"></script>
	<script src="js/jcarousellite_1.0.1.js"></script>
</head>
<body>
<div class="body-block">
<?php    
    include("include/block-header.php");    
?>
<div class="block-right">
<?php    
    include("include/block-category.php");  
    include("include/block-parametr.php");  
    include("include/block-news.php"); 
?>
</div>
<div class="block-content">
 
<?php
 
  $action = clear_string($_GET["action"]);
  switch ($action) {
 
        case 'oneclick':
    
   echo ' 
   <div id="block-step">  
   		<div id="name-step">  
   			<ul>
			   <li><a class="active_cart" >1. Корзина товаров</a></li>
			   <li><span>&rarr;</span></i></li>
			   <li><a>2. Контактная информация</a></li>
			   <li><span>&rarr;</span></i></li>
			   <li><a>3. Завершение</a></li> 
  			</ul>  
   		</div>  
   		<p>шаг 1 из 3</p>
   		<a href="cart.php?action=clear" >Очистить</a>
   </div>
';
   
    
$result = mysql_query("SELECT * FROM cart,products WHERE cart.cart_ip = '{$_SERVER['REMOTE_ADDR']}' AND products.product_id = cart.cart_id_products",$link);
 
if (mysql_num_rows($result) > 0)
{
$row = mysql_fetch_array($result);
 
   echo '  
   <div id="header-list-cart">    
	   <div class=\'left-block\' id="head1" ><p>Изображение</p></div>
	   <div class=\'left-block\' id="head2" ><p>Наименование товара</p></div>
	   <div class=\'left-block\' id="head3" ><p>Кол-во</p></div>
	   <div class=\'left-block\' id="head4" ><p>Цена</p></div>
   </div> 
   ';
 
do
{
 
$int = $row["cart_price"] * $row["cart_count"];
$all_price = $all_price + $int;
 
if  (strlen($row["image"]) >= 0 )
{
$img_path = $row["image"];
$max_width = 150; 
$max_height = 150; 
 list($width, $height) = getimagesize($img_path); 
$ratioh = $max_height/$height; 
$ratiow = $max_width/$width; 
$ratio = min($ratioh, $ratiow); 
 
$width = intval($ratio*$width); 
$height = intval($ratio*$height);    
}else
{
$img_path = "images/noimages80x70.png";
$width = 150;
$height = 150;
} 
 
echo '
 
<div class="block-list-cart">
 
<div class="img-cart">
<p><img src="'.$img_path.'" width="'.$width.'" height="'.$height.'" /></p>
</div>
 
<div class="title-cart">
<p><a href="">'.$row["title"].'</a></p>
<p class="cart-mini-features">
'.$row["mini_features"].'
</p>
</div>
 
<div class="count-cart">
<ul class="input-count-style">
 
<li>
<p iid="'.$row["cart_id"].'" class="count-minus"><i class="fa fa-minus"></i></p>
</li>
 
<li>
<p><input id="input-id'.$row["cart_id"].'" id="'.$row["cart_id"].'" class="count-input" maxlength="3" type="text" value="'.$row["cart_count"].'" /></p>
</li>
 
<li>
<p iid="'.$row["cart_id"].'" class="count-plus"><i class="fa fa-plus"></i></p>
</li>
 
</ul>
</div>
 
<div id="tovar'.$row["cart_id"].'" class="price-product"><h5><span class="span-count" >'.$row["cart_count"].'</span> x <span>'.$row["cart_price"].'</span></h5><p price="'.$row["cart_price"].'" >'.$int.' грн.</p></div>
<div class="delete-cart"><a  href="cart.php?id='.$row["cart_id"].'&action=delete" ><img src="images/bsk_item_del.png" /></a></div>
 
<div id="bottom-cart-line"></div>
</div>
 
 
';
 
     
}
 while ($row = mysql_fetch_array($result));
  
 echo '
 <h2 class="itog-price">Итого: <strong>'.$all_price.'</strong> грн.</h2>
 <p  ><a class="button-next" href="cart.php?action=confirm" >Далее</a></p> 
 ';
   
} 
else
{
    echo '<h3 id="clear-cart">Корзина пуста</h3>';
}
 
 
    
        break;
         
        case 'confirm':     
      
    echo ' 
	   <div id="block-step"> 
	   <div id="name-step">  
		   <ul>
			   <li><a class=\'active_good\' href="cart.php?action=oneclick" >1. Корзина товаров</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a class="active_cart" >2. Контактная информация</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a>3. Завершение</a></li> 
		   </ul>  
	   </div> 
   <p>шаг 2 из 3</p>
 
   </div>
 
   '; 
    
 
if ($_SESSION['order_delivery'] == "По почте") $chck1 = "checked";
if ($_SESSION['order_delivery'] == "Курьерам") $chck2 = "checked";
if ($_SESSION['order_delivery'] == "Самовывоз") $chck3 = "checked"; 
  
 echo '
 
<h3 class="title-h3" >Способы доставки:</h3>
<form method="post">
<ul id="info-radio">
  <li>
  <input type="radio" name="order_delivery" class="order_delivery" id="order_delivery1" value="По почте" '.$chck1.'  />
  <label class="label_delivery" for="order_delivery1">По почте</label>
  </li>
  <li>
  <input type="radio" name="order_delivery" class="order_delivery" id="order_delivery2" value="Курьерам" '.$chck2.' />
  <label class="label_delivery" for="order_delivery2">Курьерoм</label>
  </li>
  <li>
  <input type="radio" name="order_delivery" class="order_delivery" id="order_delivery3" value="Самовывоз" '.$chck3.' />
  <label class="label_delivery" for="order_delivery3">Самовывоз</label>
  </li>
  </ul>
<h3 class="title-h3" >Информация для доставки:</h3>
<div class=\'info-user\'>

';
  if ( $_SESSION['auth'] != 'yes_auth' ) 
{
echo '
  <p for="order_fio">ФИО:</p><input type="text" name="order_fio" id="order_fio" value="'.$_SESSION["order_fio"].'" /><span class="order_span_style" >Пример: Иванов Иван Иванович</span>
  <p for="order_email">E-mail:</p><input type="text" name="order_email" id="order_email" value="'.$_SESSION["order_email"].'" /><span class="order_span_style" >Пример: ivanov@mail.ru</span>
  <p for="order_phone">Телефон:</p><input type="text" name="order_phone" id="order_phone" value="'.$_SESSION["order_phone"].'" /><span class="order_span_style" >Пример: 8 950 100 12 34</span>
  <p class="order_label_style" for="order_address">Адрес доставки:</p><input type="text" name="order_address" id="order_address" value="'.$_SESSION["order_address"].'" /><span>Пример: г. Москва, ул Интузиастов д 18, кв 58</span>
  ';
  }
  echo '
 
</div>
  <div class="textarea">
    <p>Примечание:</p><textarea name="order_note"  >'.$_SESSION["order_note"].'</textarea><span>Уточните информацию о заказе. Например, удобное время для звонка нашего менеджера.</span>
  </div>

<p ><input class=\'button-next\' type="submit" name="submitdata" id="confirm-button-next" value="Далее" /></p>
</form>
 
 
 ';      
       
        break;
         
        case 'completion': 
 
    echo ' 
   <div id="block-step"> 
	   <div id="name-step">  
		   <ul>
			   <li><a class=\'active_good\' href="cart.php?action=oneclick" >1. Корзина товаров</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a class=\'active_good\' href="cart.php?action=confirm" >2. Контактная информация</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a class="active_cart" >3. Завершение</a></li> 
		   </ul>  
	   </div> 
   <p>шаг 3 из 3</p>
 
   </div>
 
<h3 class=\'title-h3\'>Конечная информация:</h3>
   '; 
 
if ( $_SESSION['auth'] == 'yes_auth' ) 
    {
echo '

  <div class=\'block-info\'> 
    <ul id="list-info" >
      <li><strong>Способ доставки: </strong>'.$_SESSION['order_delivery'].'</li>
      <li><strong>Email: </strong>'.$_SESSION['auth_email'].'</li>
      <li><strong>ФИО: </strong>'.$_SESSION['auth_surname'].' '.$_SESSION['auth_name'].' '.$_SESSION['auth_patronumic'].'</li>
      <li><strong>Адрес доставки: </strong>'.$_SESSION['auth_address'].'</li>
      <li><strong>Телефон: </strong>'.$_SESSION['auth_phone'].'</li>
      <li><strong>Примечание: </strong>'.$_SESSION['order_note'].'</li>
    </ul>
  </div>
 
';
   }else
   {
echo '
  <div class=\'block-info\'> 
    <ul id="list-info" >
      <li><strong>Способ доставки: </strong>'.$_SESSION['order_delivery'].'</li>
      <li><strong>Email: </strong>'.$_SESSION['order_email'].'</li>
      <li><li><strong>ФИО: </strong>'.$_SESSION['order_fio'].'</li>
      <li><strong>Адрес доставки: </strong>'.$_SESSION['order_address'].'</li>
      <li><strong>Телефон: </strong>'.$_SESSION['order_phone'].'</li>
      <li><strong>Примечание: </strong>'.$_SESSION['order_note'].'</li>
    </ul>
  </div>';    
}
 echo '
<h2 class="itog-price" >Итого: <strong>'.$itogpricecart.'</strong> грн</h2>
  <p class="button-next" ><a href="" >Оплатить</a></p> 
  
 '; 
 
 
         
        break;
         
        default:  
            
   echo ' 
   <div id="block-step">  
	   <div id="name-step">  
		   <ul>
			   <li><a class="active_cart" >1. Корзина товаров</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a>2. Контактная информация</a></li>
			   <li><span>&rarr;</span></li>
			   <li><a>3. Завершение</a></li> 
		   </ul>  
	   </div>  
   <p>шаг 1 из 3</p>
   <a href="cart.php?action=clear" >Очистить <i class="fa fa-trash"></i></a>
   <hr>
   </div>
';
   
    
$result = mysql_query("SELECT * FROM cart,products WHERE cart.cart_ip = '{$_SERVER['REMOTE_ADDR']}' AND products.product_id = cart.cart_id_products",$link);
 
If (mysql_num_rows($result) > 0)
{
$row = mysql_fetch_array($result);
 
   echo '  
   <div id="header-list-cart">    
     <div class=\'left-block\' id="head1" ><p>Изображение</p></div>
     <div class=\'left-block\' id="head2" ><p>Наименование товара</p></div>
     <div class=\'left-block\' id="head3" ><p>Кол-во</p></div>
     <div class=\'left-block\' id="head4" ><p>Цена</p></div>
   </div> 
   ';
 
do
{
 
$int = $row["cart_price"] * $row["cart_count"];
$all_price = $all_price + $int;
 
if  (strlen($row["image"]) > 0)
{
$img_path = $row["image"];
$max_width = 150; 
$max_height = 150; 
 list($width, $height) = getimagesize($img_path); 
$ratioh = $max_height/$height; 
$ratiow = $max_width/$width; 
$ratio = min($ratioh, $ratiow); 
 
$width = intval($ratio*$width); 
$height = intval($ratio*$height);    
}else
{
$img_path = "images/noimages80x70.png";
$width = 120;
$height = 105;
} 
 
echo '
 
<div class="block-list-cart">
 
<div class="img-cart">
<p align="center"><img src="'.$img_path.'" width="'.$width.'" height="'.$height.'" /></p>
</div>
 
<div class="title-cart">
<p><a href="">'.$row["title"].'</a></p>
<p class="cart-mini-features">
'.$row["mini_features"].'
</p>
</div>
 
<div class="count-cart">
<ul class="input-count-style">
 
<li>
<p iid="'.$row["cart_id"].'" class="count-minus"><i class="fa fa-minus"></i></p>
</li>
 
<li>
<p align="center"><input id="input-id'.$row["cart_id"].'" iid="'.$row["cart_id"].'" class="count-input" maxlength="3" type="text" value="'.$row["cart_count"].'" /></p>
</li>
 
<li>
<p iid="'.$row["cart_id"].'" class="count-plus"><i class="fa fa-plus"></i></p>
</li>
 
</ul>
</div>
 
<div id="tovar'.$row["cart_id"].'" class="price-product"><h5><span class="span-count" >'.$row["cart_count"].'</span> x <span>'.$row["cart_price"].'</span></h5><p price="'.$row["cart_price"].'" >'.$int.' руб</p></div>
<div class="delete-cart"><a  href="cart.php?id='.$row["cart_id"].'&action=delete" ><img src="images/bsk_item_del.png" /></a></div>
 
<div id="bottom-cart-line"></div>
</div>
 
 
';
 
     
}
 while ($row = mysql_fetch_array($result));
  
 echo '
 <h2 class="itog-price" align="right">Итого: <strong>'.$all_price.'</strong> руб</h2>
 <p align="right" class="button-next" ><a href="cart.php?action=confirm" >Далее</a></p> 
 ';
   
} 
else
{
    echo '<h3 id="clear-cart">Корзина пуста</h3>';
}
        break;      
         
}
     
?>
 
</div>
 
<?php    
    include("include/block-footer.php");    
?>
</div>
 
</body>
</html>