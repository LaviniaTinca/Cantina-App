 <div class="about-us-container" id="about-us">
   <!-- <div class="banner">
     <h1 class=" main-page-title">About Us</h1>

   </div> -->
   <h1 class=" main-page-title">Despre noi</h1>

   <!-- <img src="images/staff.webp" alt="Our Team" class="about-us-img"> -->
   <img src="public/cantina/IMG-20230808-WA0045.jpg" alt="Our Team" class="about-us-img">

   <p class="about-us-content">
     Suntem o echipă unită de bucătari pasionați, dedicați să aducem experiențe culinare de neuitat pentru fiecare client. Cu o bogată experiență în arta culinară și o dragoste profundă pentru ingrediente proaspete și autentice, ne străduim să punem în valoare aromele și gusturile autentice ale fiecărui preparat. Fiecare membru al echipei noastre aduce un talent unic și creativitate în bucătărie, asigurându-ne că fiecare masă servită este o operă de artă culinară. Ne mândrim cu profesionalismul și atenția noastră la detalii, creând astfel experiențe gastronomice inedite, care să încânte toate simțurile clienților noștri.
   </p>

   <p class="about-us-content">
     Cu o grijă deosebită pentru toate preferințele alimentare, echipa noastră de bucătari este pregătită să facă față tuturor cerințelor dietetice, inclusiv zilele de post și sărbătorile speciale. Cu o abordare creativă și inspirată, pregătim mâncăruri delicioase și sănătoase pentru a sărbători fiecare ocazie cu bucurie și gust. Fie că este vorba despre preparate vegane și vegetariene pentru zilele de post sau rețete tradiționale adaptate pentru sărbători, ne asigurăm că fiecare masă este plină de savoare și iubire.
   </p>

   <p class="about-us-contact">
     Vino și savurează <a href="view_menu.php"><strong> un prânz delicios!</strong></a>.
   </p>
   <div class="gallery">
     <div class="small-slider">
       <!-- <div><img src="public/cantina/IMG-20230808-WA0032.jpg" alt="Image 1"></div>
       <div><img src="public/cantina/IMG-20230808-WA0059.jpg" alt="Image 2"></div>
       <div><img src="public/cantina/IMG-20230808-WA0073.jpg" alt="Image 2"></div> -->
       <!-- <div><img src="public/cantina/IMG-20230808-WA0008.jpg" alt="Image 2"></div>
       <div><img src="public/cantina/IMG-20230808-WA0045.jpg" alt="Image 2"></div> -->
       <!-- Adaugă mai multe imagini aici -->
     </div>

   </div>
   <div class="lightbox"></div>
 </div>

 </div>


 <script>
   $(document).ready(function() {
     $('.small-slider').slick({
       dots: false,
       infinite: true,
       speed: 1000,
       slidesToShow: 5,
       slidesToScroll: 1,
       autoplay: true,
       autoplaySpeed: 3000,
       //  asNavFor: '.scrollbar-slider' // Link to the scrollbar slider
     });

   });
 </script>


 <script>
   $(document).ready(function() {
     $('.about-us-img').on('click', function() {
       var imgSrc = $(this).attr('src');
       $('.lightbox').html('<img src="' + imgSrc + '">').fadeIn();

       // Center the lightbox vertically
       var windowHeight = $(window).height();
       var lightboxHeight = $('.lightbox img').height();
       var topMargin = (windowHeight - lightboxHeight) / 2;
       $('.lightbox img').css('margin-top', topMargin + 'px');
     });

     $('.lightbox').on('click', function() {
       $(this).fadeOut();

     });
   });
 </script>
 <script>
   $(document).ready(function() {
     $('.about-us-img').on('click', function() {
       var imgSrc = $(this).attr('src');
       //  $('.lightbox img').attr('src', imgSrc); // Set the source of the lightbox image
       //  $('.lightbox').fadeIn(); // Show the lightbox
       $('.lightbox').html('<img src="' + imgSrc + '">').fadeIn();

     });

     $('.lightbox').on('click', function() {
       $(this).fadeOut(); // Hide the lightbox on click
     });
   });
 </script>