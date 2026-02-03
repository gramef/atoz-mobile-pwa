import { View, Text, StyleSheet, ScrollView, Pressable, Switch, Alert } from 'react-native';
import { router } from 'expo-router';
import { useState, useEffect } from 'react';
import { useTheme, spacing, radius, ThemeMode } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import {
    isBiometricAvailable,
    isBiometricEnabled,
    getBiometricType,
    enableBiometricLogin,
    disableBiometricLogin,
    getStoredCredentials,
} from '../../src/services/biometric';
import { useAuthStore } from '../../src/state/auth';

export default function SettingsScreen() {
    const { colors, mode, isDark, setMode } = useTheme();
    const token = useAuthStore((s: any) => s.token);
    const user = useAuthStore((s: any) => s.user);

    const [pushNotifications, setPushNotifications] = useState(true);
    const [emailNotifications, setEmailNotifications] = useState(true);
    const [jobAlerts, setJobAlerts] = useState(true);
    const [biometricAvailable, setBiometricAvailable] = useState(false);
    const [biometricEnabled, setBiometricEnabled] = useState(false);
    const [biometricType, setBiometricType] = useState('Biometric');

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

    async function toggleBiometric(value: boolean) {
        if (value) {
            // Enable biometric
            if (user?.email && token) {
                const success = await enableBiometricLogin(user.email, token);
                if (success) {
                    setBiometricEnabled(true);
                    Alert.alert('Success', `${biometricType} login enabled`);
                }
            } else {
                Alert.alert('Error', 'Please log in again to enable biometric login');
            }
        } else {
            // Disable biometric
            const success = await disableBiometricLogin();
            if (success) {
                setBiometricEnabled(false);
                Alert.alert('Disabled', `${biometricType} login disabled`);
            }
        }
    }

    const themeOptions: { label: string; value: ThemeMode; icon: string }[] = [
        { label: 'Light', value: 'light', icon: 'sunny-outline' },
        { label: 'Dark', value: 'dark', icon: 'moon-outline' },
        { label: 'System', value: 'system', icon: 'phone-portrait-outline' },
    ];

    const styles = createStyles(colors);

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Settings</Text>
            </View>

            <ScrollView style={styles.content}>
                {/* Appearance Section */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Appearance</Text>

                    <View style={styles.themeSelector}>
                        {themeOptions.map((option) => (
                            <Pressable
                                key={option.value}
                                style={[
                                    styles.themeOption,
                                    mode === option.value && styles.themeOptionActive,
                                ]}
                                onPress={() => setMode(option.value)}
                            >
                                <Ionicons
                                    name={option.icon as any}
                                    size={24}
                                    color={mode === option.value ? colors.onPrimary : colors.text}
                                />
                                <Text
                                    style={[
                                        styles.themeOptionText,
                                        mode === option.value && styles.themeOptionTextActive,
                                    ]}
                                >
                                    {option.label}
                                </Text>
                            </Pressable>
                        ))}
                    </View>
                </View>

                {/* Notifications Section */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Notifications</Text>

                    <View style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="notifications-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Push Notifications</Text>
                                <Text style={styles.settingDescription}>Receive push notifications for important updates</Text>
                            </View>
                        </View>
                        <Switch
                            value={pushNotifications}
                            onValueChange={setPushNotifications}
                            trackColor={{ false: colors.border, true: colors.primary }}
                            thumbColor="#fff"
                        />
                    </View>

                    <View style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="mail-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Email Notifications</Text>
                                <Text style={styles.settingDescription}>Receive email updates and summaries</Text>
                            </View>
                        </View>
                        <Switch
                            value={emailNotifications}
                            onValueChange={setEmailNotifications}
                            trackColor={{ false: colors.border, true: colors.primary }}
                            thumbColor="#fff"
                        />
                    </View>

                    <View style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="briefcase-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Job Alerts</Text>
                                <Text style={styles.settingDescription}>Get notified about new job opportunities</Text>
                            </View>
                        </View>
                        <Switch
                            value={jobAlerts}
                            onValueChange={setJobAlerts}
                            trackColor={{ false: colors.border, true: colors.primary }}
                            thumbColor="#fff"
                        />
                    </View>
                </View>

                {/* Preferences Section */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Preferences</Text>

                    <Pressable style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="language-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Language</Text>
                                <Text style={styles.settingDescription}>English</Text>
                            </View>
                        </View>
                        <Ionicons name="chevron-forward" size={20} color={colors.subtext} />
                    </Pressable>
                </View>

                {/* Privacy Section */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Privacy & Security</Text>

                    {/* Biometric Login Toggle */}
                    {biometricAvailable && (
                        <View style={styles.settingRow}>
                            <View style={styles.settingInfo}>
                                <Ionicons
                                    name={biometricType === 'Face ID' ? 'scan-outline' : 'finger-print-outline'}
                                    size={22}
                                    color={colors.primary}
                                />
                                <View style={styles.settingText}>
                                    <Text style={styles.settingLabel}>{biometricType} Login</Text>
                                    <Text style={styles.settingDescription}>Use {biometricType} for quick login</Text>
                                </View>
                            </View>
                            <Switch
                                value={biometricEnabled}
                                onValueChange={toggleBiometric}
                                trackColor={{ false: colors.border, true: colors.primary }}
                                thumbColor="#fff"
                            />
                        </View>
                    )}

                    <Pressable style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="lock-closed-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Privacy Policy</Text>
                            </View>
                        </View>
                        <Ionicons name="chevron-forward" size={20} color={colors.subtext} />
                    </Pressable>

                    <Pressable style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="document-text-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>Terms of Service</Text>
                            </View>
                        </View>
                        <Ionicons name="chevron-forward" size={20} color={colors.subtext} />
                    </Pressable>
                </View>

                {/* About Section */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>About</Text>

                    <View style={styles.settingRow}>
                        <View style={styles.settingInfo}>
                            <Ionicons name="information-circle-outline" size={22} color={colors.primary} />
                            <View style={styles.settingText}>
                                <Text style={styles.settingLabel}>App Version</Text>
                                <Text style={styles.settingDescription}>1.0.0</Text>
                            </View>
                        </View>
                    </View>
                </View>
            </ScrollView>
        </View>
    );
}

// Dynamic styles based on theme colors
const createStyles = (colors: any) => StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
        gap: spacing.md,
    },
    headerBack: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: '600',
        color: colors.text,
    },
    content: {
        flex: 1,
    },
    section: {
        marginTop: spacing.lg,
        paddingHorizontal: spacing.lg,
    },
    sectionTitle: {
        fontSize: 14,
        fontWeight: '700',
        color: colors.subtext,
        textTransform: 'uppercase',
        marginBottom: spacing.md,
        letterSpacing: 0.5,
    },
    themeSelector: {
        flexDirection: 'row',
        gap: spacing.sm,
        marginBottom: spacing.md,
    },
    themeOption: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.sm,
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        borderWidth: 2,
        borderColor: colors.border,
        gap: spacing.xs,
    },
    themeOptionActive: {
        backgroundColor: colors.primary,
        borderColor: colors.primary,
    },
    themeOptionText: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    themeOptionTextActive: {
        color: colors.onPrimary,
    },
    settingRow: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.lg,
        marginBottom: spacing.sm,
    },
    settingInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.md,
        flex: 1,
    },
    settingText: {
        flex: 1,
    },
    settingLabel: {
        fontSize: 16,
        fontWeight: '500',
        color: colors.text,
        marginBottom: 2,
    },
    settingDescription: {
        fontSize: 14,
        color: colors.subtext,
    },
});
