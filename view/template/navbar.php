<nav
  class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div
    class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button
        class="navbar-toggler navbar-toggler align-self-center"
        type="button"
        data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <div class="d-flex justify-content-center">
        <a class="navbar-brand brand-logo">
          <img src="view/img/buildings.svg" alt="" class="img-fluid">
        </a>
      </div>
      <div class="d-flex justify-content-center">
        <a class="navbar-brand brand-logo-mini" href="../index.html">
          <img src="view/img/buildings.svg" alt="" class="img-fluid">
        </a>
      </div>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-top">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text">
          <span id="greeting"></span>, <span class="text-black fw-bold"><?= $_SESSION['username'] ?></span>
        </h1>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a
          class="nav-link"
          id="UserDropdown"
          href="#"
          data-bs-toggle="dropdown"
          aria-expanded="false">
          <img
            class="img-xs rounded-circle"
            src="view/img/person-circle.svg"
            alt="Profile image" />
        </a>
        <div
          class="dropdown-menu dropdown-menu-right navbar-dropdown"
          aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <div class="img-md rounded-circle fs-2">
              <i class="bi bi-person-circle"></i>
            </div>
            <p class="mb-1 mt-3 font-weight-semibold"><?= $_SESSION['username'] ?></p>
            <p class="fw-light text-muted mb-0"><?= $_SESSION['email'] ?></p>
          </div>
          <a class="dropdown-item" href="index.php?page=logout">
            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out
          </a>
        </div>
      </li>
    </ul>
    <button
      class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
      type="button"
      data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
<script>
  const greetingElement = document.getElementById("greeting");
  const now = new Date();
  const hour = now.getHours();
  let greetingText = "Hello";

  if (hour >= 4 && hour < 11) {
    greetingText = "Good Morning";
  } else if (hour >= 11 && hour < 15) {
    greetingText = "Good Afternoon";
  } else if (hour >= 15 && hour < 18) {
    greetingText = "Good Evening";
  } else {
    greetingText = "Good Night";
  }

  greetingElement.innerText = greetingText;
</script>