@php
$sekolah = $sekolah ?? new \App\Models\ProfilSekolah(); // Inisialisasi jika variabel $sekolah belum ada
$teks = [
    'npsn'      => 'NPSN (8 digit)',
    'nama'      => 'Nama Sekolah',
    'kecamatan' => 'Kecamatan',
    'telepon'   => 'Telepon',
];
$pilihan = [
    'jenjang' => ['SD', 'SMP', 'SMA', 'SMK'],
    'status'  => ['Negeri', 'Swasta'],
];
$cls = 'w-full border rounded px-3 py-2 mt-1';
@endphp

<div>
    {{-- Kartu Putih Utama (Grid 2 Kolom: Kiri Form, Kanan Peta) --}}
    <div style="display: flex; gap: 24px; background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

        {{-- KOLOM KIRI --}}
        <div style="flex: 1; display: flex; flex-direction: column; gap: 16px;">
            @foreach ($teks as $name => $label)
            <div>
                <label for="{{ $name }}" class="block font-medium">{{ $label }}</label>
                <input type="text" id="{{ $name }}" name="{{ $name }}"
                    value="{{ old($name, $sekolah->$name) }}"
                    @readonly($name === 'npsn' && auth()->user()->isSekolah())
                    class="{{ $cls }}">
                @error($name)
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            @foreach ($pilihan as $name => $opsi)
            <div>
                <label for="{{ $name }}" class="block font-medium">{{ ucfirst($name) }}</label>
                <select id="{{ $name }}" name="{{ $name }}" class="{{ $cls }}">
                    <option value="">-- Pilih --</option>
                    @foreach ($opsi as $o)
                        <option value="{{ $o }}" @selected(old($name, $sekolah->$name) === $o)>{{ $o }}</option>
                    @endforeach
                </select>
                @error($name)
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <div>
                <label for="alamat" class="block font-medium">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" class="{{ $cls }}">{{ old('alamat', $sekolah->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div style="flex: 1; display: flex; flex-direction: column; gap: 16px;">
            <p class="font-medium">Klik peta atau geser marker untuk menentukan lokasi sekolah</p>

            <div id="map-picker" style="height: 300px; width: 100%; border-radius: 4px; border: 1px solid #ccc;"></div>

            <div style="display: flex; gap: 16px;">
                <div style="flex: 1;">
                    <label for="latitude" class="block font-medium">Latitude</label>
                    <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $sekolah->latitude) }}" class="{{ $cls }}">
                    @error('latitude')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="longitude" class="block font-medium">Longitude</label>
                    <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $sekolah->longitude) }}" class="{{ $cls }}">
                    @error('longitude')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; padding-top: 8px;">
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white rounded">Simpan</button>
                <a href="{{ auth()->user()->isDinas() ? route('dinas.sekolah.index') : route('dashboard') }}" class="px-5 py-2 bg-gray-300 rounded text-gray-800">Batal</a>
            </div>
        </div>

    </div>

    {{-- BAGIAN BAWAH (Di luar kartu putih sesuai gambar) --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
        <span class="font-medium text-gray-800">Klik peta/geser marker untuk lokasi</span>
            <button type="button" id="btn-my-location" style="background-color: #10b981; color: #ffffff; padding: 8px 16px; border-radius: 6px; border: none; font-weight: 500; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            📍 Gunakan Lokasi Saya
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const sudahAda = latInput.value !== '' && lngInput.value !== '';
    const pusat = sudahAda
        ? [parseFloat(latInput.value), parseFloat(lngInput.value)]
        : [-3.3194, 114.5908];

    const map = L.map('map-picker').setView(pusat, sudahAda ? 16 : 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const marker = L.marker(pusat, { draggable: true }).addTo(map);

    setTimeout(() => {
        map.invalidateSize();
    }, 200);

    function isiKoordinat(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        isiKoordinat(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', () => {
        const p = marker.getLatLng();
        isiKoordinat(p.lat, p.lng);
    });

    [latInput, lngInput].forEach((el) => {
        el.addEventListener('change', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);
            }
        });
    });

    document.getElementById('btn-my-location').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    isiKoordinat(lat, lng);
                },
                () => alert('Gagal mengambil lokasi. Pastikan izin lokasi aktif.')
            );
        } else {
            alert('Browser Anda tidak mendukung fitur Geolocation.');
        }
    });
});
</script>
@endpush