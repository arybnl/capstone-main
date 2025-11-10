<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recommended Videos</title>
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="RecoVid.css">
  <link rel="stylesheet" href="SideBar.css">
</head>

<body class="body">

  <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

  <!--------------------------- Main Container for RECOMMENDED DIET PLANS ------------------------->

  <div class="content-wrapper">
    <div class="main-container">
      <!-- Top Section -->
  <div class="section">
    <div class="header-text">
      <span class="highlight-red">FULL BODY</span>
      <span class="highlight-white">WORKOUT</span>
    </div>
    <div class="content-box">
      <div id="carouselTop" class="carousel slide w-100 h-100" data-bs-ride="carousel">
        <div class="carousel-inner h-100">
          <div class="carousel-item active h-100 text-center">
            <img src="img/top01.jpg" class="d-block mx-auto h-100">
          </div>
          <div class="carousel-item h-100 text-center">
            <img src="img/top02.jpg" class="d-block mx-auto h-100">
          </div>
          <div class="carousel-item h-100 text-center">
            <img src="img/top3.jpg" class="d-block mx-auto h-100">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTop" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselTop" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </div>

  <!-- Bottom Section -->
  <div class="section">
    <div class="header-text">
      <span class="highlight-red">BODY PARTS</span>
      <span class="highlight-white">WORKOUT</span>
    </div>
    <div class="content-box">
      <div id="carouselBottom" class="carousel slide w-100 h-100" data-bs-ride="carousel">
        <div class="carousel-inner h-100">
          <div class="carousel-item active h-100 text-center">
            <img src="img/bottom01.jpg" class="d-block mx-auto h-100">
          </div>
          <div class="carousel-item h-100 text-center">
            <img src="img/bottom02.jpg" class="d-block mx-auto h-100">
          </div>
          <div class="carousel-item h-100 text-center">
            <img src="img/bottom03.jpg" class="d-block mx-auto h-100">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselBottom" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselBottom" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </div>
    </div>
  </div>

 <script src="Sidebar.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
