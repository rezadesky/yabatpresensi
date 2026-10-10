import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Animated
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as Location from 'expo-location';
import { useAuth } from '../context/AuthContext';
import MobileHeader from '../components/MobileHeader';
import api from '../api/client';
import { calculateHaversineDistance, formatTimeWIB } from '../utils/helpers';

export default function PresensiScreen() {
  const { employee } = useAuth();
  const [loading, setLoading] = useState(true);
  const [checkingGps, setCheckingGps] = useState(false);
  const [submittingIn, setSubmittingIn] = useState(false);
  const [submittingOut, setSubmittingOut] = useState(false);
  
  const [todayAttendance, setTodayAttendance] = useState(null);
  const [institution, setInstitution] = useState(null);

  // GPS State
  const [userCoords, setUserCoords] = useState(null);
  const [accuracy, setAccuracy] = useState(null);
  const [distance, setDistance] = useState(null);
  const [isWithinRadius, setIsWithinRadius] = useState(false);
  const [gpsError, setGpsError] = useState(null);

  // Toast feedback state
  const [toastMessage, setToastMessage] = useState(null);
  const toastAnim = useRef(new Animated.Value(0)).current;

  // Real-time Clock
  const [currentTime, setCurrentTime] = useState('');
  useEffect(() => {
    const timer = setInterval(() => {
      const now = new Date();
      setCurrentTime(
        `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`
      );
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const showToast = (title, message, isSuccess = true) => {
    setToastMessage({ title, message, isSuccess });
    Animated.spring(toastAnim, {
      toValue: 1,
      useNativeDriver: true,
      tension: 60,
      friction: 8,
    }).start();

    setTimeout(() => {
      Animated.timing(toastAnim, {
        toValue: 0,
        duration: 250,
        useNativeDriver: true,
      }).start(() => setToastMessage(null));
    }, 3500);
  };

  // Fetch Attendance status from server
  const fetchStatus = async () => {
    try {
      const res = await api.get('/mobile/presensi-status');
      if (res.data.success) {
        setTodayAttendance(res.data.todayAttendance);
        setInstitution(res.data.institution);
      }
    } catch (err) {
      console.log('Error fetching presensi status:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchStatus();
  }, []);

  // Native GPS Check via expo-location
  const checkCurrentLocation = async () => {
    setCheckingGps(true);
    setGpsError(null);

    try {
      // 1. Cek / Minta Izin
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        setGpsError('Izin akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan HP Anda.');
        setCheckingGps(false);
        return;
      }

      // 2. Cek apakah layanan GPS aktif di HP
      const isEnabled = await Location.hasServicesEnabledAsync();
      if (!isEnabled) {
        setGpsError('GPS perangkat sedang tidak aktif. Harap aktifkan GPS / Lokasi perangkat Anda.');
        setCheckingGps(false);
        return;
      }

      // 3. Ambil posisi GPS Native Akurasi Tinggi
      const position = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
        mayShowUserSettingsDialog: true,
      });

      const { latitude, longitude, accuracy: posAccuracy } = position.coords;
      setUserCoords({ latitude, longitude });
      setAccuracy(posAccuracy ? Math.round(posAccuracy) : 10);

      // 4. Hitung Jarak ke Unit Institusi
      const instLat = parseFloat(institution?.latitude || employee?.institution?.latitude || 3.4883);
      const instLng = parseFloat(institution?.longitude || employee?.institution?.longitude || 97.8085);
      const maxRadius = parseInt(institution?.radius_meters || employee?.institution?.radius_meters || 100, 10);

      const calculatedDistance = calculateHaversineDistance(latitude, longitude, instLat, instLng);
      setDistance(calculatedDistance);

      if (calculatedDistance <= maxRadius) {
        setIsWithinRadius(true);
      } else {
        setIsWithinRadius(false);
      }
    } catch (err) {
      console.log('GPS error:', err);
      setGpsError('Gagal mendeteksi sinyal GPS. Pastikan Anda berada di luar ruangan dengan pandangan langit terbuka.');
    } finally {
      setCheckingGps(false);
    }
  };

  // Jalankan GPS saat layar pertama dibuka jika data institusi sudah siap
  useEffect(() => {
    if (institution || employee?.institution) {
      checkCurrentLocation();
    }
  }, [institution]);

  // Handle Presensi Masuk
  const handleCheckIn = async () => {
    if (!userCoords) {
      Alert.alert('Periksa Lokasi', 'Silakan periksa titik GPS Anda terlebih dahulu.');
      return;
    }
    if (!isWithinRadius) {
      Alert.alert('Di Luar Jangkauan', `Anda berada ${distance}m dari unit kerja. Batas radius adalah ${institution?.radius_meters || 100}m.`);
      return;
    }

    setSubmittingIn(true);
    try {
      const res = await api.post('/mobile/checkin', {
        latitude: userCoords.latitude,
        longitude: userCoords.longitude,
        notes: 'Presensi masuk dari React Native App',
      });

      if (res.data.success) {
        setTodayAttendance(res.data.data);
        showToast('Presensi Masuk Berhasil', 'Data kehadiran Anda telah tercatat valid di server yayasan.', true);
      } else {
        showToast('Presensi Gagal', res.data.message || 'Terjadi kesalahan sistem.', false);
      }
    } catch (err) {
      const msg = err.response?.data?.message || 'Gagal mengirim presensi masuk.';
      showToast('Presensi Gagal', msg, false);
    } finally {
      setSubmittingIn(false);
    }
  };

  // Handle Presensi Pulang
  const handleCheckOut = async () => {
    if (!userCoords) {
      Alert.alert('Periksa Lokasi', 'Silakan periksa titik GPS Anda terlebih dahulu.');
      return;
    }
    if (!isWithinRadius) {
      Alert.alert('Di Luar Jangkauan', `Anda berada ${distance}m dari unit kerja. Batas radius adalah ${institution?.radius_meters || 100}m.`);
      return;
    }

    setSubmittingOut(true);
    try {
      const res = await api.post('/mobile/checkout', {
        latitude: userCoords.latitude,
        longitude: userCoords.longitude,
      });

      if (res.data.success) {
        setTodayAttendance(res.data.data);
        showToast('Presensi Pulang Berhasil', 'Data checkout kepulangan telah tersimpan.', true);
      } else {
        showToast('Presensi Gagal', res.data.message || 'Terjadi kesalahan sistem.', false);
      }
    } catch (err) {
      const msg = err.response?.data?.message || 'Gagal mengirim presensi pulang.';
      showToast('Presensi Gagal', msg, false);
    } finally {
      setSubmittingOut(false);
    }
  };

  const hasCheckedIn = !!todayAttendance?.time_in;
  const hasCheckedOut = !!todayAttendance?.time_out;
  const maxRadius = institution?.radius_meters || employee?.institution?.radius_meters || 100;

  return (
    <View style={styles.container}>
      <MobileHeader employee={employee} />

      {/* Floating Modern Toast */}
      {toastMessage && (
        <Animated.View
          style={[
            styles.toast,
            {
              opacity: toastAnim,
              transform: [
                {
                  translateY: toastAnim.interpolate({
                    inputRange: [0, 1],
                    outputRange: [-20, 0],
                  }),
                },
              ],
            },
          ]}
        >
          <View style={[styles.toastIconBox, toastMessage.isSuccess ? styles.toastIconSuccess : styles.toastIconDanger]}>
            <Ionicons
              name={toastMessage.isSuccess ? 'checkmark-circle' : 'alert-circle'}
              size={20}
              color={toastMessage.isSuccess ? '#10b981' : '#ef4444'}
            />
          </View>
          <View style={styles.toastTextBox}>
            <Text style={styles.toastTitle}>{toastMessage.title}</Text>
            <Text style={styles.toastMessage}>{toastMessage.message}</Text>
          </View>
        </Animated.View>
      )}

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* B. JAM DIGITAL & JADWAL KERJA */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <Text style={styles.cardLabel}>JAM DIGITAL WAKTU NYATA</Text>
            <View style={styles.activeShiftBadge}>
              <View style={styles.activeShiftDot} />
              <Text style={styles.activeShiftText}>Jam Kerja Aktif</Text>
            </View>
          </View>

          <View style={styles.clockBox}>
            <Text style={styles.clockText}>{currentTime || '--:--:--'}</Text>
            <Text style={styles.clockSubtext}>Waktu Indonesia Barat (WIB)</Text>
          </View>

          <View style={styles.shiftGrid}>
            <View style={styles.shiftBox}>
              <View style={styles.shiftBoxTitleRow}>
                <View style={[styles.dotCircle, { backgroundColor: '#3b82f6' }]} />
                <Text style={styles.shiftBoxLabel}>Jam Masuk</Text>
              </View>
              <Text style={styles.shiftBoxTime}>07:30 WIB</Text>
              <Text style={styles.shiftBoxSub}>Toleransi s/d 07:45</Text>
            </View>
            <View style={styles.shiftBox}>
              <View style={styles.shiftBoxTitleRow}>
                <View style={[styles.dotCircle, { backgroundColor: '#f59e0b' }]} />
                <Text style={styles.shiftBoxLabel}>Jam Pulang</Text>
              </View>
              <Text style={styles.shiftBoxTime}>16:00 WIB</Text>
              <Text style={styles.shiftBoxSub}>Senin - Kamis & Sabtu</Text>
            </View>
          </View>
        </View>

        {/* C. STATUS PRESENSI HARI INI */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <Text style={styles.cardLabel}>STATUS KEHADIRAN HARI INI</Text>
            <View style={[styles.summaryBadge, todayAttendance ? styles.summaryBadgePresent : styles.summaryBadgeEmpty]}>
              <Text style={[styles.summaryBadgeText, todayAttendance ? styles.summaryBadgeTextPresent : styles.summaryBadgeTextEmpty]}>
                {todayAttendance ? `${todayAttendance.status?.toUpperCase()} (Tercatat)` : 'Belum Presensi'}
              </Text>
            </View>
          </View>

          <View style={styles.metricGrid}>
            <View style={styles.metricCard}>
              <View style={styles.metricCardHeader}>
                <Text style={styles.metricLabel}>Presensi Masuk</Text>
                <View style={[styles.statusDot, hasCheckedIn ? styles.statusDotActive : styles.statusDotInactive]} />
              </View>
              <Text style={[styles.metricTime, hasCheckedIn ? styles.metricTimeActive : styles.metricTimeInactive]}>
                {todayAttendance?.time_in ? todayAttendance.time_in.substring(0, 5) + ' WIB' : '--:--'}
              </Text>
              <Text style={styles.metricDesc}>
                {hasCheckedIn ? 'Terverifikasi lokasi GPS' : 'Menunggu verifikasi GPS'}
              </Text>
            </View>

            <View style={styles.metricCard}>
              <View style={styles.metricCardHeader}>
                <Text style={styles.metricLabel}>Presensi Pulang</Text>
                <View style={[styles.statusDot, hasCheckedOut ? styles.statusDotActive : styles.statusDotInactive]} />
              </View>
              <Text style={[styles.metricTime, hasCheckedOut ? styles.metricTimeActive : styles.metricTimeInactive]}>
                {todayAttendance?.time_out ? todayAttendance.time_out.substring(0, 5) + ' WIB' : '--:--'}
              </Text>
              <Text style={styles.metricDesc}>
                {hasCheckedOut ? 'Telah presensi pulang' : 'Tersedia jam pulang'}
              </Text>
            </View>
          </View>
        </View>

        {/* D. SENSOR LOKASI GPS (NATIVE) */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <View style={styles.gpsTitleRow}>
              <View style={styles.gpsIconContainer}>
                <Ionicons name="location" size={16} color="#2563eb" />
              </View>
              <View>
                <Text style={styles.gpsTitle}>Sensor Lokasi GPS</Text>
                <Text style={styles.gpsSubtitle}>Native Android Geolocation</Text>
              </View>
            </View>

            <View
              style={[
                styles.gpsBadge,
                userCoords ? (isWithinRadius ? styles.gpsBadgeGood : styles.gpsBadgeWarning) : styles.gpsBadgeDefault,
              ]}
            >
              <Text
                style={[
                  styles.gpsBadgeText,
                  userCoords ? (isWithinRadius ? styles.gpsBadgeTextGood : styles.gpsBadgeTextWarning) : styles.gpsBadgeTextDefault,
                ]}
              >
                {checkingGps ? 'Memindai...' : userCoords ? (isWithinRadius ? 'Sinyal Terkunci' : 'Di Luar Radius') : 'Belum Diperiksa'}
              </Text>
            </View>
          </View>

          {/* Koordinat & Akurasi Display */}
          <View style={styles.coordsBox}>
            <View style={styles.coordRow}>
              <Text style={styles.coordLabel}>Latitude:</Text>
              <Text style={styles.coordValue}>{userCoords ? userCoords.latitude.toFixed(6) : '-'}</Text>
            </View>
            <View style={styles.coordRow}>
              <Text style={styles.coordLabel}>Longitude:</Text>
              <Text style={styles.coordValue}>{userCoords ? userCoords.longitude.toFixed(6) : '-'}</Text>
            </View>
            <View style={[styles.coordRow, { borderTopWidth: 1, borderTopColor: '#e2e8f0', paddingTop: 6, marginTop: 4 }]}>
              <Text style={styles.coordLabel}>Akurasi Sinyal GPS:</Text>
              <Text style={styles.coordValue}>
                {accuracy ? `± ${accuracy} meter (${accuracy <= 25 ? 'Sangat Akurat' : 'Cukup'})` : '-'}
              </Text>
            </View>
          </View>

          {/* Error Notice Box */}
          {gpsError && (
            <View style={styles.errorBox}>
              <Ionicons name="alert-circle" size={18} color="#e11d48" style={{ marginRight: 6 }} />
              <View style={{ flex: 1 }}>
                <Text style={styles.errorTitle}>Gagal Mengambil Lokasi</Text>
                <Text style={styles.errorText}>{gpsError}</Text>
              </View>
            </View>
          )}

          {/* Tombol Periksa Titik GPS */}
          <TouchableOpacity
            style={styles.refreshGpsButton}
            onPress={checkCurrentLocation}
            disabled={checkingGps}
            activeOpacity={0.85}
          >
            {checkingGps ? (
              <ActivityIndicator color="#ffffff" size="small" />
            ) : (
              <>
                <Ionicons name="refresh" size={16} color="#ffffff" />
                <Text style={styles.refreshGpsButtonText}>Periksa / Perbarui Titik GPS</Text>
              </>
            )}
          </TouchableOpacity>
        </View>

        {/* E. VALIDASI AREA PRESENSI (GEOFENCING) */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <View>
              <Text style={styles.cardLabel}>VERIFIKASI AREA KERJA</Text>
              <Text style={styles.institutionName}>
                {institution?.name || employee?.institution?.name || 'Unit Institusi Yayasan'}
              </Text>
            </View>

            <View
              style={[
                styles.radiusBadge,
                userCoords
                  ? isWithinRadius
                    ? styles.radiusBadgeValid
                    : styles.radiusBadgeInvalid
                  : styles.radiusBadgeWait,
              ]}
            >
              <Text
                style={[
                  styles.radiusBadgeText,
                  userCoords
                    ? isWithinRadius
                      ? styles.radiusBadgeTextValid
                      : styles.radiusBadgeTextInvalid
                    : styles.radiusBadgeTextWait,
                ]}
              >
                {userCoords ? (isWithinRadius ? 'Dalam Radius' : 'Di Luar Area') : 'Menunggu GPS'}
              </Text>
            </View>
          </View>

          <View style={styles.distanceGrid}>
            <View style={styles.distanceBox}>
              <Text style={styles.distanceBoxLabel}>Jarak ke Unit Kerja</Text>
              <Text style={styles.distanceBoxValue}>
                {distance !== null ? `${distance} Meter` : '-'}
              </Text>
              <Text style={styles.distanceBoxSub}>Dihitung otomatis (Haversine)</Text>
            </View>

            <View style={styles.distanceBox}>
              <Text style={styles.distanceBoxLabel}>Batas Maksimal Radius</Text>
              <Text style={[styles.distanceBoxValue, { color: '#2563eb' }]}>
                {maxRadius} Meter
              </Text>
              <Text style={styles.distanceBoxSub}>Kebijakan resmi yayasan</Text>
            </View>
          </View>

          {/* Indikator Informatif Sisa Jarak */}
          {userCoords && (
            <View
              style={[
                styles.guidanceBox,
                isWithinRadius ? styles.guidanceBoxValid : styles.guidanceBoxInvalid,
              ]}
            >
              <Ionicons
                name={isWithinRadius ? 'shield-checkmark' : 'warning'}
                size={18}
                color={isWithinRadius ? '#059669' : '#dc2626'}
                style={{ marginRight: 8 }}
              />
              <View style={{ flex: 1 }}>
                <Text style={[styles.guidanceTitle, { color: isWithinRadius ? '#065f46' : '#991b1b' }]}>
                  {isWithinRadius ? 'Lokasi Valid untuk Presensi' : 'Posisi Terlalu Jauh'}
                </Text>
                <Text style={[styles.guidanceText, { color: isWithinRadius ? '#047857' : '#b91c1c' }]}>
                  {isWithinRadius
                    ? 'Anda terverifikasi berada di dalam wilayah resmi kampus / sekolah.'
                    : `Anda berada ${distance}m dari unit (batas ${maxRadius}m). Dekatilah lokasi unit kerja.`}
                </Text>
              </View>
            </View>
          )}
        </View>

        {/* F. TOMBOL PRESENSI LANGSUNG */}
        <View style={styles.actionSection}>
          <View style={styles.actionButtonsRow}>
            {/* Tombol Masuk */}
            <TouchableOpacity
              style={[
                styles.actionBtn,
                !hasCheckedIn && isWithinRadius ? styles.actionBtnActiveIn : styles.actionBtnDisabled,
              ]}
              disabled={hasCheckedIn || !isWithinRadius || submittingIn}
              onPress={handleCheckIn}
              activeOpacity={0.85}
            >
              {submittingIn ? (
                <ActivityIndicator color="#ffffff" size="small" />
              ) : (
                <>
                  <Ionicons name="log-in-outline" size={20} color={!hasCheckedIn && isWithinRadius ? '#ffffff' : '#94a3b8'} />
                  <Text style={[styles.actionBtnText, !hasCheckedIn && isWithinRadius ? styles.actionBtnTextActive : styles.actionBtnTextDisabled]}>
                    Presensi Masuk
                  </Text>
                  <Text style={styles.actionBtnSub}>
                    {hasCheckedIn ? 'Sudah presensi masuk' : isWithinRadius ? 'Tekan untuk absen' : 'Perlu periksa lokasi'}
                  </Text>
                </>
              )}
            </TouchableOpacity>

            {/* Tombol Pulang */}
            <TouchableOpacity
              style={[
                styles.actionBtn,
                hasCheckedIn && !hasCheckedOut && isWithinRadius ? styles.actionBtnActiveOut : styles.actionBtnDisabled,
              ]}
              disabled={!hasCheckedIn || hasCheckedOut || !isWithinRadius || submittingOut}
              onPress={handleCheckOut}
              activeOpacity={0.85}
            >
              {submittingOut ? (
                <ActivityIndicator color="#ffffff" size="small" />
              ) : (
                <>
                  <Ionicons name="log-out-outline" size={20} color={hasCheckedIn && !hasCheckedOut && isWithinRadius ? '#ffffff' : '#94a3b8'} />
                  <Text style={[styles.actionBtnText, hasCheckedIn && !hasCheckedOut && isWithinRadius ? styles.actionBtnTextActive : styles.actionBtnTextDisabled]}>
                    Presensi Pulang
                  </Text>
                  <Text style={styles.actionBtnSub}>
                    {hasCheckedOut ? 'Sudah presensi pulang' : hasCheckedIn ? (isWithinRadius ? 'Tekan untuk pulang' : 'Di luar radius') : 'Menunggu absen masuk'}
                  </Text>
                </>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 28,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 18,
    padding: 16,
    borderWidth: 1,
    borderColor: 'rgba(226, 232, 240, 0.8)',
    marginBottom: 14,
    shadowColor: '#64748b',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 2,
  },
  cardHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  cardLabel: {
    fontSize: 9.5,
    fontWeight: '700',
    color: '#94a3b8',
    letterSpacing: 0.8,
  },
  activeShiftBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
    borderWidth: 1,
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 3,
  },
  activeShiftDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#10b981',
    marginRight: 5,
  },
  activeShiftText: {
    fontSize: 10,
    fontWeight: '700',
    color: '#047857',
  },
  clockBox: {
    alignItems: 'center',
    paddingVertical: 6,
  },
  clockText: {
    fontSize: 34,
    fontWeight: '800',
    color: '#0f172a',
    fontFamily: 'monospace',
    letterSpacing: 1,
  },
  clockSubtext: {
    fontSize: 10,
    color: '#94a3b8',
    marginTop: 2,
  },
  shiftGrid: {
    flexDirection: 'row',
    gap: 10,
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
    paddingTop: 10,
    marginTop: 8,
  },
  shiftBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    padding: 10,
  },
  shiftBoxTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
  },
  dotCircle: {
    width: 6,
    height: 6,
    borderRadius: 3,
  },
  shiftBoxLabel: {
    fontSize: 9.5,
    color: '#64748b',
    fontWeight: '600',
  },
  shiftBoxTime: {
    fontSize: 13,
    fontWeight: '800',
    color: '#0f172a',
    fontFamily: 'monospace',
    marginTop: 2,
  },
  shiftBoxSub: {
    fontSize: 9,
    color: '#94a3b8',
    marginTop: 1,
  },
  summaryBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  summaryBadgePresent: {
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
    borderWidth: 1,
  },
  summaryBadgeEmpty: {
    backgroundColor: '#f1f5f9',
  },
  summaryBadgeText: {
    fontSize: 10,
    fontWeight: '700',
  },
  summaryBadgeTextPresent: {
    color: '#047857',
  },
  summaryBadgeTextEmpty: {
    color: '#64748b',
  },
  metricGrid: {
    flexDirection: 'row',
    gap: 10,
  },
  metricCard: {
    flex: 1,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: 'rgba(226, 232, 240, 0.8)',
    backgroundColor: 'rgba(248, 250, 252, 0.6)',
    padding: 12,
  },
  metricCardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  metricLabel: {
    fontSize: 10.5,
    fontWeight: '600',
    color: '#64748b',
  },
  statusDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
  },
  statusDotActive: {
    backgroundColor: '#10b981',
  },
  statusDotInactive: {
    backgroundColor: '#cbd5e1',
  },
  metricTime: {
    fontSize: 16,
    fontWeight: '800',
    fontFamily: 'monospace',
    marginBottom: 2,
  },
  metricTimeActive: {
    color: '#059669',
  },
  metricTimeInactive: {
    color: '#334155',
  },
  metricDesc: {
    fontSize: 9.5,
    color: '#94a3b8',
  },
  gpsTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  gpsIconContainer: {
    width: 28,
    height: 28,
    borderRadius: 8,
    backgroundColor: '#eff6ff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  gpsTitle: {
    fontSize: 12,
    fontWeight: '700',
    color: '#0f172a',
  },
  gpsSubtitle: {
    fontSize: 9.5,
    color: '#94a3b8',
  },
  gpsBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
    borderWidth: 1,
  },
  gpsBadgeGood: {
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
  },
  gpsBadgeWarning: {
    backgroundColor: '#fffbeb',
    borderColor: '#fde68a',
  },
  gpsBadgeDefault: {
    backgroundColor: '#f1f5f9',
    borderColor: '#e2e8f0',
  },
  gpsBadgeText: {
    fontSize: 10,
    fontWeight: '700',
  },
  gpsBadgeTextGood: {
    color: '#047857',
  },
  gpsBadgeTextWarning: {
    color: '#b45309',
  },
  gpsBadgeTextDefault: {
    color: '#64748b',
  },
  coordsBox: {
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    padding: 10,
    marginBottom: 10,
  },
  coordRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 2,
  },
  coordLabel: {
    fontSize: 11,
    color: '#94a3b8',
  },
  coordValue: {
    fontSize: 11,
    fontWeight: '700',
    color: '#1e293b',
    fontFamily: 'monospace',
  },
  errorBox: {
    backgroundColor: '#fff1f2',
    borderColor: '#fecdd3',
    borderWidth: 1,
    borderRadius: 12,
    padding: 10,
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 10,
  },
  errorTitle: {
    color: '#9f1239',
    fontSize: 11,
    fontWeight: '700',
  },
  errorText: {
    color: '#e11d48',
    fontSize: 10,
    lineHeight: 14,
    marginTop: 1,
  },
  refreshGpsButton: {
    backgroundColor: '#2563eb',
    borderRadius: 12,
    paddingVertical: 11,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
  },
  refreshGpsButtonText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },
  institutionName: {
    fontSize: 12,
    fontWeight: '700',
    color: '#1e293b',
    marginTop: 2,
  },
  radiusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
    borderWidth: 1,
  },
  radiusBadgeValid: {
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
  },
  radiusBadgeInvalid: {
    backgroundColor: '#fff1f2',
    borderColor: '#fecdd3',
  },
  radiusBadgeWait: {
    backgroundColor: '#f1f5f9',
    borderColor: '#e2e8f0',
  },
  radiusBadgeText: {
    fontSize: 10,
    fontWeight: '700',
  },
  radiusBadgeTextValid: {
    color: '#047857',
  },
  radiusBadgeTextInvalid: {
    color: '#e11d48',
  },
  radiusBadgeTextWait: {
    color: '#64748b',
  },
  distanceGrid: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 2,
  },
  distanceBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    padding: 10,
  },
  distanceBoxLabel: {
    fontSize: 9.5,
    color: '#94a3b8',
    fontWeight: '600',
  },
  distanceBoxValue: {
    fontSize: 14,
    fontWeight: '800',
    color: '#0f172a',
    fontFamily: 'monospace',
    marginTop: 2,
  },
  distanceBoxSub: {
    fontSize: 9,
    color: '#94a3b8',
    marginTop: 1,
  },
  guidanceBox: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 10,
    borderRadius: 12,
    borderWidth: 1,
    marginTop: 10,
  },
  guidanceBoxValid: {
    backgroundColor: '#f0fdf4',
    borderColor: '#bbf7d0',
  },
  guidanceBoxInvalid: {
    backgroundColor: '#fef2f2',
    borderColor: '#fecaca',
  },
  guidanceTitle: {
    fontSize: 11,
    fontWeight: '700',
  },
  guidanceText: {
    fontSize: 10,
    lineHeight: 14,
    marginTop: 1,
  },
  actionSection: {
    marginTop: 2,
  },
  actionButtonsRow: {
    flexDirection: 'row',
    gap: 10,
  },
  actionBtn: {
    flex: 1,
    borderRadius: 16,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  actionBtnActiveIn: {
    backgroundColor: '#2563eb',
  },
  actionBtnActiveOut: {
    backgroundColor: '#ea580c',
  },
  actionBtnDisabled: {
    backgroundColor: '#e2e8f0',
  },
  actionBtnText: {
    fontSize: 12,
    fontWeight: '800',
    marginTop: 4,
  },
  actionBtnTextActive: {
    color: '#ffffff',
  },
  actionBtnTextDisabled: {
    color: '#64748b',
  },
  actionBtnSub: {
    fontSize: 9,
    color: 'rgba(255, 255, 255, 0.85)',
    marginTop: 2,
  },
  toast: {
    position: 'absolute',
    top: 14,
    left: 20,
    right: 20,
    zIndex: 9999,
    backgroundColor: 'rgba(15, 23, 42, 0.96)',
    borderRadius: 16,
    padding: 12,
    flexDirection: 'row',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 10,
    borderWidth: 1,
    borderColor: 'rgba(51, 65, 85, 0.8)',
  },
  toastIconBox: {
    width: 32,
    height: 32,
    borderRadius: 10,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  toastIconSuccess: {
    backgroundColor: 'rgba(16, 185, 129, 0.15)',
  },
  toastIconDanger: {
    backgroundColor: 'rgba(239, 68, 68, 0.15)',
  },
  toastTextBox: {
    flex: 1,
  },
  toastTitle: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },
  toastMessage: {
    color: '#cbd5e1',
    fontSize: 10.5,
    marginTop: 1,
  },
});
