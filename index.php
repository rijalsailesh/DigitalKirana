<?php
require_once 'includes/functions.php';
require_once 'includes/Connection.php';
require_once 'constants/TenantStatus.php';


$connection = ConnectionHelper::getConnection();

if (isPost()) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "select * from user where username = :username";
    $statement = $connection->prepare($query);
    $statement->bindParam('username', $username);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $passwordHash = $result['PasswordHash'];
        if (password_verify($password, $passwordHash)) {

            if ($result['TenantId'] == null) {
                session_start();
                $_SESSION['user'] = $result;
                header('Location: /tenants.php');
            } else {
                $query = "select * from tenants where id = :tenantId";
                $statement = $connection->prepare($query);
                $statement->bindParam('tenantId', $result['TenantId']);
                $statement->execute();
                $tenantResult = $statement->fetch(PDO::FETCH_ASSOC);
                if ($tenantResult['Status'] == TenantStatus::$Pending) {
                    addErrorMessage("Your tenant is still pending");
                    header('Location: /');
                } else if ($tenantResult['Status'] == TenantStatus::$Rejected) {
                    addErrorMessage("Your tenant is rejected");
                    header('Location: /');
                } else {
                    if (!$result['Status']) {
                        addErrorMessage("You are no longer active");
                        header('Location: /');
                    } else {
                        session_start();
                        $_SESSION['user'] = $result;
                        addSuccessMessage("Login Successful");
                        $returnUrl = getParam("returnUrl", "/dashboard.php");
                        header('Location: ' . $returnUrl);
                    }
                }
            }
        } else {
            addErrorMessage("Invalid password");
            header('Location: /');
        }
    } else {
        addErrorMessage("Invalid username");
        header('Location: /');
    }
}
require_once 'includes/header.php';
?>

<!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex align-items-center">

    <div class="container">
        <div class="row">
            <div class="col-lg-6 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200">
                <?php
                if (checkAuth()) :
                ?>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-2 mb-4">
                        <a href="/dashboard.php" class="btn btn-success">🎉 Logged In As <?= getLoggedInUser()['FirstName'] . " " . getLoggedInUser()['LastName'] ?></a>
                    </div>
                <?php
                endif;
                ?>
                <h1>Effortless Inventory Management with Digital Kirana</h1>
                <h2>Transform Your Inventory Management Experience Today</h2>
                <div class="d-flex justify-content-center justify-content-lg-start gap-2">
                    <?php
                    if (checkAuth()) :
                    ?>
                        <a href="/dashboard.php" class="btn-get-started">Dashboard</a>
                        <form action="/logout.php" method="post">
                            <button class="btn-get-started border-0" style="background-color: red;">Log Out</button>
                        </form>
                    <?php
                    else :
                    ?>
                        <a href="/register.php" class="btn-get-started scrollto">Register Here</a>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
                <?php
                if (checkAuth()) :
                ?>
                    <img src="/assets/public-theme/assets/img/hero-img.png" class="img-fluid animated" alt="">
                <?php
                else :
                ?>
                    <div class="card">
                        <form action="" method="post">
                            <div class="card-body p-5">
                                <?php renderMessages(); ?>
                                <div class="mb-3 form-floating">
                                    <input type="text" id="username" class="form-control" name="username" placeholder="Username" value="admin">
                                    <label for="username">Username*</label>
                                </div>
                                <div class="mb-3 form-floating">
                                    <input type="password" id="username" class="form-control" name="password" placeholder="Username" value="admin">
                                    <label for="username" >Password*</label>
                                </div>
                                <div class="text-center">
                                    <button class="btn-get-started border-0 w-100">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
</section><!-- End Hero -->

