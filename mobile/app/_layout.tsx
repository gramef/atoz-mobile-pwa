import { Stack } from 'expo-router';
import { useEffect } from 'react';
import { ActivityIndicator, View } from 'react-native';
import { useAuthStore } from '../src/state/auth';
import ErrorBoundary from '../src/ui/components/ErrorBoundary';
import { ThemeProvider } from '../src/ui/theme';
import { NotificationProvider } from '../src/providers/NotificationProvider';
import InstallPrompt from '../src/components/InstallPrompt';

export default function Layout() {
  const isReady = useAuthStore((s: any) => s.isReady);
  const hydrate = useAuthStore((s: any) => s.hydrate);

  useEffect(() => {
    if (!isReady) {
      hydrate();
    }
  }, [hydrate, isReady]);

  if (!isReady) {
    return (
      <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center' }}>
        <ActivityIndicator />
      </View>
    );
  }

  return (
    <ThemeProvider>
      <NotificationProvider>
        <ErrorBoundary>
          <Stack screenOptions={{ headerShown: false }}>
            <Stack.Screen name="onboarding" />
            <Stack.Screen name="register" />
            <Stack.Screen name="(tabs)" />
            <Stack.Screen name="login" />
          </Stack>
          {/* PWA Install Prompt - shows on web when not installed */}
          <InstallPrompt />
        </ErrorBoundary>
      </NotificationProvider>
    </ThemeProvider>
  );
}

