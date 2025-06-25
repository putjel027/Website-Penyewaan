// FUNGSI UNTUK MENAMPILKAN POPUP DETAIL PAKET

// Dapatkan semua tombol detail
const detailButtons = document.querySelectorAll(".detail-btn");
const detailPopup = document.getElementById("detailPopup");
const closeDetail = document.getElementById("closeDetail");

// Tambahkan event click ke semua tombol detail
detailButtons.forEach((button) => {
  button.addEventListener("click", function (e) {
    e.preventDefault();

    // Ambil data dari atribut data-*
    const paket = this.getAttribute("data-paket");
    const nuansa = this.getAttribute("data-nuansa");
    const harga = this.getAttribute("data-harga");
    const tinggi = this.getAttribute("data-tinggi");
    const lebar = this.getAttribute("data-lebar");
    const img = this.getAttribute("data-img");

    // Setel data ke elemen popup
    document.getElementById("popupPaket").textContent = paket;
    document.getElementById("popupNuansa").textContent = nuansa;
    document.getElementById("popupHarga").textContent = harga;
    document.getElementById("popupTinggi").textContent = tinggi;
    document.getElementById("popupLebar").textContent = lebar;
    document.getElementById("popupImage").src = "data:image/jpeg;base64," + img;

    // Tampilkan popup
    detailPopup.style.display = "flex";
    document.body.style.overflow = "hidden"; // Mencegah scrolling di belakang popup
  });
});

// Tutup popup saat tombol close diklik
closeDetail.addEventListener("click", function () {
  detailPopup.style.display = "none";
  document.body.style.overflow = "auto";
});
