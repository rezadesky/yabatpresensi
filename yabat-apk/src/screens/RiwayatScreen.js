import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../context/AuthContext';
import MobileHeader from '../components/MobileHeader';
import api from '../api/client';
import { formatDateID, formatTimeWIB } from '../utils/helpers';

export default function RiwayatScreen() {
  const { employee } = useAuth();
  const [period, setPeriod] = useState('month'); // 'month', 'week', 'all'
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [records, setRecords] = useState([]);
  const [stats, setStats] = useState({
    total: 0,
    hadir: 0,
    terlambat: 0,
    izin: 0,
    alpa: 0,
  });

  const fetchRiwayat = async (selectedPeriod = period) => {
    try {
      const res = await api.get(`/mobile/riwayat?period=${selectedPeriod}`);
      if (res.data.success) {
        setRecords(res.data.records || []);
        setStats(res.data.stats || { total: 0, hadir: 0, terlambat: 0, izin: 0, alpa: 0 });
      }
    } catch (err) {
      console.log('Error fetching riwayat:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchRiwayat(period);
  }, [period]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchRiwayat(period);
  };

  return (
    <View style={styles.container}>
      <MobileHeader employee={employee} />

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2563eb']} />
        }
        showsVerticalScrollIndicator={false}
      >
        {/* 1. Ringkasan Presensi Pegawai & Filter Periode */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <View>
              <Text style={styles.cardLabel}>REKAPITULASI PRIBADI</Text>
              <Text style={styles.sectionSubtitle}>
                {period === 'week' ? 'Minggu Ini' : period === 'all' ? 'Seluruh Riwayat' : 'Bulan Berjalan'}
              </Text>
            </View>
            <View style={styles.logBadge}>
              <Text style={styles.logBadgeText}>{stats.total} Log</Text>
            </View>
          </View>

          {/* Filter Periode (Tabs Pill) */}
          <View style={styles.filterPillsContainer}>
            <TouchableOpacity
              style={[styles.filterPill, period === 'month' && styles.filterPillActive]}
              onPress={() => setPeriod('month')}
              activeOpacity={0.7}
            >
              <Text style={[styles.filterPillText, period === 'month' && styles.filterPillTextActive]}>
                Bulan Ini
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.filterPill, period === 'week' && styles.filterPillActive]}
              onPress={() => setPeriod('week')}
              activeOpacity={0.7}
            >
              <Text style={[styles.filterPillText, period === 'week' && styles.filterPillTextActive]}>
                Minggu Ini
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.filterPill, period === 'all' && styles.filterPillActive]}
              onPress={() => setPeriod('all')}
              activeOpacity={0.7}
            >
              <Text style={[styles.filterPillText, period === 'all' && styles.filterPillTextActive]}>
                Semua
              </Text>
            </TouchableOpacity>
          </View>

          {/* 4 Minimal Stat Cards */}
          <View style={styles.statsGrid}>
            <View style={[styles.statBox, { backgroundColor: '#ecfdf5', borderColor: '#d1fae5' }]}>
              <Text style={[styles.statBoxTitle, { color: '#065f46' }]}>Hadir</Text>
              <Text style={[styles.statBoxNumber, { color: '#047857' }]}>{stats.hadir}</Text>
            </View>

            <View style={[styles.statBox, { backgroundColor: '#fffbeb', borderColor: '#fef3c7' }]}>
              <Text style={[styles.statBoxTitle, { color: '#92400e' }]}>Telat</Text>
              <Text style={[styles.statBoxNumber, { color: '#b45309' }]}>{stats.terlambat}</Text>
            </View>

            <View style={[styles.statBox, { backgroundColor: '#eff6ff', borderColor: '#dbeafe' }]}>
              <Text style={[styles.statBoxTitle, { color: '#1e40af' }]}>Izin</Text>
              <Text style={[styles.statBoxNumber, { color: '#2563eb' }]}>{stats.izin}</Text>
            </View>

            <View style={[styles.statBox, { backgroundColor: '#fff1f2', borderColor: '#ffe4e6' }]}>
              <Text style={[styles.statBoxTitle, { color: '#9f1239' }]}>Alpa</Text>
              <Text style={[styles.statBoxNumber, { color: '#e11d48' }]}>{stats.alpa}</Text>
            </View>
          </View>
        </View>

        {/* 2. Daftar Riwayat Presensi Komprehensif */}
        <View style={styles.card}>
          <View style={styles.listHeaderRow}>
            <View>
              <Text style={styles.cardLabel}>CATATAN WAKTU</Text>
              <Text style={styles.listTitle}>Daftar Presensi Harian</Text>
            </View>
            <Text style={styles.listPeriodLabel}>
              {period === 'week' ? '7 Hari Terakhir' : period === 'all' ? 'Seluruh Riwayat' : 'Bulan Berjalan'}
            </Text>
          </View>

          {loading ? (
            <View style={{ paddingVertical: 30, alignItems: 'center' }}>
              <ActivityIndicator color="#2563eb" size="small" />
              <Text style={{ fontSize: 11, color: '#94a3b8', marginTop: 8 }}>Memuat riwayat...</Text>
            </View>
          ) : records.length === 0 ? (
            <View style={styles.emptyState}>
              <Ionicons name="calendar-outline" size={32} color="#cbd5e1" />
              <Text style={styles.emptyText}>Belum ada data presensi pada periode ini.</Text>
            </View>
          ) : (
            <View style={styles.recordsList}>
              {records.map((item, index) => {
                const isHadir = item.status === 'hadir';
                const isTelat = item.status === 'terlambat';

                return (
                  <View 
                    key={item.id || index} 
                    style={[
                      styles.recordItem, 
                      index !== records.length - 1 && styles.recordItemBorder
                    ]}
                  >
                    <View style={styles.recordHeader}>
                      <Text style={styles.recordDate}>{formatDateID(item.date)}</Text>
                      <View
                        style={[
                          styles.statusTag,
                          isHadir
                            ? styles.statusTagHadir
                            : isTelat
                            ? styles.statusTagTelat
                            : styles.statusTagDefault,
                        ]}
                      >
                        <Text
                          style={[
                            styles.statusTagText,
                            isHadir
                              ? styles.statusTagTextHadir
                              : isTelat
                              ? styles.statusTagTextTelat
                              : styles.statusTagTextDefault,
                          ]}
                        >
                          {item.status ? item.status.toUpperCase() : 'HADIR'}
                        </Text>
                      </View>
                    </View>

                    <View style={styles.recordDetailsBox}>
                      <View style={styles.recordDetailCol}>
                        <Text style={styles.recordDetailLabel}>Waktu Masuk:</Text>
                        <Text style={styles.recordDetailValue}>{formatTimeWIB(item.time_in)}</Text>
                      </View>
                      <View style={styles.recordDetailCol}>
                        <Text style={styles.recordDetailLabel}>Waktu Pulang:</Text>
                        <Text style={styles.recordDetailValue}>{formatTimeWIB(item.time_out)}</Text>
                      </View>
                    </View>
                  </View>
                );
              })}
            </View>
          )}
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
  sectionSubtitle: {
    fontSize: 12,
    fontWeight: '700',
    color: '#1e293b',
    marginTop: 2,
  },
  logBadge: {
    backgroundColor: '#eff6ff',
    borderColor: '#bfdbfe',
    borderWidth: 1,
    borderRadius: 6,
    paddingHorizontal: 8,
    paddingVertical: 2,
  },
  logBadgeText: {
    fontSize: 10,
    fontWeight: '700',
    color: '#2563eb',
  },
  filterPillsContainer: {
    flexDirection: 'row',
    backgroundColor: '#f1f5f9',
    borderRadius: 12,
    padding: 3,
    marginBottom: 12,
  },
  filterPill: {
    flex: 1,
    paddingVertical: 6,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 9,
  },
  filterPillActive: {
    backgroundColor: '#ffffff',
    shadowColor: '#64748b',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.12,
    shadowRadius: 2,
    elevation: 1,
  },
  filterPillText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#64748b',
  },
  filterPillTextActive: {
    color: '#2563eb',
    fontWeight: '800',
  },
  statsGrid: {
    flexDirection: 'row',
    gap: 8,
  },
  statBox: {
    flex: 1,
    borderRadius: 12,
    borderWidth: 1,
    paddingVertical: 8,
    alignItems: 'center',
  },
  statBoxTitle: {
    fontSize: 10,
    fontWeight: '600',
  },
  statBoxNumber: {
    fontSize: 14,
    fontWeight: '800',
    fontFamily: 'monospace',
    marginTop: 2,
  },
  listHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
    paddingBottom: 8,
    marginBottom: 8,
  },
  listTitle: {
    fontSize: 12,
    fontWeight: '700',
    color: '#1e293b',
    marginTop: 1,
  },
  listPeriodLabel: {
    fontSize: 10,
    color: '#94a3b8',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 26,
  },
  emptyText: {
    fontSize: 11,
    color: '#94a3b8',
    marginTop: 8,
  },
  recordsList: {},
  recordItem: {
    paddingVertical: 10,
  },
  recordItemBorder: {
    borderBottomWidth: 1,
    borderBottomColor: '#f8fafc',
  },
  recordHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 6,
  },
  recordDate: {
    fontSize: 12,
    fontWeight: '700',
    color: '#1e293b',
  },
  statusTag: {
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: 4,
  },
  statusTagHadir: {
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
    borderWidth: 1,
  },
  statusTagTelat: {
    backgroundColor: '#fffbeb',
    borderColor: '#fde68a',
    borderWidth: 1,
  },
  statusTagDefault: {
    backgroundColor: '#f1f5f9',
  },
  statusTagText: {
    fontSize: 9.5,
    fontWeight: '700',
  },
  statusTagTextHadir: {
    color: '#047857',
  },
  statusTagTextTelat: {
    color: '#b45309',
  },
  statusTagTextDefault: {
    color: '#64748b',
  },
  recordDetailsBox: {
    flexDirection: 'row',
    backgroundColor: 'rgba(248, 250, 252, 0.8)',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#f1f5f9',
    padding: 8,
    gap: 8,
  },
  recordDetailCol: {
    flex: 1,
  },
  recordDetailLabel: {
    fontSize: 9.5,
    color: '#94a3b8',
  },
  recordDetailValue: {
    fontSize: 11,
    fontWeight: '700',
    color: '#1e293b',
    fontFamily: 'monospace',
    marginTop: 1,
  },
});
