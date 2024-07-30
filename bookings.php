<?php

global $conn;
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['cancel'])){

   $booking_id = $_POST['booking_id'];
   $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_booking->execute([$booking_id]);

   if($verify_booking->rowCount() > 0){
      $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_booking->execute([$booking_id]);
      $success_msg[] = 'booking cancelled successfully!';
   }else{
      $warning_msg[] = 'booking cancelled already!';
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>bookings</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- booking section starts  -->

<section class="bookings">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

   <h1 class="heading">MY BOOKINGS</h1>

    <br>
    <br>
    <form method="post" name="f_booking" id="boo">
        <label> Enter ID: </label>
        <input type="text" name="booking">
        <br>
        <input type="submit" value="Submit">
        <br>
        <br>
        <br>
        <br>
    </form>

    <div class="box-container">

        <?php
        if (isset($_POST['booking'])) {
            $enteredBookingID = $_POST['booking'];

            $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE iduser = ? ");
            $select_bookings->execute([$enteredBookingID]);

            if ($select_bookings->rowCount() > 0) {
                ?>
                <script>
                    document.getElementById("boo").style.display="none"; // To show the form
                </script>
        <?php
                while ($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="box">
                        <p>Name : <span><?= $fetch_booking['name']; ?></span></p>
                        <p>Email : <span><?= $fetch_booking['email']; ?></span></p>
                        <p>Number : <span><?= $fetch_booking['number']; ?></span></p>
                        <p>Check in : <span><?= $fetch_booking['check_in']; ?></span></p>
                        <p>Check out : <span><?= $fetch_booking['check_out']; ?></span></p>
                        <p>Rooms : <span><?= $fetch_booking['rooms']; ?></span></p>
                        <p>Adults : <span><?= $fetch_booking['adults']; ?></span></p>
                        <p>Childs : <span><?= $fetch_booking['childs']; ?></span></p>
                        <br>
                        <!--<p>booking id : <span><?= $fetch_booking['booking_id']; ?></span></p>-->
                        <form action="" method="POST">
                            <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
                            <input type="submit" value="cancel booking" name="cancel" class="btn" onclick="return confirm('cancel this booking?');">
                        </form>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="box" style="text-align: center;">
                    <p style="padding-bottom: .5rem; text-transform:capitalize;">no bookings found!</p>
                    <a href="index.php#reservation" class="btn">book new</a>
                </div>
                <?php
            }
        }
        ?>
    </div>



</section>

<!-- booking section ends -->


<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>