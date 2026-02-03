import { useState, useEffect } from 'react';
import { View, Text, ActivityIndicator, Alert, Image, Linking, Platform, KeyboardAvoidingView, ScrollView, Pressable, StyleSheet } from 'react-native';
import { router } from 'expo-router';
import { login, me } from '../src/api/client';
import { useAuthStore } from '../src/state/auth';
import Screen from '../src/ui/components/Screen';
import { WEB_BASE_URL } from '../src/config';
import Input from '../src/ui/components/Input';
import { useTheme, spacing, typography, radius } from '../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import {
  isBiometricAvailable,
  isBiometricEnabled,
  getBiometricType,
  authenticateWithBiometrics,
  getStoredCredentials,
  enableBiometricLogin,
} from '../src/services/biometric';

export default function Login() {
  const { colors } = useTheme();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [show, setShow] = useState(false);
  const [biometricAvailable, setBiometricAvailable] = useState(false);
  const [biometricEnabled, setBiometricEnabled] = useState(false);
  const [biometricType, setBiometricType] = useState('Biometric');
  const setAuth = useAuthStore((s: any) => s.setAuth);

  // Check biometric availability on mount
  useEffect(() => {
    checkBiometric();
  }, []);

  async function checkBiometric() {
    const available = await isBiometricAvailable();
    setBiometricAvailable(available);

    if (available) {
      const type = await getBiometricType();
      setBiometricType(type);

      const enabled = await isBiometricEnabled();
      setBiometricEnabled(enabled);
    }
  }

  async function onSubmit() {
    if (!email || !password) return;
    setLoading(true);
    try {
      const res = await login(email, password);
      const token = res.data.token || res.data.access_token;
      const m = await me(token);
      await setAuth(token, m.data);

      // Offer to enable biometric login after successful login
      if (biometricAvailable && !biometricEnabled) {
        Alert.alert(
          `Enable ${biometricType}?`,
          `Would you like to use ${biometricType} for faster login next time?`,
          [
            { text: 'Not Now', style: 'cancel', onPress: () => router.replace('/(tabs)') },
            {
              text: 'Enable',
              onPress: async () => {
                await enableBiometricLogin(email, token);
                router.replace('/(tabs)');
              }
            },
          ]
        );
      } else {
        router.replace('/(tabs)');
      }
    } catch (e) {
      setLoading(false);
      try {
        const status = (e as any)?.response?.status;
        const isNetwork = (e as any)?.code === 'ERR_NETWORK' || String((e as any)?.message || '').includes('net::ERR_FAILED');
        const msg =
          status === 404
            ? 'Login endpoint not found on the backend (/api/auth/login). Ensure the Laravel app is running the API auth routes.'
            : (e as any)?.response?.data?.message || (isNetwork ? 'Network error contacting the backend. Please ensure the server is running.' : 'Please check your email and password.');
        Alert.alert('Login failed', String(msg));
      } catch { }
    }
  }

  async function onBiometricLogin() {
    const result = await authenticateWithBiometrics();

    if (result.success) {
      const credentials = await getStoredCredentials();
      if (credentials) {
        setLoading(true);
        try {
          // Verify the stored token is still valid
          const m = await me(credentials.token);
          await setAuth(credentials.token, m.data);
          router.replace('/(tabs)');
        } catch (e) {
          setLoading(false);
          Alert.alert(
            'Session Expired',
            'Please log in with your password to continue.',
            [{ text: 'OK' }]
          );
        }
      }
    } else if (result.error) {
      // Only show error if user didn't cancel
      if (!result.error.includes('cancel')) {
        Alert.alert('Authentication Failed', result.error);
      }
    }
  }

  const styles = createStyles(colors);

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
      <Screen>
        <ScrollView contentContainerStyle={{ flexGrow: 1, justifyContent: 'center' }} showsVerticalScrollIndicator={false}>
          <View style={{ alignItems: 'center', marginBottom: spacing.xl }}>
            <Image
              source={require('../assets/icon.png')}
              style={{ width: 120, height: 120, resizeMode: 'contain', borderRadius: 20 }}
            />
          </View>

          <View style={styles.card}>
            <Text style={styles.title}>Welcome back</Text>
            <Text style={styles.subtitle}>Sign in to your account</Text>

            <Input
              label="Email"
              value={email}
              onChangeText={setEmail}
              autoCapitalize="none"
              keyboardType="email-address"
              placeholder="name@company.com"
            />
            <Input
              label="Password"
              value={password}
              onChangeText={setPassword}
              secureTextEntry={!show}
              placeholder="••••••••"
              labelActionText="Forgot password?"
              onLabelAction={() => Linking.openURL(`${WEB_BASE_URL}/password/reset`)}
              endAdornment={
                <Ionicons
                  name={show ? 'eye-off' : 'eye'}
                  size={20}
                  color={colors.subtext}
                  onPress={() => setShow(!show)}
                />
              }
            />

            {loading ? (
              <ActivityIndicator color={colors.primary} style={{ marginVertical: spacing.md }} />
            ) : (
              <>
                <Pressable style={styles.button} onPress={onSubmit}>
                  <Text style={styles.buttonText}>Log In</Text>
                </Pressable>

                {/* Biometric Login Button */}
                {biometricAvailable && biometricEnabled && (
                  <Pressable style={styles.biometricButton} onPress={onBiometricLogin}>
                    <Ionicons
                      name={biometricType === 'Face ID' ? 'scan-outline' : 'finger-print-outline'}
                      size={24}
                      color={colors.primary}
                    />
                    <Text style={styles.biometricText}>Log in with {biometricType}</Text>
                  </Pressable>
                )}
              </>
            )}

            {/* Registration Link */}
            <View style={styles.registerPrompt}>
              <Text style={styles.registerText}>Don't have an account? </Text>
              <Pressable onPress={() => router.push('/register/role')}>
                <Text style={styles.registerLink}>Sign Up</Text>
              </Pressable>
            </View>

            <View style={styles.divider}>
              <View style={styles.dividerLine} />
              <Text style={styles.dividerText}>OR CONTINUE WITH</Text>
              <View style={styles.dividerLine} />
            </View>

            <View style={styles.socialButtons}>
              <SocialButton icon="logo-google" onPress={() => Linking.openURL(`${WEB_BASE_URL}/login/google`)} colors={colors} />
              <SocialButton icon="logo-apple" onPress={() => Linking.openURL(`${WEB_BASE_URL}/login/apple`)} colors={colors} />
              <SocialButton icon="logo-linkedin" onPress={() => Linking.openURL(`${WEB_BASE_URL}/login/linkedin`)} colors={colors} />
            </View>
          </View>

          <View style={styles.footerPrompt}>
            <Text style={styles.footerText}>Don't have an account? </Text>
            <Text style={styles.footerLink} onPress={() => Linking.openURL('https://atozinterpreting.com/get-a-quote')}>Sign Up</Text>
          </View>
        </ScrollView>
      </Screen>
    </KeyboardAvoidingView>
  );
}

