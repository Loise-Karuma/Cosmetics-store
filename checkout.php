<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      };
   };

   $total_products = implode(', ', $cart_products);

   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'order placed already!';
   }else{
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'order placed successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= '$'.$fetch_cart_items['price'].'/- x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
    }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>
   <div class="grand-total">grand total : <span>$<?= $cart_grand_total; ?>/-</span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>place your order</h3>

      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" id="name" placeholder="enter your name" class="box" required>
            <small class="text-danger name"></small>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" id="number" placeholder="enter your number" class="box" required>
            <small class="text-danger number"></small>
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" id="email" placeholder="enter your email" class="box" required>
            <small class="text-danger email"></small>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" id="city" placeholder="e.g. mumbai" class="box" required>
            <small class="text-danger city"></small>
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" id="pincode" placeholder="e.g. 123456" class="box" required>
            <small class="text-danger pincode"></small>
         </div>
      </div>
      
      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="place order">
      <div id="paypal-button-container" class="mt-2"></div>
   </form>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
 <!-- Replace "test" with your own sandbox Business account app client ID -->
 <script src="https://www.paypal.com/sdk/js?client-id=AV5kwLxbhCQjX4Gg8nUpB3TSOGfZ3gjvYqBDB891AHe2KCodY39K_3jkhVrv_RzENXRSg7Htm0SVq06t&currency=USD"></script>  

<script>

   paypal.Buttons({
         onClick(){
               var name = $('#name').val();
               var email = $('#email').val();
               var number = $('#number').val();
               var city = $('#city').val();
               var pincode = $('#pincode').val();
         
         alert(name.length;)

         if(name.length == 0)
         {
            $('name').text("*This field is required");
            return false;
         }else{
            $('.name').text("");
         }
         if(email.length == 0)
         {
            $('email').text("*This field is required");
            return false;
         }else{
            $('.email').text("");
         }
         if(number.length == 0)
         {
            $('number').text("*This field is required");
            return false;
         }else{
            $('.number').text("");
         }
         if(city.length == 0)
         {
            $('city').text("*This field is required");
            return false;
         }else{
            $('.city').text("");
         }
         if(pincode.length == 0)
         {
            $('pincode').text("*This field is required");
            return false;
         }else{
            $('.pincode').text("");
         }
      },
      createOrder: (data, actions) => {
         return actions.order.create({
            purchase_units: [{
               amount: {
                  value: '<?= $cart_grand_total; ?>'
               }
            }]
         });
      },
      onApprove: (data, actions) => {
         return actions.order.capture().then(function(orderData) {
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            const transaction = orderData.purchase_units[0].payments.captures[0];
            alert(`transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
         });
      }
   }).render('#paypal-button-container');
 </script>
</body>
</html>