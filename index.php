<?php

session_start();
global $conn;
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 1){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $iduser=$_POST['iduser'];
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ? AND iduser = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs, $iduser]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs,iduser) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs,$iduser]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MillenniumPalestine</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">


</head>
<body>

<!-- header section starts  -->

<div class="header" >
    <nav class="navbar">
        <a href="#home">HOME</a>
        <a href="#about">ABOUT</a>
        <a href="#reservation">RESERVATION</a>
        <a href="#gallery">GALLERY</a>
        <a href="#contact">CONTACT</a>
        <a href="#reviews">REVIEWS</a>
        <a href="bookings.php">MY BOOKINGS</a>
    </nav>


    <div class="flex">

        <a href="#home" class="logo">MILLENNIUM PALESTINE</a>
        <div id="menu-btn" class="fas fa-bars"></div>
    </div>
    <br>
    <a class="b">HOTELS AND RESORTS</a>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
</div>




<!-- home section starts  -->

<section class="home" id="home">

    <div class="swiper home-slider">

        <div class="swiper-wrapper">

            <div class="box swiper-slide center">
                <img src="images/mill.jpg" alt="Millennium">
                <div class="flex">
                    <a href="#availability" class="btn">Check availability</a>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

    <form action="" method="post">
        <div class="flex">
            <div class="box">
                <p>Check in <span>*</span></p>
                <input type="date" name="check_in" class="input" required>
            </div>
            <br>
            <br>
            <div class="box">
                <p>Check out <span>*</span></p>
                <input type="date" name="check_out" class="input" required>
            </div>
            <br>
            <br>
            <div class="box">
                <p>Adults <span>*</span></p>
                <select name="adults" class="input" required>
                    <option value="1">1 adult</option>
                    <option value="2">2 adults</option>
                    <option value="3">3 adults</option>
                    <option value="4">4 adults</option>
                    <option value="5">5 adults</option>
                    <option value="6">6 adults</option>
                </select>
            </div>
            <br>
            <br>
            <div class="box">
                <p>Childs <span>*</span></p>
                <select name="childs" class="input" required>
                    <option value="-">0 child</option>
                    <option value="1">1 child</option>
                    <option value="2">2 childs</option>
                    <option value="3">3 childs</option>
                    <option value="4">4 childs</option>
                    <option value="5">5 childs</option>
                    <option value="6">6 childs</option>
                </select>
            </div>
            <br>
            <br>
            <div class="box">
                <p>Rooms <span>*</span></p>
                <select name="rooms" class="input" required>
                    <option value="1">1 room</option>
                    <option value="2">2 rooms</option>
                    <option value="3">3 rooms</option>
                    <option value="4">4 rooms</option>
                    <option value="5">5 rooms</option>
                    <option value="6">6 rooms</option>
                </select>
            </div>
            <br>
            <br>
        </div>

        <input type="submit" value="Check" name="check" class="btn">
    </form>
    <br>
    <br>
    <br>
