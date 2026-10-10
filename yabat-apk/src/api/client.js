import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Base API URL ke hosting backend Laravel
export const BASE_URL = 'https://yabatpresensi.stkip-us.ac.id';

const api = axios.create({
  baseURL: `${BASE_URL}/api`,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 15000,
});

// Auto attach Sanctum bearer token jika ada
api.interceptors.request.use(async (config) => {
  try {
    const token = await AsyncStorage.getItem('user_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  } catch (error) {
    console.log('Error reading token:', error);
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

export default api;
