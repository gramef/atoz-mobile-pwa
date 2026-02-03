/**
 * PWA Install Prompt Component
 * Shows a banner prompting users to install the app on their device
 */

import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Platform, Animated, Image } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const PROMPT_DISMISSED_KEY = 'pwa_install_dismissed';
const PROMPT_DELAY_MS = 2000; // Show after 2 seconds

interface BeforeInstallPromptEvent extends Event {
    prompt: () => Promise<void>;
    userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

export function InstallPrompt() {
    const [showPrompt, setShowPrompt] = useState(false);
    const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null);
    const [isIOS, setIsIOS] = useState(false);
    const fadeAnim = useState(new Animated.Value(0))[0];

    useEffect(() => {
        // Only run on web
        if (Platform.OS !== 'web') return;

        // Check if iOS Safari
        const ua = navigator.userAgent;
        const isIOSSafari = /iPhone|iPad|iPod/.test(ua) && !/(Chrome|CriOS|FxiOS)/.test(ua);
        setIsIOS(isIOSSafari);

        // Check if already installed (standalone mode)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
            (window.navigator as any).standalone === true;

        if (isStandalone) return;

        // Check if dismissed recently
        AsyncStorage.getItem(PROMPT_DISMISSED_KEY).then((dismissed) => {
            if (dismissed) {
                const dismissedDate = new Date(dismissed);
                const daysSinceDismissed = (Date.now() - dismissedDate.getTime()) / (1000 * 60 * 60 * 24);
                if (daysSinceDismissed < 7) return; // Don't show for 7 days after dismissal
            }

            // Listen for beforeinstallprompt (Android/Chrome)
            const handleBeforeInstall = (e: Event) => {
                e.preventDefault();
                setDeferredPrompt(e as BeforeInstallPromptEvent);
                setTimeout(() => {
                    setShowPrompt(true);
                    Animated.timing(fadeAnim, {
                        toValue: 1,
                        duration: 300,
                        useNativeDriver: true,
                    }).start();
                }, PROMPT_DELAY_MS);
            };

            window.addEventListener('beforeinstallprompt', handleBeforeInstall);

            // For iOS, show custom prompt after delay
            if (isIOSSafari) {
                setTimeout(() => {
                    setShowPrompt(true);
                    Animated.timing(fadeAnim, {
                        toValue: 1,
                        duration: 300,
                        useNativeDriver: true,
                    }).start();
                }, PROMPT_DELAY_MS);
            }

            return () => {
                window.removeEventListener('beforeinstallprompt', handleBeforeInstall);
            };
        });
    }, []);

    const handleInstall = useCallback(async () => {
        if (deferredPrompt) {
            // Android/Chrome - trigger native prompt
            await deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                setShowPrompt(false);
            }
            setDeferredPrompt(null);
        }
    }, [deferredPrompt]);

    const handleDismiss = useCallback(() => {
        Animated.timing(fadeAnim, {
            toValue: 0,
            duration: 200,
            useNativeDriver: true,
        }).start(() => {
            setShowPrompt(false);
            AsyncStorage.setItem(PROMPT_DISMISSED_KEY, new Date().toISOString());
        });
    }, []);

    if (!showPrompt) return null;

    return (
        <Animated.View style={[styles.overlay, { opacity: fadeAnim }]}>
            <View style={styles.modal}>
                <View style={styles.header}>
                    <Image
                        source={require('../../assets/icon.png')}
                        style={styles.icon}
                        resizeMode="contain"
                    />
                    <View style={styles.headerText}>
                        <Text style={styles.title}>Install AtoZ App</Text>
                        <Text style={styles.subtitle}>Add to your home screen for quick access</Text>
                    </View>
                    <TouchableOpacity onPress={handleDismiss} style={styles.closeButton}>
                        <Text style={styles.closeText}>✕</Text>
                    </TouchableOpacity>
                </View>

                <View style={styles.body}>
                    {isIOS ? (
                        <View style={styles.iosInstructions}>
                            <Text style={styles.instructionText}>
                                To install this app on your iPhone:
                            </Text>
                            <View style={styles.step}>
                                <Text style={styles.stepNumber}>1</Text>
                                <Text style={styles.stepText}>
                                    Tap the <Text style={styles.bold}>Share</Text> button{' '}
                                    <Text style={styles.shareIcon}>⬆️</Text> at the bottom
                                </Text>
                            </View>
                            <View style={styles.step}>
                                <Text style={styles.stepNumber}>2</Text>
                                <Text style={styles.stepText}>
                                    Scroll down and tap <Text style={styles.bold}>"Add to Home Screen"</Text>
                                </Text>
                            </View>
                            <View style={styles.step}>
                                <Text style={styles.stepNumber}>3</Text>
                                <Text style={styles.stepText}>
                                    Tap <Text style={styles.bold}>"Add"</Text> in the top right
                                </Text>
                            </View>
                        </View>
                    ) : (
                        <View style={styles.benefits}>
                            <View style={styles.benefit}>
                                <Text style={styles.benefitIcon}>⚡</Text>
                                <Text style={styles.benefitText}>Fast access from home screen</Text>
                            </View>
                            <View style={styles.benefit}>
                                <Text style={styles.benefitIcon}>📱</Text>
                                <Text style={styles.benefitText}>Works like a native app</Text>
                            </View>
                            <View style={styles.benefit}>
                                <Text style={styles.benefitIcon}>🔔</Text>
                                <Text style={styles.benefitText}>Get job notifications</Text>
                            </View>
                        </View>
                    )}
                </View>

                <View style={styles.footer}>
                    {isIOS ? (
                        <TouchableOpacity onPress={handleDismiss} style={styles.gotItButton}>
                            <Text style={styles.gotItText}>Got it!</Text>
                        </TouchableOpacity>
                    ) : (
                        <>
                            <TouchableOpacity onPress={handleDismiss} style={styles.laterButton}>
                                <Text style={styles.laterText}>Maybe Later</Text>
                            </TouchableOpacity>
                            <TouchableOpacity onPress={handleInstall} style={styles.installButton}>
                                <Text style={styles.installText}>Install Now</Text>
                            </TouchableOpacity>
                        </>
                    )}
                </View>
            </View>
        </Animated.View>
    );
}

