<div class="slider">
  <div class="slideshow-container">

    <div class="mySlides fade">
      <img src="public/cantina/20230807_113932.jpg" style="width:100%; object-fit: cover" alt="slider image1">
    </div>
    <div class="mySlides fade">
      <img src="public/cantina/20230807_124031.png" style="width:100%; object-fit: cover" alt="slider image1">
    </div>
    <div class="mySlides fade">
      <img src="public/cantina/20230807_113724.jpg" style="width:100%; object-fit: cover" alt="slider image1">
    </div>

    <!-- <div class="mySlides fade">
      <img src="https://images.creativemarket.com/0.1.0/ps/10261382/910/624/m2/fpnw/wm1/q4qzpc6b9aocfot3kozuv7bqcnwuvqyggby8cevpqvni6piq5z0setfb4qveocng-.jpg?1619466669&s=77d3a93af701cb1effc3106b3a6c64cc&fmt=webp" style="width:100%" alt="slider image1">
      <div class="text">Caption Text</div>
    </div> -->

    <div class="mySlides fade">
      <img src="https://images.creativemarket.com/0.1.0/ps/10059367/910/603/m2/fpnw/wm1/new_08.03.2021_72-.jpg?1616097447&s=cf895db741fa49d903303092c6385c6d&fmt=webp" style="width:100%" alt="slider image2">
    </div>
    <!-- 
    <div class="mySlides fade">
      <img src="public/cantina/20230807_112940.jpg" style="width:100%; object-fit: cover" alt="slider image3">
    </div> -->
    <div class="mySlides fade">
      <img src="public/cantina/20230807_114003c.jpg" style="width:100%; object-fit: cover" alt="slider image3">
    </div>

    <!-- <div class="mySlides fade">
      <img src="public/cantina/20230807_113121.jpg" style="width:100%; object-fit: cover" alt="slider image3">
    </div> -->
    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>

  </div>
  <br><br>
  <!-- POINTS -->
  <!-- <div style="text-align:center">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>

    <span class="dot" onclick="currentSlide(6)"></span>
    <span class="dot" onclick="currentSlide(7)"></span>
    <span class="dot" onclick="currentSlide(8)"></span>
    <span class="dot" onclick="currentSlide(9)"></span>
  </div> -->
</div>
<script>
  var slideIndex = 1;
  showSlides(slideIndex);

  // Auto play slides
  setInterval(function() {
    slideIndex++;
    showSlides(slideIndex);
  }, 3000); // Change slide every 3 seconds

  function plusSlides(n) {
    showSlides(slideIndex += n);
  }

  function currentSlide(n) {
    showSlides(slideIndex = n);
  }

  function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    // var dots = document.getElementsByClassName("dot");
    if (n > slides.length) {
      slideIndex = 1;
    }
    if (n < 1) {
      slideIndex = slides.length;
    }
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }
    // for (i = 0; i < dots.length; i++) {
    //   dots[i].className = dots[i].className.replace(" active", "");
    // }
    slides[slideIndex - 1].style.display = "block";
    // dots[slideIndex - 1].className += " active";
  }
</script>