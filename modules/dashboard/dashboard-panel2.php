<!-- Container-Fitting Bootstrap Carousel Banner (Happy Faces) -->
<div id="happyFacesCarousel" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel">
  <div class="carousel-inner rounded-3 shadow-sm" style="overflow: hidden;">
    <div class="carousel-item active">
      <a href="https://unsplash.com/photos/Yr4n8O_3UPc" target="_blank">
        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=1600&q=80"
          class="d-block w-100"
          alt="Happy Face 1"
          style="object-fit: cover; height: 280px;">
      </a>
    </div>
    <div class="carousel-item">
      <a href="https://unsplash.com/photos/6anudmpILw4" target="_blank">
        <img src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=1600&q=80"
          class="d-block w-100"
          alt="Happy Face 2"
          style="object-fit: cover; height: 280px;">
      </a>
    </div>
    <div class="carousel-item">
      <a href="https://unsplash.com/photos/8manzosRGPE" target="_blank">
        <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=1600&q=80"
          class="d-block w-100"
          alt="Happy Face 3"
          style="object-fit: cover; height: 280px;">
      </a>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#happyFacesCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#happyFacesCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<script>
  var myCarousel = document.querySelector('#happyFacesCarousel');
  var carousel = new bootstrap.Carousel(myCarousel, {
    interval: 2500,
    ride: 'carousel'
  });
</script>


<?php include 'dashboard-whats-up-widget.php'; ?>