function SocialButton({ icon, onPress, colors }: { icon: any; onPress: () => void; colors: any }) {
  return (
    <Pressable
      onPress={onPress}
      style={{
        backgroundColor: colors.surface,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: radius.md,
        padding: spacing.md,
        width: 50,
        height: 50,
        alignItems: 'center',
        justifyContent: 'center',
      }}
    >
      <Ionicons name={icon} size={24} color={colors.text} />
    </Pressable>
  );
}

const createStyles = (colors: any) => StyleSheet.create({
  card: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.xl,
    shadowColor: '#000000',
    shadowOpacity: 0.08,
    shadowRadius: 16,
    shadowOffset: { width: 0, height: 8 },
    elevation: 3,
  },
  title: {
    ...typography.title,
    color: colors.text,
    textAlign: 'center',
    marginBottom: spacing.sm,
  },
  subtitle: {
    ...typography.hint,
    color: colors.subtext,
    textAlign: 'center',
    marginBottom: spacing.lg,
  },
  button: {
    backgroundColor: colors.primary,
    paddingVertical: 16,
    borderRadius: radius.lg,
    alignItems: 'center',
    marginTop: spacing.md,
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  biometricButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: spacing.sm,
    paddingVertical: 16,
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.primary,
    marginTop: spacing.md,
  },
  biometricText: {
    color: colors.primary,
    fontSize: 16,
    fontWeight: '600',
  },
  registerPrompt: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: spacing.lg,
  },
  registerText: {
    color: colors.subtext,
    fontSize: 14,
  },
  registerLink: {
    color: colors.primary,
    fontSize: 14,
    fontWeight: '600',
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: spacing.lg,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: colors.border,
  },
  dividerText: {
    marginHorizontal: spacing.md,
    color: colors.subtext,
    fontSize: 12,
  },
  socialButtons: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: spacing.md,
  },
  footerPrompt: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: spacing.xl,
  },
  footerText: {
    ...typography.hint,
    color: colors.subtext,
  },
  footerLink: {
    ...typography.hint,
    color: colors.primary,
    fontWeight: '600',
  },
});
