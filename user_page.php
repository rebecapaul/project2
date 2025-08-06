<?php
 include("bar.php");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Matukio ya Wananchi</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(to right, #dbeafe, #e0e7ff);
    }

    .service {
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 100vh;
      padding: 20px 9%;
      text-align: center;
    }

    .service .heading {
      font-size: 40px;
      margin-bottom: 30px;
      color: #1e3a8a;
      font-weight: 700;
    }

    .service .wrapper {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
    }

    .wrapper .box {
      padding: 30px 20px;
      background: #ffffff;
      border-radius: 12px;
      transition: 0.4s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .wrapper .box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .box i {
      font-size: 60px;
      color: #3b82f6;
    }

    .box h3 {
      font-size: 22px;
      margin-top: 15px;
      color: #1e3a8a;
    }

    .box p {
      margin: 12px 0 20px;
      font-size: 15px;
      color: #555;
    }

    .box .btn {
      display: inline-block;
      padding: 10px 20px;
      background: #3b82f6;
      border-radius: 6px;
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.3s;
    }

    .box .btn:hover {
      background: #1d4ed8;
    }

    .problem-section {
      background: #ffffff;
      padding: 60px 9%;
    }

    .problem-section h2 {
      font-size: 30px;
      margin-bottom: 20px;
      color: #1e3a8a;
    }

    .problem-section form {
      max-width: 600px;
      margin: auto;
      display: flex;
      flex-direction: column;
      background: #f9fafb;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .problem-section input,
    .problem-section textarea {
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 16px;
    }

    .problem-section button {
      padding: 12px;
      background: #3b82f6;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .problem-section button:hover {
      background: #2563eb;
    }

    #detailsSection {
      display: none;
      padding: 60px 9%;
      background: #fff;
    }

    #detailsSection h2 {
      font-size: 30px;
      margin-bottom: 20px;
      color: #1e3a8a;
    }

    #detailsSection p {
      font-size: 18px;
      line-height: 1.7;
      color: #333;
    }

    .logout-btn {
      display: block;
      text-align: right;
      padding: 10px 9%;
      font-weight: bold;
      color: #3b82f6;
      text-decoration: none;
    }
  .hero-slider {
  position: relative;
  height: 70vh;        /* Kupunguza kutoka 70vh kwenda 50vh */
  min-height: 300px;   /* Kupunguza kutoka 450px kwenda 300px */
  color: #fff;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-family: 'Poppins', sans-serif;
  padding: 0 20px;     /* Optional: kuongeza padding kidogo kwa maudhui ya ndani */
}


.slides {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  display: flex;
  transition: transform 1s ease-in-out;
}

.slide {
  min-width: 100%;
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
  opacity: 0;
  transition: opacity 1s ease-in-out;
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
}

.slide.active {
  opacity: 1;
  position: relative;
  z-index: 1;
}

.hero-text {
  position: relative;
  z-index: 2;
  max-width: 700px;
  padding: 0 20px;
}

.hero-text h1 {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 15px;
  text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
}

.hero-text p {
  font-size: 1.3rem;
  margin-bottom: 25px;
  text-shadow: 1px 1px 6px rgba(0,0,0,0.6);
}

.btn-hero {
  background-color: #3b82f6;
  color: white;
  padding: 12px 30px;
  font-weight: 600;
  border-radius: 6px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.btn-hero:hover {
  background-color: #2563eb;
  color: #fff;
}

    .logout-btn:hover {
      text-
      decoration: underline;
    }
  </style>
</head>
<body>
  <section class="hero-slider">
  <div class="slides">
    <div class="slide active" style="background-image: url('images/Image_fx (1).jpg');"></div>
    <div class="slide" style="background-image: url('images/Image_fx.jpg');"></div>
    <div class="slide" style="background-image: url('images/Image_fx (2).jpg');"></div>
        <div class="slide" style="background-image: url('images/Image_fx (3).jpg');"></div>

  </div>
  <div class="hero-text">
    <h1>Karibu kwenye Tovuti ya Matukio ya Wananchi</h1>
    <p>Gundua, shiriki, na chukua hatua kwenye matukio muhimu ya kijamii.</p>
    <a href="#service" class="btn-hero">Anza Kuchunguza</a>
  </div>
</section>


  <section class="service" id ='service'>
    <h1 class="heading">Matukio ya Wananchi</h1>
    <div class="wrapper">
      <div class="box">
        <i class="bx bx-heart"></i>
        <h3>Harusi</h3>
        <p>Sherehekea ndoa za kijamii na tazama ratiba ya sherehe na mapokezi.</p>
        <a href="marriage.php" class="btn">Angalia Maelezo</a>
      </div>

      <div class="box">
        <i class="bx bx-cube-alt"></i>
        <h3>Msiba</h3>
        <p>Pata taarifa kuhusu huduma za mazishi na kumbukumbu.</p>
        <a href="funeral.php" class="btn">Angalia Maelezo</a>
      </div>

      <div class="box">
        <i class="bx bx-group"></i>
        <h3>Mikutano ya Jamii</h3>
        <p>Shiriki katika maamuzi ya kijamii—angalia tarehe na ajenda za mikutano ijayo.</p>
        <a href="meetings.php" class="btn">Jiunge</a>
      </div>

      <div class="box">
        <i class="bx bx-calendar-event"></i>
        <h3>Sherehe za Utamaduni</h3>
        <p>Tambua tamasha, maonyesho, na burudani mbalimbali za kitamaduni.</p>
        <a href="cultural.php" class="btn">Gundua</a>
      </div>

      <div class="box">
        <i class="bx bx-basketball"></i>
        <h3>Mashindano ya Michezo</h3>
        <p>Pata taarifa kuhusu mashindano na jiandikishe au shiriki kuunga mkono timu zako.</p>
        <a href="michezo.php" class="btn">Pata Maelezo</a>
      </div>

      <div class="box">
        <i class="bx bx-leaf"></i>
        <h3>Kampeni za Mazingira</h3>
        <p>Jiunge na harakati kama upandaji miti, usafi wa mazingira, na mafunzo ya mazingira.</p>
        <a href="view.php" class="btn">Shiriki</a>
      </div>
    </div>
  </section>

  <!-- Sehemu ya Kuripoti Matatizo -->
  <section class="problem-section">
    <h2>Ripoti Tatizo</h2>
    <form action="submit_problem.php" method="POST">
      <input type="text" name="citizen_name" placeholder="Jina Lako" required />
      <input type="email" name="citizen_email" placeholder="Barua Pepe Yako" required />
      <textarea name="problem_description" placeholder="Eleza tatizo lako..." required rows="5"></textarea>
      <button type="submit">Tuma Ripoti</button>
    </form>
  </section>

  <!-- Maelezo ya Tukio -->
  <section id="detailsSection">
    <h2 id="detailsTitle"></h2>
    <p id="detailsContent"></p>
  </section>

  
    <script>
  const slides = document.querySelectorAll('.slide');
  let currentIndex = 0;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    showSlide(currentIndex);
  }

  // Start slider
  setInterval(nextSlide, 5000); // change slide every 5 seconds


    function showDetails(title, content) {
      document.getElementById('detailsTitle').innerText = title;
      document.getElementById('detailsContent').innerHTML = content;
      document.getElementById('detailsSection').style.display = 'block';
      document.getElementById('detailsSection').scrollIntoView({ behavior: 'smooth' });
    }
  </script>
</body>
</html>
