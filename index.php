<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Nyaralós fotókat megtekintő weboldal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

  <style>
    body { background-color: #f8f9fa; transition: background-color 0.3s; }
    @media (prefers-color-scheme: dark) { body { background-color: #121212; color: #f8f9fa; } }

    #map { height: 600px; border-radius: 10px; }
    #gallery img { width: 100%; border-radius: 8px; cursor: pointer; transition: transform 0.2s; }
    #gallery img:hover { transform: scale(1.05); }
    #gallery .col-6, #gallery .col-md-4 { display: flex; justify-content: center; }
    h1 { font-family: 'Dancing Script', cursive; font-size: 50px; padding-bottom: 3%; }
  </style>
</head>
<body>
  <div class="container py-4">
    <h1 class="text-center mb-4">Válassz a térképen egy helyet az ott készült fotók vagy videók megtekintéséhez!</h1>

    <div class="row justify-content-center mb-4">
      <div class="col-12 col-md-10 col-lg-10">
        <div id="map"></div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-10">
        <div id="gallery" class="row g-3"></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

  <script>
    const map = L.map('map').setView([25.0, 15.0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const cities = [
      {name: "Róma", coords: [41.9028, 12.4964], folder: "rome"},
      {name: "Marsa Alam", coords: [25.0670, 34.8830], folder: "marsa_alam"},
      {name: "Tunézia", coords: [33.8869, 9.5375], folder: "tuneziai"}
    ];

    const gallery = document.getElementById("gallery");
    let lightbox = null;

    function initLightbox() {
      if(lightbox) lightbox.destroy();
      lightbox = GLightbox({ selector: '.glightbox', autoplayVideos: false });
    }

    cities.forEach(city => {
      const marker = L.marker(city.coords).addTo(map)
        .bindPopup(`${city.name} - Kattints a képekre vagy videókra a megtekintéshez!`);

      marker.on("click", () => {
        fetch(`gallery_loader.php?city=${city.folder}`)
          .then(res => res.text())
          .then(html => {
            gallery.innerHTML = html;
            initLightbox();
          });
      });
    });
  </script>
</body>
</html>
