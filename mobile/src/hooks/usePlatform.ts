/**
 * Platform detection hook
 * Provides utilities for detecting the current platform and PWA status
 */

import { Platform } from 'react-native';

export interface PlatformInfo {
    isWeb: boolean;
    isNative: boolean;
    isIOS: boolean;
    isAndroid: boolean;
    isPWA: boolean;
    isMobile: boolean;
    isStandalone: boolean;
}

/**
 * Check if running in PWA mode (installed on home screen)
 */
function checkIsPWA(): boolean {
    if (typeof window === 'undefined') return false;

    // Check for display-mode: standalone
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches;

    // iOS Safari adds this when installed
    const isIOSPWA = (window.navigator as any).standalone === true;

    // Check URL for PWA launch
    const urlParams = new URLSearchParams(window.location.search);
    const launchedFromPWA = urlParams.get('source') === 'pwa';

    return isStandalone || isIOSPWA || launchedFromPWA;
}

/**
 * Check if running on mobile device (via user agent)
 */
function checkIsMobile(): boolean {
    if (typeof navigator === 'undefined') return false;

    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent
    );
}

/**
 * Get comprehensive platform information
 */
export function usePlatform(): PlatformInfo {
    const isWeb = Platform.OS === 'web';
    const isNative = Platform.OS === 'ios' || Platform.OS === 'android';
    const isIOS = Platform.OS === 'ios' || (isWeb && checkIsMobile() && /iPhone|iPad|iPod/i.test(navigator.userAgent));
    const isAndroid = Platform.OS === 'android' || (isWeb && checkIsMobile() && /Android/i.test(navigator.userAgent));
    const isPWA = isWeb && checkIsPWA();
    const isMobile = isNative || (isWeb && checkIsMobile());
    const isStandalone = isPWA;

    return {
        isWeb,
        isNative,
        isIOS,
        isAndroid,
        isPWA,
        isMobile,
        isStandalone,
    };
}

/**
 * Get platform-specific feature availability
 */
export function usePlatformFeatures() {
    const platform = usePlatform();

    return {
        // Push notifications: fully supported on native, limited on web (especially iOS)
        pushNotifications: platform.isNative || (platform.isWeb && !platform.isIOS),

        // Local notifications: same as push
        localNotifications: platform.isNative || (platform.isWeb && !platform.isIOS),

        // Biometric auth: native only
        biometrics: platform.isNative,

        // Camera: available but with browser picker on web
        camera: true,

        // File picking: always available
        filePicker: true,

        // Location: available on all platforms with permission
        location: true,

        // Secure storage: native uses SecureStore, web uses localStorage
        secureStorage: platform.isNative,

        // Background sync: PWA with service worker
        backgroundSync: platform.isPWA,

        // Offline mode: PWA or native
        offlineMode: platform.isNative || platform.isPWA,

        // App install prompt: web only (native is already installed)
        installPrompt: platform.isWeb && !platform.isPWA,
    };
}

export const getPlatformFeatures = usePlatformFeatures;