</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">
    <p class="para">ABOUT</p>
    <br>
    <br>
    <div class="row">
        <div class="image">
            <img src="images/staff.jpg" alt="">
        </div>
        <div class="content">
            <h3>Best staff</h3>
            <p>Dedicated to excellence, our hotel boasts the Best Staff, ensuring unforgettable experiences for our valued guests. Warm hospitality and expert care make us a home away from home.</p>
            <a href="#reservation" class="btn">Make a reservation</a>
        </div>
    </div>

    <div class="row revers">
        <div class="image">
            <img src="images/food.jpg" alt="">
        </div>
        <div class="content">
            <h3>Best foods</h3>
            <p> Indulge in the finest dining experience with our Best Foods. From exquisite flavors to exceptional service, we ensure a culinary journey like no other.</p>
            <a href="#contact" class="btn">Contact us</a>
        </div>
    </div>

    <div class="row">
        <div class="image">
            <img src="images/boll.jpg" alt="">
        </div>
        <div class="content">
            <h3>Swimming pool</h3>
            <p>Immerse yourself in luxury at our Swimming Pool. Relax and unwind in a serene oasis, where time slows down, and worries melt away.</p>
            <a href="#availability" class="btn">Check availability</a>
        </div>
    </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">
    <br>
    <br>
    <br>
    <br>
    <br>
    <p class="par">THINGS TO DO</p>
    <br>
    <br>

    <div class="box-container">

        <div class="box">
            <img src="images/icon-1.png" alt="">
            <h3>food & drinks</h3>
            <p>Experience gastronomic delights with our exquisite Food & Drinks offerings. Indulge in a world of flavors and enjoy the finest culinary creations in a warm and inviting ambiance.</p>
        </div>

        <div class="box">
            <img src="images/icon-2.png" alt="">
            <h3>outdoor dining</h3>
            <p>Embrace nature's beauty while savoring culinary delights at our enchanting Outdoor Dining. Enjoy the perfect blend of exquisite food and the refreshing outdoor atmosphere for a memorable dining experience.</p>
        </div>

        <div class="box">
            <img src="images/icon-4.png" alt="">
            <h3>decorations</h3>
            <p>Experience a world of enchantment through our exquisite Decorations. Immerse yourself in a realm of beauty and charm, where every detail is meticulously crafted to create an ambiance of warmth and wonder.</p>
        </div>

        <div class="box">
            <img src="images/icon-5.png" alt="">
            <h3>swimming pool</h3>
            <p>Dive into blissful relaxation at our Swimming Pool. Enjoy the freedom and serenity of the water, where worries dissipate, and a refreshing escape awaits.</p>
        </div>



    </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->
<section class="reservation" id="reservation">
    <br>
    <br>
    <br>
    <br>
    <p class="r">MAKE A RESERVATION</p>
    <br>
    <br>
    <form action="" method="post">

        <div class="flex">
            <div class="box">
                <p>Your ID <span>*</span></p>
                <input type="text" name="iduser" maxlength="50" required placeholder="enter your id" class="input">
            </div>
            <div class="box">
                <p>Your Name <span>*</span></p>
                <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
            </div>
            <div class="box">
                <p>Your Email <span>*</span></p>
                <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
            </div>
            <div class="box">
                <p>Your Number <span>*</span></p>
                <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
            </div>
            <div class="box">
                <p>Rooms <span>*</span></p>
                <select name="rooms" class="input" required>
                    <option value="1" selected>1 room</option>
                    <option value="2">2 rooms</option>
                    <option value="3">3 rooms</option>
                    <option value="4">4 rooms</option>
                    <option value="5">5 rooms</option>
                    <option value="6">6 rooms</option>
                </select>
            </div>
            <div class="box">
                <p>Check in <span>*</span></p>
                <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
                <p>Check out <span>*</span></p>
                <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
                <p>Adults <span>*</span></p>
                <select name="adults" class="input" required>
                    <option value="1" selected>1 adult</option>
                    <option value="2">2 adults</option>
                    <option value="3">3 adults</option>
                    <option value="4">4 adults</option>
                    <option value="5">5 adults</option>
                    <option value="6">6 adults</option>
                </select>
            </div>
            <div class="box">
                <p>Childs <span>*</span></p>
                <select name="childs" class="input" required>
                    <option value="0" selected>0 child</option>
                    <option value="1">1 child</option>
                    <option value="2">2 childs</option>
                    <option value="3">3 childs</option>
                    <option value="4">4 childs</option>
                    <option value="5">5 childs</option>
                    <option value="6">6 childs</option>
                </select>
            </div>
        </div>
        <br>
        <br>

        <input type="submit" value="BOOK NOW" name="book" class="btn">
    </form>

</section>




<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">
    <br>
    <br>
    <br>
    <p class="gal">GALLERY</p>
    <br>
    <br>

    <div class="swiper gallery-slider">
        <div class="swiper-wrapper">
            <img src="images/k1.jpg" class="swiper-slide" alt="">
            <img src="images/k3.jpg" class="swiper-slide" alt="">
            <img src="images/k4.jpg" class="swiper-slide" alt="">
            <img src="images/k5.jpg" class="swiper-slide" alt="">
            <img src="images/k6.jpg" class="swiper-slide" alt="">
            <img src="images/k7.jpg" class="swiper-slide" alt="">

        </div>
        <div class="swiper-pagination"></div>
    </div>
    <br>
    <br>
    <br>


