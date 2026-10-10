import React from 'react';
import { View, Text, StyleSheet, Platform } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';

import BerandaScreen from '../screens/BerandaScreen';
import PresensiScreen from '../screens/PresensiScreen';
import RiwayatScreen from '../screens/RiwayatScreen';
import ProfilScreen from '../screens/ProfilScreen';

const Tab = createBottomTabNavigator();

export default function MainTabNavigator() {
  const insets = useSafeAreaInsets();
  
  // Dynamic bottom padding to handle Android gesture navigation & 3-button navigation bar
  const bottomPadding = Math.max(insets.bottom, 12);
  const tabHeight = 60 + bottomPadding;

  return (
    <Tab.Navigator
      initialRouteName="Beranda"
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: '#2563eb',
        tabBarInactiveTintColor: '#94a3b8',
        tabBarStyle: {
          backgroundColor: '#ffffff',
          borderTopWidth: 1,
          borderTopColor: '#e2e8f0',
          height: tabHeight,
          paddingBottom: bottomPadding,
          paddingTop: 8,
          elevation: 16,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -3 },
          shadowOpacity: 0.08,
          shadowRadius: 6,
        },
        tabBarLabelStyle: {
          fontSize: 10,
          fontWeight: '700',
          marginTop: -2,
        },
        tabBarIcon: ({ focused, color, size }) => {
          let iconName;
          if (route.name === 'Beranda') {
            iconName = focused ? 'home' : 'home-outline';
          } else if (route.name === 'Presensi') {
            iconName = focused ? 'location' : 'location-outline';
          } else if (route.name === 'Riwayat') {
            iconName = focused ? 'time' : 'time-outline';
          } else if (route.name === 'Profil') {
            iconName = focused ? 'person' : 'person-outline';
          }

          return (
            <View style={styles.iconContainer}>
              <Ionicons name={iconName} size={22} color={color} />
              {focused && <View style={styles.activeDot} />}
            </View>
          );
        },
      })}
    >
      <Tab.Screen 
        name="Beranda" 
        component={BerandaScreen} 
        options={{ tabBarLabel: 'Beranda' }}
      />
      <Tab.Screen 
        name="Presensi" 
        component={PresensiScreen} 
        options={{ tabBarLabel: 'Presensi' }}
      />
      <Tab.Screen 
        name="Riwayat" 
        component={RiwayatScreen} 
        options={{ tabBarLabel: 'Riwayat' }}
      />
      <Tab.Screen 
        name="Profil" 
        component={ProfilScreen} 
        options={{ tabBarLabel: 'Profil' }}
      />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  iconContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    position: 'relative',
    height: 28,
  },
  activeDot: {
    position: 'absolute',
    bottom: -4,
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: '#2563eb',
  },
});