const styles = StyleSheet.create({
    overlay: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: 'rgba(0, 0, 0, 0.6)',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 9999,
        padding: 20,
    },
    modal: {
        backgroundColor: '#fff',
        borderRadius: 16,
        width: '100%',
        maxWidth: 360,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.3,
        shadowRadius: 20,
        elevation: 20,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        padding: 16,
        borderBottomWidth: 1,
        borderBottomColor: '#f0f0f0',
    },
    icon: {
        width: 48,
        height: 48,
        borderRadius: 12,
    },
    headerText: {
        flex: 1,
        marginLeft: 12,
    },
    title: {
        fontSize: 18,
        fontWeight: '700',
        color: '#1a1a1a',
    },
    subtitle: {
        fontSize: 13,
        color: '#666',
        marginTop: 2,
    },
    closeButton: {
        padding: 8,
    },
    closeText: {
        fontSize: 18,
        color: '#999',
    },
    body: {
        padding: 16,
    },
    iosInstructions: {
        gap: 12,
    },
    instructionText: {
        fontSize: 15,
        color: '#333',
        marginBottom: 8,
    },
    step: {
        flexDirection: 'row',
        alignItems: 'flex-start',
        gap: 12,
    },
    stepNumber: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: '#0d6efd',
        color: '#fff',
        fontSize: 14,
        fontWeight: '600',
        textAlign: 'center',
        lineHeight: 24,
    },
    stepText: {
        flex: 1,
        fontSize: 14,
        color: '#444',
        lineHeight: 22,
    },
    bold: {
        fontWeight: '600',
        color: '#0d6efd',
    },
    shareIcon: {
        fontSize: 14,
    },
    benefits: {
        gap: 12,
    },
    benefit: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 12,
    },
    benefitIcon: {
        fontSize: 20,
    },
    benefitText: {
        fontSize: 14,
        color: '#444',
    },
    footer: {
        flexDirection: 'row',
        padding: 16,
        gap: 12,
        borderTopWidth: 1,
        borderTopColor: '#f0f0f0',
    },
    laterButton: {
        flex: 1,
        paddingVertical: 12,
        borderRadius: 8,
        backgroundColor: '#f5f5f5',
        alignItems: 'center',
    },
    laterText: {
        fontSize: 15,
        color: '#666',
        fontWeight: '500',
    },
    installButton: {
        flex: 1,
        paddingVertical: 12,
        borderRadius: 8,
        backgroundColor: '#0d6efd',
        alignItems: 'center',
    },
    installText: {
        fontSize: 15,
        color: '#fff',
        fontWeight: '600',
    },
    gotItButton: {
        flex: 1,
        paddingVertical: 12,
        borderRadius: 8,
        backgroundColor: '#0d6efd',
        alignItems: 'center',
    },
    gotItText: {
        fontSize: 15,
        color: '#fff',
        fontWeight: '600',
    },
});

export default InstallPrompt;