</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">
    <br>
    <br>
    <br>
    <p class="cont">CONTACT US</p>
    <br>
    <br>
    <br>
    <br>
    <div class="row">

        <form action="" method="post">
            <h3>send us message</h3>
            <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="box">
            <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="box">
            <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter your number" class="box">
            <textarea name="message" class="box" required maxlength="1000" placeholder="Enter your message" cols="30" rows="10"></textarea>
            <input type="submit" value="Send message" name="send" class="btn">
        </form>

        <div class="faq">
            <h3 class="title">frequently asked questions</h3>
            <div class="box active">
                <h3>How to cancel?</h3>
                <p>To cancel, log in to your account, find your reservation in the "Reservations" section. Contact customer support if needed. Review the cancellation policy for any applicable charges.</p>
            </div>
            <div class="box">
                <h3>Is there any vacancy?</h3>
                <p>To check for vacancy, please contact us directly through phone or email. Availability can vary, so we recommend reaching out to inquire about the dates you are interested in.</p>
            </div>
            <div class="box">
                <h3>What are payment methods?</h3>
                <p>We offer various payment methods for your convenience. Please check our website or contact our customer support for specific payment options available for your booking.</p>
            </div>
            <div class="box">
                <h3>How to claim coupons codes?</h3>
                <p>To claim coupon codes, review terms and conditions.</p>
            </div>
            <div class="box">
                <h3>What are the age requirements?</h3>
                <p>Age requirements may vary depending on the specific services and facilities offered by our establishment. Please refer to our website or contact our customer support for detailed information about age restrictions for accommodations, amenities, and any other activities.</p>
            </div>
        </div>

    </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

    <div class="swiper reviews-slider">
        <br>
        <br>
        <br>
        <br>
        <div class="swiper-wrapper">
            <div class="swiper-slide box">
                <img src="images/cof.jpg" alt="">
                <h3>Masa Abu Aisheh</h3>
                <p>Hello, I'm Masa Abu Aisheh, a co-founder and web designer. Feel free to contact me at masaabuaisheh11@gmail.com for any inquiries or collaborations! </p>
            </div>

            <div class="swiper-slide box">
                <img src="images/cof.jpg" alt="">
                <h3>Nancy Sawaleh</h3>
                <p>Hello, I'm Nancy Sawalmeh, a co-founder and web developer. Feel free to contact me at nancysawalmeh@gmail.com for any inquiries or collaborations!</p>
            </div>


        </div>

        <div class="swiper-pagination"></div>
    </div>

</section>

<!-- reviews section ends  -->

<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <a href="tel:1234567890"><i class="fas fa-phone"></i> +595-948-598</a>
            <a href="tel:1112223333"><i class="fas fa-phone"></i> +566-015-115</a>
            <a href="mailto:shakhanas@gmail.com"><i class="fas fa-envelope"></i> millenniumPalestine@gmail.com</a>
            <a href="https://www.google.com/maps/place/%D9%85%D9%8A%D9%84%D9%86%D9%8A%D9%88%D9%85+%D9%81%D9%84%D8%B3%D8%B7%D9%8A%D9%86+%D8%B1%D8%A7%D9%85+%D8%A7%D9%84%D9%84%D9%87%E2%80%AD/@31.895734,35.2070001,17z/data=!3m1!4b1!4m9!3m8!1s0x1502d55288c0f9cb:0x7508f30fee69f0cb!5m2!4m1!1i2!8m2!3d31.895734!4d35.2048114!16s%2Fm%2F0drzzjz?entry=ttu" target="_blank" ><i class="fas fa-map-marker-alt"></i> Ramallah - Palestine </a>
        </div>

        <div class="box">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#reservation">Reservation</a>
            <a href="#gallery">Gallery</a>
            <a href="#contact">Contact</a>
            <a href="#reviews">Reviews</a>
        </div>

        <div class="box">
            <a href="https://www.facebook.com/MillenniumHotelRamallah"target="_blank">Facebook <i class="fab fa-facebook-f"></i></a>
            <a href="#">Twitter <i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/millennium.hotel.ramallah/"target="_blank">Instagram <i class="fab fa-instagram"></i></a>
        </div>

    </div>


</section>

<!-- footer section ends -->


<!-- masa,nancy -->
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>