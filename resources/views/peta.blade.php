<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah - Latihan Mandiri</title>
    
    <!-- Leaflet CSS & MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    <style>
        html, body { height: 100%; margin: 0; font-family: system-ui, sans-serif; }
        header { height: 56px; box-sizing: border-box; padding: 0 16px; display: flex; align-items: center; gap: 12px; background: #1e3a8a; color: #fff; flex-wrap: wrap; }
        header h1 { font-size: 18px; margin: 0; }
        header select, header input { padding: 4px 8px; border-radius: 4px; border: none; }
        header a { color: #bfdbfe; margin-left: auto; text-decoration: none; }
        header a:hover { color: #ffffff; text-decoration: underline; }
        #map { height: calc(100% - 56px); }
    </style>
</head>
<body>
    <header>
        <h1>Peta Sekolah</h1>
        
        <!-- Filter Jenjang -->
        <select id="filter-jenjang">
            <option value="">Semua Jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>

        <!-- Filter Status (Poin 2) -->
        <select id="filter-status">
            <option value="">Semua Status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>

        <!-- Pencarian Sekolah (Poin 3) -->
        <input type="text" id="cari-sekolah" placeholder="Cari nama sekolah..." list="list-sekolah">
        <datalist id="list-sekolah"></datalist>
        <span id="jumlah"></span>

        <!-- Navigasi Autentikasi Praktikum 14 -->
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Masuk</a>
        @endauth
    </header>

    <div id="map"></div>

    <!-- Leaflet JS & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        // --- 1. Kontrol Layer Peta Dasar (Poin 6) ---
        const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap'
        });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19, attribution: '&copy; Esri World Imagery'
        });
        const map = L.map('map', {
            center: [-3.3194, 114.5908],
            zoom: 12,
            layers: [osm]
        });
        const baseMaps = {
            "OpenStreetMap": osm,
            "Satelit (Esri)": satellite
        };
        L.control.layers(baseMaps).addTo(map);

        // --- 2. Marker Custom Warna per Jenjang (Poin 1) ---
        function getIconWarna(jenjang) {
            let color = 'blue';
            if (jenjang === 'SD') color = 'red';
            else if (jenjang === 'SMP') color = 'blue';
            else if (jenjang === 'SMA') color = 'green';
            else if (jenjang === 'SMK') color = 'orange';
            return new L.Icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        // --- 3. MarkerCluster (Poin 5) ---
        const markersCluster = L.markerClusterGroup();
        map.addLayer(markersCluster);
        let dataSekolahGlobal = [];
        const markerMap = {};

        function esc(teks) {
            const d = document.createElement('div');
            d.textContent = teks ?? '';
            return d.innerHTML;
        }

        async function muatData() {
            const jenjang = document.getElementById('filter-jenjang').value;
            const status = document.getElementById('filter-status').value;
            const url = `{{ route('peta.data') }}?jenjang=${jenjang}&status=${status}`;
            const respon = await fetch(url);
            const data = await respon.json();
            markersCluster.clearLayers();
            dataSekolahGlobal = data.features;
            const datalist = document.getElementById('list-sekolah');
            datalist.innerHTML = '';
            data.features.forEach(fitur => {
                const p = fitur.properties;
                const [lng, lat] = fitur.geometry.coordinates;
                
                // Marker individu dengan warna sesuai jenjang
                const marker = L.marker([lat, lng], { icon: getIconWarna(p.jenjang) });
                marker.bindPopup(`
                    <strong>${esc(p.nama)}</strong><br>
                    NPSN: ${esc(p.npsn)}<br>
                    ${esc(p.jenjang)} ${esc(p.status)}<br>
                    ${esc(p.alamat)}, ${esc(p.kecamatan)}<br>
                    Telp: ${esc(p.telepon) || '-'}<br>
                    Kepala Sekolah: ${esc(p.kepala_sekolah) || '-'}<br>
                    Akreditasi: ${esc(p.akreditasi) || '-'}<br>
                    Siswa: ${p.jumlah_siswa ?? '-'} | Guru: ${p.jumlah_guru ?? '-'}<br>
                    <small>Koordinat: ${lat}, ${lng}</small>
                `);
                markersCluster.addLayer(marker);
                markerMap[p.nama.toLowerCase()] = { marker, lat, lng };

                // Isi pilihan pencarian auto-complete
                const option = document.createElement('option');
                option.value = p.nama;
                datalist.appendChild(option);
            });
            document.getElementById('jumlah').textContent = data.features.length + ' sekolah';
            if (data.features.length > 0) {
                map.fitBounds(markersCluster.getBounds().pad(0.1));
            }
        }

        // --- 4. Pencarian Nama Sekolah (Poin 3) ---
        document.getElementById('cari-sekolah').addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase().trim();
            if (markerMap[val]) {
                const item = markerMap[val];
                map.setView([item.lat, item.lng], 17);
                markersCluster.zoomToShowLayer(item.marker, () => {
                    item.marker.openPopup();
                });
            }
        });

        // Event Listener Filter
        document.getElementById('filter-jenjang').addEventListener('change', muatData);
        document.getElementById('filter-status').addEventListener('change', muatData);
        muatData();
    </script>
</body>
</html>