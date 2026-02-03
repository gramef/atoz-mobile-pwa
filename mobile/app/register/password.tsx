
import { View, Text, StyleSheet, Pressable, TextInput, ScrollView, KeyboardAvoidingView, Platform, ActivityIndicator } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import Screen from '../../src/ui/components/Screen';
import { register } from '../../src/api/client';
import { useAuthStore } from '../../src/state/auth';

export default function PasswordCreationScreen() {
    const params = useLocalSearchParams();

    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(false);

    const validatePassword = () => {
        const newErrors: Record<string, string> = {};

        if (!password) {
            newErrors.password = 'Password is required';
        } else if (password.length < 8) {
            newErrors.password = 'Password must be at least 8 characters';
        }

        if (!confirmPassword) {
            newErrors.confirmPassword = 'Please confirm your password';
        } else if (password !== confirmPassword) {
            newErrors.confirmPassword = 'Passwords do not match';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleCreateAccount = async () => {
        if (!validatePassword()) return;

        setLoading(true);
        try {
            // Call registration API
            const response = await register({
                firstName: params.firstName as string,
                lastName: params.lastName as string,
                email: params.email as string,
                password: password,
                role: params.role as 'agent' | 'client',
                phone: params.phone as string,
            });

            // Save token and user data
            const { token, user } = response.data;
            await useAuthStore.getState().setAuth(token, user);

            // Navigate to success
            router.replace('/register/success');
        } catch (error: any) {
            console.error('Registration error:', error);
            setErrors({
                general: error.response?.data?.message || 'Registration failed. Please try again.'
            });
        } finally {
            setLoading(false);
        }
    };

    const passwordStrength = () => {
        if (!password) return { text: '', color: '' };
        if (password.length < 6) return { text: 'Weak', color: colors.danger };
        if (password.length < 10) return { text: 'Medium', color: colors.accent };
        return { text: 'Strong', color: colors.secondary };
    };

    const strength = passwordStrength();

    return (
        <Screen>
            <KeyboardAvoidingView
                behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
                style={{ flex: 1 }}
            >
                <ScrollView contentContainerStyle={styles.container}>
                    {/* Header */}
                    <View style={styles.header}>
                        <Pressable onPress={() => router.back()} style={styles.backButton}>
                            <Ionicons name="arrow-back" size={24} color={colors.text} />
                        </Pressable>
                    </View>

                    {/* Title */}
                    <Text style={styles.title}>Create Password</Text>
                    <Text style={styles.subtitle}>
                        Choose a strong password to secure your account
                    </Text>

                    {/* Form */}
                    <View style={styles.form}>
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Password</Text>
                            <View style={styles.passwordContainer}>
                                <TextInput
                                    style={[styles.input, errors.password ? styles.inputError : null]}
                                    value={password}
                                    onChangeText={(text) => {
                                        setPassword(text);
                                        if (errors.password) setErrors({ ...errors, password: '' });
                                    }}
                                    placeholder="Enter your password"
                                    placeholderTextColor={colors.subtext}
                                    secureTextEntry={!showPassword}
                                    autoCapitalize="none"
                                />
                                <Pressable
                                    style={styles.eyeButton}
                                    onPress={() => setShowPassword(!showPassword)}
                                >
                                    <Ionicons
                                        name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                                        size={24}
                                        color={colors.subtext}
                                    />
                                </Pressable>
                            </View>
                            {errors.password && <Text style={styles.errorText}>{errors.password}</Text>}
                            {password && !errors.password && (
                                <Text style={[styles.strengthText, { color: strength.color }]}>
                                    Password strength: {strength.text}
                                </Text>
                            )}
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Confirm Password</Text>
                            <View style={styles.passwordContainer}>
                                <TextInput
                                    style={[styles.input, errors.confirmPassword ? styles.inputError : null]}
                                    value={confirmPassword}
                                    onChangeText={(text) => {
                                        setConfirmPassword(text);
                                        if (errors.confirmPassword) setErrors({ ...errors, confirmPassword: '' });
                                    }}
                                    placeholder="Re-enter your password"
                                    placeholderTextColor={colors.subtext}
                                    secureTextEntry={!showConfirmPassword}
                                    autoCapitalize="none"
                                />
                                <Pressable
                                    style={styles.eyeButton}
                                    onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                                >
                                    <Ionicons
                                        name={showConfirmPassword ? 'eye-off-outline' : 'eye-outline'}
                                        size={24}
                                        color={colors.subtext}
                                    />
                                </Pressable>
                            </View>
                            {errors.confirmPassword && <Text style={styles.errorText}>{errors.confirmPassword}</Text>}
                        </View>

                        {/* Password Requirements */}
                        <View style={styles.requirements}>
                            <Text style={styles.requirementsTitle}>Password must contain:</Text>
                            <View style={styles.requirement}>
                                <Ionicons
                                    name={password.length >= 8 ? 'checkmark-circle' : 'ellipse-outline'}
                                    size={16}
                                    color={password.length >= 8 ? colors.secondary : colors.subtext}
                                />
                                <Text style={styles.requirementText}>At least 8 characters</Text>
                            </View>
                        </View>
                    </View>

                    {/* General Error */}
                    {errors.general && (
                        <View style={styles.generalError}>
                            <Ionicons name="alert-circle" size={20} color={colors.danger} />
                            <Text style={styles.generalErrorText}>{errors.general}</Text>
                        </View>
                    )}

                    {/* Create Account Button */}
                    <Pressable
                        style={[styles.button, loading && styles.buttonDisabled]}
                        onPress={handleCreateAccount}
                        disabled={loading}
                    >
                        {loading ? (
                            <ActivityIndicator color="#fff" />
                        ) : (
                            <Text style={styles.buttonText}>Create Account</Text>
                        )}
                    </Pressable>
                </ScrollView>
            </KeyboardAvoidingView>
        </Screen>
    );
}

const styles = StyleSheet.create({
    container: {
        flexGrow: 1,
        paddingHorizontal: spacing.lg,
        paddingBottom: 40,
    },
    header: {
        paddingTop: spacing.md,
        marginBottom: spacing.lg,
    },
    backButton: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    title: {
        fontSize: 28,
        fontWeight: '700',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    subtitle: {
        fontSize: 16,
        color: colors.subtext,
        marginBottom: spacing.xl,
    },
    form: {
        gap: spacing.lg,
        marginBottom: spacing.xl,
    },
    inputGroup: {
        gap: spacing.sm,
    },
    label: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    passwordContainer: {
        position: 'relative',
    },
    input: {
        backgroundColor: colors.surface,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: radius.md,
        paddingHorizontal: spacing.md,
        paddingVertical: 14,
        paddingRight: 50,
        fontSize: 16,
        color: colors.text,
    },
    inputError: {
        borderColor: colors.danger,
    },
    eyeButton: {
        position: 'absolute',
        right: spacing.md,
        top: 14,
    },
    errorText: {
        fontSize: 12,
        color: colors.danger,
    },
    strengthText: {
        fontSize: 12,
        fontWeight: '600',
    },
    requirements: {
        backgroundColor: colors.bg,
        borderRadius: radius.md,
        padding: spacing.md,
        gap: spacing.sm,
    },
    requirementsTitle: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.xs,
    },
    requirement: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    requirementText: {
        fontSize: 14,
        color: colors.subtext,
    },
    generalError: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
        backgroundColor: '#FFEBEE',
        padding: spacing.md,
        borderRadius: radius.md,
        marginTop: spacing.md,
    },
    generalErrorText: {
        flex: 1,
        fontSize: 14,
        color: colors.danger,
    },
    button: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
    },
    buttonDisabled: {
        opacity: 0.6,
    },
    buttonText: {
        color: '#fff',
        fontSize: 18,
        fontWeight: '600',
    },
});
