const CACHE_NAME = "SW-001";
const toCache = [
  "/index.php",
  "manifest.json",
  "service-worker.js",
  "assets/Decor.png",
  "assets/paketgold.png",
  "assets/paketplatinum.png",
  "assets/paketsilver.png",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(toCache);
    })
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    fetch(event.request).catch(() =>
      caches.match(event.request).then((response) => {
        return response || caches.match("/index.php");
      })
    )
  );
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      )
    )
  );
});
