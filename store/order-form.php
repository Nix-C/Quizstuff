<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="shortcut icon"
      href="/assets/images/ostritch_final_1.png"
      type="image/x-icon"
    />
    <link rel="stylesheet" href="/style.css" />
    <script src="/script.js" defer></script>
    <title>QuizStuff</title>
  </head>

  <body>
    <div id="canvas">
      <div id="radial-1"></div>
      <div id="radial-2"></div>
    </div>
    <header id="header">
      <nav>
        <a href="/" class="logo-container">
          <img
            class="ostrich-icon"
            src="/assets/images/ostritch_final_1.png"
            alt="An ostrich wearing glasses."
          />
          <h1>QuizStuff</h1>
        </a>
        <div id="header-links-wrapper">
          <ul id="header-links">
            <li><a href="/">Overview</a></li>
            <li><a href="/downloads.html">Downloads</a></li>
            <!-- <li><a href="#">Changelog</a></li> -->
            <li><a href="/faqs.html">FAQs</a></li>
            <li class="dropdown">
              <button class="current" onclick="toggleOpen(this)">
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
                <li>
                  <a href="/store/products.php">Products</a>
                </li>
                <li>
                  <a class="current" href="/store/order-form.php">Order form</a>
                </li>
                <li>
                  <a href="/store/shipping-info.php">Shipping policy</a>
                </li>
              </ul>
            </li>
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

    <main>
    <h1>Order Form</h1>
      <section class="container">
        <form>
          <button>Submit</button>
        </form>
      </section>
    </main>

    <footer>
      <div class="footer-links">
        <h1>Church of The Nazarene Links</h1>
        <a
          href="https://nazarene.org/who-we-are/organization/ministries/nazarene-youth-international"
          target="_blank"
          >Nazarene Youth International</a
        >

        <a
          href="http://www.nazquizzing.org/pages/37-tournament-web-sites"
          target="_blank"
          >Nazarene Quizzing Tournaments</a
        >
      </div>
      <img
        src="/assets/images/Ostrich 1.jpg"
        alt="A crazy ostrich with a jetpack!"
      />
      <small>© 2008-<span class="current-year">2023</span> QuizStuff</small>
    </footer>
  </body>
</html>
