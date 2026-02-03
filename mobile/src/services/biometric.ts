import * as LocalAuthentication from 'expo-local-authentication';
import * as SecureStore from 'expo-secure-store';

const BIOMETRIC_ENABLED_KEY = 'biometric_enabled';
const STORED_EMAIL_KEY = 'stored_email';
const STORED_TOKEN_KEY = 'stored_token';

export interface BiometricAuthResult {
    success: boolean;
    error?: string;
}

/**
 * Check if biometric authentication is available on this device
 */
export async function isBiometricAvailable(): Promise<boolean> {
    try {
        const hasHardware = await LocalAuthentication.hasHardwareAsync();
        if (!hasHardware) return false;

        const isEnrolled = await LocalAuthentication.isEnrolledAsync();
        return isEnrolled;
    } catch (error) {
        console.error('Error checking biometric availability:', error);
        return false;
    }
}

/**
 * Get the type of biometric authentication available (Face ID, Touch ID, etc.)
 */
export async function getBiometricType(): Promise<string> {
    try {
        const types = await LocalAuthentication.supportedAuthenticationTypesAsync();

        if (types.includes(LocalAuthentication.AuthenticationType.FACIAL_RECOGNITION)) {
            return 'Face ID';
        }
        if (types.includes(LocalAuthentication.AuthenticationType.FINGERPRINT)) {
            return 'Touch ID';
        }
        if (types.includes(LocalAuthentication.AuthenticationType.IRIS)) {
            return 'Iris';
        }
        return 'Biometric';
    } catch (error) {
        return 'Biometric';
    }
}

/**
 * Authenticate using biometrics
 */
export async function authenticateWithBiometrics(): Promise<BiometricAuthResult> {
    try {
        const result = await LocalAuthentication.authenticateAsync({
            promptMessage: 'Log in to AtoZ',
            fallbackLabel: 'Use Password',
            cancelLabel: 'Cancel',
            disableDeviceFallback: false,
        });

        if (result.success) {
            return { success: true };
        }

        return {
            success: false,
            error: result.error || 'Authentication failed'
        };
    } catch (error) {
        return {
            success: false,
            error: 'Biometric authentication error'
        };
    }
}

/**
 * Check if biometric login is enabled for this user
 */
export async function isBiometricEnabled(): Promise<boolean> {
    try {
        const enabled = await SecureStore.getItemAsync(BIOMETRIC_ENABLED_KEY);
        return enabled === 'true';
    } catch (error) {
        return false;
    }
}

/**
 * Enable biometric login and store credentials securely
 */
export async function enableBiometricLogin(email: string, token: string): Promise<boolean> {
    try {
        await SecureStore.setItemAsync(BIOMETRIC_ENABLED_KEY, 'true');
        await SecureStore.setItemAsync(STORED_EMAIL_KEY, email);
        await SecureStore.setItemAsync(STORED_TOKEN_KEY, token);
        return true;
    } catch (error) {
        console.error('Error enabling biometric login:', error);
        return false;
    }
}

/**
 * Disable biometric login and clear stored credentials
 */
export async function disableBiometricLogin(): Promise<boolean> {
    try {
        await SecureStore.deleteItemAsync(BIOMETRIC_ENABLED_KEY);
        await SecureStore.deleteItemAsync(STORED_EMAIL_KEY);
        await SecureStore.deleteItemAsync(STORED_TOKEN_KEY);
        return true;
    } catch (error) {
        console.error('Error disabling biometric login:', error);
        return false;
    }
}

/**
 * Get stored credentials for biometric login
 */
export async function getStoredCredentials(): Promise<{ email: string; token: string } | null> {
    try {
        const email = await SecureStore.getItemAsync(STORED_EMAIL_KEY);
        const token = await SecureStore.getItemAsync(STORED_TOKEN_KEY);

        if (email && token) {
            return { email, token };
        }
        return null;
    } catch (error) {
        return null;
    }
}
