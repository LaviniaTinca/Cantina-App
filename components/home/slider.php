<div class="slider">
  <div class="slideshow-container">

    <div class="mySlides fade">
      <img src="../public/images/20230807_113932.jpg" style="width:100%; object-fit: cover" alt="slider image1">
    </div>
    <div class="mySlides fade">
      <img src="../public/images/20230807_124031.png" style="width:100%; object-fit: cover" alt="slider image2">
    </div>
    <div class="mySlides fade">
      <img src="../public/images/20230807_113724.jpg" style="width:100%; object-fit: cover" alt="slider image3">
    </div>
    <div class="mySlides fade">
      <img src="../public/images/20230807_114003c.jpg" style="width:100%; object-fit: cover" alt="slider image4">
    </div>
    <div class="mySlides fade">
      <img src="../public/images/new_08.03.2021_72-.webp" style="width:100%" alt="slider image5">
    </div>
    <div class="mySlides fade">
      <img src="../public/images/new_image-.webp" style="width:100%" alt="slider image6">
    </div>
    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>

  </div>
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
    if (n > slides.length) {
      slideIndex = 1;
    }
    if (n < 1) {
      slideIndex = slides.length;
    }
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }
    slides[slideIndex - 1].style.display = "block";
  }
</script>