<main id="main">
    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
        <div class="container">

            <div class="section-title">
                <h2>About Us</h2>
            </div>

            <div class="row content" data-aos="fade-down">
                <div class="col-lg-6">
                    <p>
                        Welcome to Digital Kirana, the ultimate inventory management system designed to simplify and streamline your inventory processes. Whether you own a small retail store or manage a large warehouse, our platform is here to help you take control of your inventory like never before.
                    </p>
                    <ul>
                        <li><i class="ri-check-double-line"></i> Say goodbye to endless spreadsheets and tedious data entry
                        </li>
                        <li><i class="ri-check-double-line"></i>
                            Experience the convenience of seamless integration with barcode scanners, POS systems, and other hardware devices.
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0">
                    <p>
                        At Digital Kirana, we understand the unique challenges of inventory management. That's why we offer robust customer support, ensuring that you have assistance every step of the way. Our team is dedicated to your success, helping you maximize efficiency, reduce costs, and boost profitability.
                    </p>
                    <p>
                        Discover the power of Digital Kirana today and unlock the full potential of your inventory!
                    </p>
                </div>
            </div>

        </div>
    </section><!-- End About Us Section -->

    <!-- ======= Why Us Section ======= -->
    <section id="why-us" class="why-us section-bg">
        <div class="container-fluid" data-aos="fade-up">

            <div class="row">

                <div class="col-lg-7 d-flex flex-column justify-content-center align-items-stretch  order-2 order-lg-1">

                    <div class="content">
                        <h3>FAQ (Frequently Asked Questions)</h3>
                        <p>
                            Still have questions? Check out our frequently asked questions below.
                        </p>
                    </div>

                    <div class="accordion-list">
                        <ul>
                            <li>
                                <a data-bs-toggle="collapse" class="collapse" data-bs-target="#accordion-list-1"><span>01</span> What is Digital Kirana?
                                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                                <div id="accordion-list-1" class="collapse show" data-bs-parent=".accordion-list">
                                    <p>
                                        Digital Kirana is an advanced inventory management system that provides businesses with powerful tools to simplify and streamline their inventory processes. It offers features such as real-time tracking, barcode integration, intelligent forecasting, and comprehensive reporting.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <a data-bs-toggle="collapse" data-bs-target="#accordion-list-2" class="collapsed"><span>02</span>
                                    Who can benefit from Digital Kirana? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                                <div id="accordion-list-2" class="collapse" data-bs-parent=".accordion-list">
                                    <p>
                                        Digital Kirana caters to a wide range of businesses, including retail stores, e-commerce platforms, warehouses, and distribution centers. Whether you're a small business owner or managing a large-scale operation, Digital Kirana can help you optimize your inventory management.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <a data-bs-toggle="collapse" data-bs-target="#accordion-list-3" class="collapsed"><span>03</span>
                                    How does Digital Kirana work?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                                <div id="accordion-list-3" class="collapse" data-bs-parent=".accordion-list">
                                    <p>
                                        Digital Kirana works by centralizing your inventory data and providing real-time visibility into stock levels, orders, and sales. It allows you to track incoming shipments, manage multiple warehouses, automate stock replenishment, and generate detailed reports for better decision-making.
                                    </p>
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>

                <div class="col-lg-5 align-items-stretch order-1 order-lg-2 img" style='background-image: url("/assets/public-theme/assets/img/why-us.png");' data-aos="zoom-in" data-aos-delay="150">&nbsp;</div>
            </div>

        </div>
    </section><!-- End Why Us Section -->


    <!-- ======= Services Section ======= -->
    <section id="services" class="services section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h2>Why Digital Kirana</h2>
                <p>Simplify Inventory Management with Digital Kirana</p>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-box text-center">
                        <div class="icon"><i class="bx bxl-dribbble"></i></div>
                        <h4><a href="#">Simple and Rich</a></h4>
                        <p>Streamline your inventory management with a user-friendly interface and comprehensive features.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-box text-center">
                        <div class="icon"><i class="bx bx-file"></i></div>
                        <h4><a href="#">Easy Customizable</a></h4>
                        <p>Tailor Digital Kirana to your specific business needs with customizable settings and configurations.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-xl-0" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-box text-center">
                        <div class="icon"><i class="bx bx-tachometer"></i></div>
                        <h4><a href="#">Fast and scalable</a></h4>
                        <p>Enjoy high-performance and scalability to handle your growing inventory demands efficiently.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 d-flex align-items-stretch mt-4 mt-xl-0" data-aos="zoom-in" data-aos-delay="400">
                    <div class="icon-box text-center">
                        <div class="icon"><i class="bx bx-layer"></i></div>
                        <h4><a href="#">Excellent Support</a></h4>
                        <p>Our dedicated support team is here to assist you every step of the way, ensuring your success with Digital Kirana.</p>
                    </div>
                </div>

            </div>

        </div>
    </section><!-- End Services Section -->
</main><!-- End #main -->

<?php
require_once 'includes/footer.php';
?>