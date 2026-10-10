import React from 'react';
import { View, Text, StyleSheet, Image } from 'react-native';
import { getInitials } from '../utils/helpers';

export default function MobileHeader({ employee }) {
  const initials = getInitials(employee?.name);

  return (
    <View style={styles.header}>
      {/* Decorative Glow Circles */}
      <View style={styles.glowTopRight} />
      <View style={styles.glowBottomLeft} />

      <View style={styles.headerContent}>
        {/* Brand Bar */}
        <View style={styles.brandRow}>
          <Image 
            source={require('../../assets/splash-transparent.png')} 
            style={styles.brandLogo} 
            resizeMode="contain" 
          />
          <View style={styles.brandTextContainer}>
            <Text style={styles.brandTitle}>YABAT PRESENSI</Text>
            <Text style={styles.brandSubtitle}>Portal Presensi Yayasan Anak Bangsa Aceh Tenggara</Text>
          </View>
        </View>

        {/* Pegawai Info Card (Terkunci Sesuai Akun Pribadi) */}
        <View style={styles.employeeCard}>
          <View style={styles.empInfoLeft}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>{initials}</Text>
            </View>
            <View style={styles.empDetails}>
              <Text style={styles.empName} numberOfLines={1}>
                {employee?.name || 'Pegawai Yayasan'}
              </Text>
              <Text style={styles.empPosition} numberOfLines={1}>
                {employee?.position || 'Tenaga Pendidik / Staf'}
              </Text>
              <Text style={styles.empNip} numberOfLines={1}>
                NIP. {employee?.nip_nidn || '-'}
              </Text>
            </View>
          </View>

          <View style={styles.empInfoRight}>
            <Text style={styles.institutionName} numberOfLines={2}>
              {employee?.institution?.name || 'Yayasan'}
            </Text>
            <Text style={styles.foundationName}>Yayasan Anak Bangsa</Text>
          </View>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    backgroundColor: '#090d16',
    paddingHorizontal: 20,
    paddingTop: 16,
    paddingBottom: 20,
    borderBottomLeftRadius: 28,
    borderBottomRightRadius: 28,
    position: 'relative',
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 8,
  },
  glowTopRight: {
    position: 'absolute',
    top: -50,
    right: -50,
    width: 160,
    height: 160,
    borderRadius: 80,
    backgroundColor: 'rgba(37, 99, 235, 0.22)',
  },
  glowBottomLeft: {
    position: 'absolute',
    bottom: -40,
    left: -40,
    width: 140,
    height: 140,
    borderRadius: 70,
    backgroundColor: 'rgba(14, 165, 233, 0.16)',
  },
  headerContent: {
    zIndex: 10,
  },
  brandRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
  },
  brandLogo: {
    width: 36,
    height: 36,
    marginRight: 10,
  },
  brandTextContainer: {
    flex: 1,
  },
  brandTitle: {
    color: '#ffffff',
    fontSize: 14,
    fontWeight: '800',
    letterSpacing: 1,
    lineHeight: 18,
  },
  brandSubtitle: {
    color: '#93c5fd',
    fontSize: 10,
    fontWeight: '500',
    lineHeight: 14,
    marginTop: 1,
  },
  employeeCard: {
    backgroundColor: 'rgba(30, 41, 59, 0.75)',
    borderWidth: 1,
    borderColor: 'rgba(71, 85, 105, 0.6)',
    borderRadius: 16,
    padding: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  empInfoLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
    paddingRight: 8,
  },
  avatar: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: 'rgba(37, 99, 235, 0.3)',
    borderWidth: 1,
    borderColor: 'rgba(59, 130, 246, 0.4)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  avatarText: {
    color: '#93c5fd',
    fontSize: 14,
    fontWeight: '800',
  },
  empDetails: {
    flex: 1,
  },
  empName: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
    lineHeight: 16,
  },
  empPosition: {
    color: '#cbd5e1',
    fontSize: 11,
    lineHeight: 15,
  },
  empNip: {
    color: '#94a3b8',
    fontSize: 10,
    fontFamily: 'monospace',
    marginTop: 1,
  },
  empInfoRight: {
    alignItems: 'flex-end',
    maxWidth: 120,
    paddingLeft: 4,
  },
  institutionName: {
    color: '#93c5fd',
    fontSize: 11,
    fontWeight: '600',
    textAlign: 'right',
    lineHeight: 14,
  },
  foundationName: {
    color: '#94a3b8',
    fontSize: 9.5,
    marginTop: 2,
    textAlign: 'right',
  },
});
