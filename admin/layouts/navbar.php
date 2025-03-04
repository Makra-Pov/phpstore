<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <!-- Hamburger Icon for Sidebar Toggle -->
        <button class="navbar-toggler d-md-none text-dark" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon text-dark"></span>
        </button>


        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                    <?php echo isset($page_title) ? htmlspecialchars($page_title) : "Dashboard"; ?>
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0">
                <?php echo isset($page_title) ? htmlspecialchars($page_title) : "Dashboard"; ?>
            </h6>
        </nav>

        <!-- Collapsible Navbar -->
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <!-- Search Box -->
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div>
            </div>
            <!-- Right Side Links -->
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])) { ?>
                <!-- Logged in: show profile icon -->
                <li class="nav-item d-flex align-items-center">
                    <a href="index.php?route=profile" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none"> <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </a>
                </li>
                <!-- Logout link -->
                <li class="nav-item d-flex align-items-center">
                    <a href="index.php?route=logout" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-sign-out-alt me-sm-1"></i>
                        <span class="d-sm-inline d-none">Logout</span>
                    </a>
                </li>
                <?php } else { ?>
                <!-- Not logged in: show sign-in link triggering a modal -->
                <li class="nav-item d-flex align-items-center">
                    <a href="javascript:void(0);" class="nav-link text-body font-weight-bold px-0"
                        data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">Sign In</span>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navbarToggler = document.querySelector(".navbar-toggler");
    const navbarCollapse = document.getElementById("navbar");

    navbarToggler.addEventListener("click", function() {
        navbarCollapse.classList.toggle("show");
    });
});
</script>