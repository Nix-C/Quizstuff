<header id="header">
      <nav>
        <a href="/" class="logo-container">
          <img
            class="ostrich-icon"
            src="/assets/images/ostritch_final_1.png"
            alt="An ostrich wearing glasses."
          />
          <h1>Quizstuff</h1>
        </a>
        <div id="header-links-wrapper">
          <ul id="header-links">
            <!-- <h3 class="mobile-only nav-heading">General</h3> -->
            <li><a href="/">Overview</a></li>
            <li><a href="/downloads.php">Downloads</a></li>
            <li><a href="/faqs.php">FAQ</a></li>
            <!-- <h3 class="mobile-only nav-heading">Store</h3> -->
            <li class="dropdown">
              <button class="dropdown-toggle">
                Store
                <svg
                  width="24px"
                  height="24px"
                  stroke-width="1.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  color="currentColor"
                >
                  <path
                    d="M6 9L12 15L18 9"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  ></path>
                </svg>
              </button>
              <ul class="dropdown-content">
                <!-- <li>
                  <a href="/store/products.php">Products</a>
                </li> -->
                <li>
                  <a href="/store/order-form.php">Order Equipment</a>
                </li>
                <!-- <li>
                  <a href="/store/shipping-info.php">Shipping policy</a>
                </li> -->
              </ul>
              
            </li>
            <?php
                # <li><a href="/store/equipment-registration.php">Equipment Registration</a></li>
             ?>
            <li><a href="/contact.php">Contact Us</a></li>
          </ul>
        </div>
        <button class="icon mobile-only" onclick="toggleNavOpen()">
          <svg
            class="ham-icon"
            viewBox="0 0 100 100"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path style="stroke-miterlimit: 6" d="M 0 20 L 100 20" />
            <path style="stroke-miterlimit: 6" d="M 0 50 L 100 50" />
            <path style="stroke-miterlimit: 6" d="M 0 80 L 100 80" />
          </svg>
        </button>
      </nav>
    </header>