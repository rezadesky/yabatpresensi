/**
 * Rumus Haversine: Menghitung jarak (meter) antar dua titik koordinat GPS
 * Digunakan untuk validasi Geofencing kehadiran pegawai
 */
export function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
  if (lat1 == null || lon1 == null || lat2 == null || lon2 == null) {
    return null;
  }

  const R = 6371e3; // radius bumi dalam meter
  const φ1 = (lat1 * Math.PI) / 180;
  const φ2 = (lat2 * Math.PI) / 180;
  const Δφ = ((lat2 - lat1) * Math.PI) / 180;
  const Δλ = ((lon2 - lon1) * Math.PI) / 180;

  const a =
    Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return Math.round(R * c);
}

/**
 * Format string jam ke format 'HH:mm WIB'
 */
export function formatTimeWIB(timeStr) {
  if (!timeStr) return '--:--';
  return timeStr.substring(0, 5) + ' WIB';
}

/**
 * Format tanggal ke nama hari & tanggal lokal Indonesia
 */
export function formatDateID(dateInput) {
  if (!dateInput) return '-';
  const d = new Date(dateInput);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

/**
 * Ambil inisial 2 karakter dari nama pegawai
 */
export function getInitials(name) {
  if (!name) return 'P';
  return name
    .trim()
    .split(' ')
    .filter(Boolean)
    .map((w) => w[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
}
