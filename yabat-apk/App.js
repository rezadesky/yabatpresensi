import React, { useRef, useState, useEffect } from 'react';
import { 
  StyleSheet, 
  BackHandler, 
  Platform, 
  View,
  StatusBar,
  Image,
  Text,
  Animated
} from 'react-native';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';
import { WebView } from 'react-native-webview';

const WEB_URL = 'https://yabatpresensi.stkip-us.ac.id';

function MainScreen() {
  const webViewRef = useRef(null);
  const [canGoBack, setCanGoBack] = useState(false);
  const [isInitialLoading, setIsInitialLoading] = useState(true);
  const fadeAnim = useRef(new Animated.Value(1)).current;

  // Tombol kembali fisik Android
  useEffect(() => {
    if (Platform.OS === 'android') {
      const backAction = () => {
        if (canGoBack && webViewRef.current) {
          webViewRef.current.goBack();
          return true;
        }
        return false;
      };

      const backHandler = BackHandler.addEventListener(
        'hardwareBackPress',
        backAction
      );

      return () => backHandler.remove();
    }
  }, [canGoBack]);

  const handleFinishInitialLoad = () => {
    Animated.timing(fadeAnim, {
      toValue: 0,
      duration: 300,
      useNativeDriver: true,
    }).start(() => {
      setIsInitialLoading(false);
    });
  };

  return (
    <SafeAreaView style={styles.safeArea} edges={['top']}>
      <StatusBar 
        barStyle="light-content" 
        backgroundColor="#0f172a" 
        translucent={false} 
      />

      <View style={styles.container}>
        <WebView
          ref={webViewRef}
          source={{ uri: WEB_URL }}
          style={styles.webview}
          javaScriptEnabled={true}
          domStorageEnabled={true}
          geolocationEnabled={true}
          allowFileAccess={true}
          allowFileAccessFromFileURLs={true}
          allowUniversalAccessFromFileURLs={true}
          cacheEnabled={true}
          pullToRefreshEnabled={false}
          onNavigationStateChange={(navState) => {
            setCanGoBack(navState.canGoBack);
          }}
          onLoadEnd={() => {
            if (isInitialLoading) {
              handleFinishInitialLoad();
            }
          }}
          // Optimasi Performa & Smooth Rendering Hardware
          androidLayerType="hardware"
          androidHardwareAccelerationDisabled={false}
          renderToHardwareTextureAndroid={true}
          overScrollMode="never"
          showsVerticalScrollIndicator={false}
          showsHorizontalScrollIndicator={false}
          originWhitelist={['*']}
          mixedContentMode="always"
        />

        {/* Splash Screen Eksklusif dengan Logo No Background (Transparan) */}
        {isInitialLoading && (
          <Animated.View style={[styles.splashOverlay, { opacity: fadeAnim }]}>
            <Image 
              source={require('./assets/splash-transparent.png')} 
              style={styles.logoImage} 
              resizeMode="contain"
            />
            <Text style={styles.brandTitle}>YABAT PRESENSI</Text>
            <Text style={styles.brandSubtitle}>Yayasan Anak Bangsa Aceh Tenggara</Text>

            <View style={styles.loadingBarTrack}>
              <View style={styles.loadingBarFill} />
            </View>
          </Animated.View>
        )}
      </View>
    </SafeAreaView>
  );
}

export default function App() {
  return (
    <SafeAreaProvider>
      <MainScreen />
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  webview: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  splashOverlay: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0f172a',
    zIndex: 9999,
  },
  logoImage: {
    width: 130,
    height: 130,
    marginBottom: 20,
  },
  brandTitle: {
    color: '#ffffff',
    fontSize: 20,
    fontWeight: '800',
    letterSpacing: 2,
    marginBottom: 6,
  },
  brandSubtitle: {
    color: '#93c5fd',
    fontSize: 12,
    fontWeight: '500',
    letterSpacing: 0.5,
    marginBottom: 28,
  },
  loadingBarTrack: {
    width: 140,
    height: 3,
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    borderRadius: 2,
    overflow: 'hidden',
  },
  loadingBarFill: {
    width: '60%',
    height: '100%',
    backgroundColor: '#3b82f6',
    borderRadius: 2,
  },
});
