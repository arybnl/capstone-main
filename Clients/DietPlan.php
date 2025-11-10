<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diet Plan</title>
  <link rel="icon" type="img/t3-logo.png" href="img/t3-logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="DietPlan.css">
  <link rel="stylesheet" href="SideBar.css">
</head>
<body class="body overflow-auto">

  <!-- Toggle button -->
     <button class="btn btn-danger d-md-none" id="menuToggle" style="margin:10px;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Side Bar -->
    <?php include 'Client_sidebar.php'; ?>

    <!------------------------------- Main Content for DIET PLAN -------------------------------->

  <div class="content-wrapper"> 
    <div class="main-container">
      <!-- RECOMMENDED DIET PLAN LIST CONTAINER -->
  <div class="section">
    <div class="header-text">
      <span class="highlight-red">RECOMMENDED</span>
      <span class="highlight-white">DIET PLAN</span>
    </div>
    <div class="content-box">
      <div class="row g-3 justify-content-center">
        
        <!-- BREAKFAST -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#breakfastModal">
            <img src="img/breakfast.png" class="card-img" alt="Breakfast">
            <div class="overlay">View List</div>
            <div class="card-title text-center">BREAKFAST</div>
            <p class="card-text">Start your morning with a healthy boost of energy. Nutritious choices help you stay focused and active all day.</p>
          </div>
        </div>

        <!-- LUNCH -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#lunchModal">
            <img src="img/lunch.png" class="card-img" alt="Lunch">
            <div class="overlay">View List</div>
            <div class="card-title text-center">LUNCH</div>
            <p class="card-text">Recharge with a hearty and balanced meal. It keeps your energy steady through the busiest hours.</p>
          </div>
        </div>

        <!-- SNACK -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#snackModal">
            <img src="img/snack.png" class="card-img" alt="Snack">
            <div class="overlay">View List</div>
            <div class="card-title text-center">SNACK</div>
            <p class="card-text">Enjoy light bites that keep hunger away. Perfect for a quick boost without the guilt.</p>
          </div>
        </div>

        <!-- DINNER -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#dinnerModal">
            <img src="img/dinner.jpg" class="card-img" alt="Dinner">
            <div class="overlay">View List</div>
            <div class="card-title text-center">DINNER</div>
            <p class="card-text">End your day with a nourishing meal. Easy to digest and great for overnight recovery.</p>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- COACH CHAD'S IMAGE LIST CONTAINERS -->
  <div class="section">
    <div class="header-text">
      <span class="highlight-red">COACH CHAD'S</span>
      <span class="highlight-white">DIET PLAN</span>
    </div>
    <div class="content-box">
      <div class="row g-3 justify-content-center">

        <!-- BREAKFAST -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#coachModal">
            <img src="img/breakfast.png" class="card-img" alt="Breakfast">
            <div class="overlay">View List</div>
            <div class="card-title text-center">BREAKFAST</div>
          </div>
        </div>

        <!-- LUNCH -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#coachModal">
            <img src="img/lunch.png" class="card-img" alt="Lunch">
            <div class="overlay">View List</div>
            <div class="card-title text-center">LUNCH</div>
          </div>
        </div>

        <!-- SNACK -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#coachModal">
            <img src="img/snack.png" class="card-img" alt="Snack">
            <div class="overlay">View List</div>
            <div class="card-title text-center">SNACK</div>
          </div>
        </div>

        <!-- DINNER -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="diet-card" data-bs-toggle="modal" data-bs-target="#coachModal">
            <img src="img/dinner.jpg" class="card-img" alt="Dinner">
            <div class="overlay">View List</div>
            <div class="card-title text-center">DINNER</div>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>
  </div>
  

<!-- ==================== MODALS ==================== -->

<!-- Breakfast Modal -->
<div class="modal fade" id="breakfastModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content diet-modal">
      <div class="modal-header border-0">
        <h5 class="text-white">Breakfast</h5>
        <button type="button" class="btn-close reco-btn" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-white">
        <p>- Oatmeal with fruits</p>
        <p>- Boiled eggs</p>
      </div>
    </div>
  </div>
</div>

<!-- Lunch Modal -->
<div class="modal fade" id="lunchModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content diet-modal">
      <div class="modal-header border-0">
        <h5 class="text-white">Lunch</h5>
        <button type="button" class="btn-close reco-btn" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-white">
        <p>- Grilled chicken with vegetables</p>
      </div>
    </div>
  </div>
</div>

<!-- Snack Modal -->
<div class="modal fade" id="snackModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content diet-modal">
      <div class="modal-header border-0">
        <h5 class="text-white">Snack</h5>
        <button type="button" class="btn-close reco-btn" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-white">
        <p>- Yogurt with nuts</p>
      </div>
    </div>
  </div>
</div>

<!-- Dinner Modal -->
<div class="modal fade" id="dinnerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content diet-modal">
      <div class="modal-header border-0">
        <h5 class="text-white">Dinner</h5>
        <button type="button" class="btn-close reco-btn" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-white">
        <p>- Baked salmon with rice</p>
      </div>
    </div>
  </div>
</div>

<!-- Coach Chad's Modal --> 
 <div class="modal fade" id="coachModal" tabindex="-1"> 
  <div class="modal-dialog modal-xl modal-dialog-centered"> 
    <div class="modal-content coach-modal"> 
      <div class="modal-header border-0"> 
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
       </div> <div class="modal-body row"> 
        <div class="col-md-4 text-white p-4 coach-left"> 
          <h4 class="fw-bold coach-header">COACH CHAD'S LIST</h4>
           <hr> 
           <div class="text-center mt-4"> 
            <h2 class="fw-bold fst-italic">START YOUR</h2> 
            <h2 class="fw-bold highlight-red fst-italic">PLAN</h2> 
            <h2 class="fw-bold fst-italic">TODAY!</h2> 
          </div> 
        </div> 
        <div class="col-md-8 p-4"> 
          <div class="coach-box"> 
            <ul class="nav nav-tabs" id="coachTab" role="tablist"> 
              <li class="nav-item">
                <a class="nav-link active text-white" data-bs-toggle="tab" href="#coach-breakfast">Breakfast</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link text-white" data-bs-toggle="tab" href="#coach-lunch">Lunch</a>
              </li> 
                <li class="nav-item">
                  <a class="nav-link text-white" data-bs-toggle="tab" href="#coach-snacks">Snacks</a>
                </li> 
                <li class="nav-item">
                  <a class="nav-link text-white" data-bs-toggle="tab" href="#coach-dinner">Dinner</a>
                </li> 
              </ul> 
              <div class="tab-content text-white mt-3"> 
                <div id="coach-breakfast" class="tab-pane fade show active"> 
                  <p>- Protein pancakes</p> 
                </div> 
                <div id="coach-lunch" class="tab-pane fade"> 
                  <p>- Grilled steak with greens</p> 
                </div> 
                <div id="coach-snacks" class="tab-pane fade"> <p>- Protein shake</p> 
                </div> 
                <div id="coach-dinner" class="tab-pane fade"> 
                  <p>- Salmon with sweet potato</p> 
                </div> 
              </div> 
            </div> 
          </div> 
        </div> 
      </div> 
    </div> 
  </div>

<script src="Sidebar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
