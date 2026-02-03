import { View, Text, StyleSheet, Pressable, TextInput, ScrollView, KeyboardAvoidingView, Platform } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import Screen from '../../src/ui/components/Screen';

export default function RegistrationDetailsScreen() {
    const params = useLocalSearchParams();
    const role = params.role as 'agent' | 'client';

    const [formData, setFormData] = useState({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        location: '',
    });

    const [errors, setErrors] = useState<Record<string, string>>({});

    const validateForm = () => {
        const newErrors: Record<string, string> = {};

        if (!formData.firstName.trim()) newErrors.firstName = 'First name is required';
        if (!formData.lastName.trim()) newErrors.lastName = 'Last name is required';
        if (!formData.email.trim()) newErrors.email = 'Email is required';
        else if (!/^\S+@\S+\.\S+$/.test(formData.email)) newErrors.email = 'Invalid email format';
        if (!formData.phone.trim()) newErrors.phone = 'Phone number is required';

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleContinue = () => {
        if (validateForm()) {
            router.push({
                pathname: '/register/password',
                params: { ...formData, role },
            });
        }
    };

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
                    <Text style={styles.title}>Tell us about yourself</Text>
                    <Text style={styles.subtitle}>
                        {role === 'agent'
                            ? 'Create your professional profile'
                            : 'We need some basic information to get started'}
                    </Text>

                    {/* Form */}
                    <View style={styles.form}>
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>First Name</Text>
                            <TextInput
                                style={[styles.input, errors.firstName ? styles.inputError : null]}
                                value={formData.firstName}
                                onChangeText={(text) => {
                                    setFormData({ ...formData, firstName: text });
                                    if (errors.firstName) setErrors({ ...errors, firstName: '' });
                                }}
                                placeholder="John"
                                placeholderTextColor={colors.subtext}
                            />
                            {errors.firstName && <Text style={styles.errorText}>{errors.firstName}</Text>}
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Last Name</Text>
                            <TextInput
                                style={[styles.input, errors.lastName ? styles.inputError : null]}
                                value={formData.lastName}
                                onChangeText={(text) => {
                                    setFormData({ ...formData, lastName: text });
                                    if (errors.lastName) setErrors({ ...errors, lastName: '' });
                                }}
                                placeholder="Doe"
                                placeholderTextColor={colors.subtext}
                            />
                            {errors.lastName && <Text style={styles.errorText}>{errors.lastName}</Text>}
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Email</Text>
                            <TextInput
                                style={[styles.input, errors.email ? styles.inputError : null]}
                                value={formData.email}
                                onChangeText={(text) => {
                                    setFormData({ ...formData, email: text });
                                    if (errors.email) setErrors({ ...errors, email: '' });
                                }}
                                placeholder="john.doe@example.com"
                                placeholderTextColor={colors.subtext}
                                keyboardType="email-address"
                                autoCapitalize="none"
                            />
                            {errors.email && <Text style={styles.errorText}>{errors.email}</Text>}
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Phone Number</Text>
                            <TextInput
                                style={[styles.input, errors.phone ? styles.inputError : null]}
                                value={formData.phone}
                                onChangeText={(text) => {
                                    setFormData({ ...formData, phone: text });
                                    if (errors.phone) setErrors({ ...errors, phone: '' });
                                }}
                                placeholder="+44 123 456 7890"
                                placeholderTextColor={colors.subtext}
                                keyboardType="phone-pad"
                            />
                            {errors.phone && <Text style={styles.errorText}>{errors.phone}</Text>}
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Location (Optional)</Text>
                            <TextInput
                                style={styles.input}
                                value={formData.location}
                                onChangeText={(text) => setFormData({ ...formData, location: text })}
                                placeholder="London, UK"
                                placeholderTextColor={colors.subtext}
                            />
                        </View>
                    </View>

                    {/* Continue Button */}
                    <Pressable style={styles.button} onPress={handleContinue}>
                        <Text style={styles.buttonText}>Continue</Text>
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
    input: {
        backgroundColor: colors.surface,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: radius.md,
        paddingHorizontal: spacing.md,
        paddingVertical: 14,
        fontSize: 16,
        color: colors.text,
    },
    inputError: {
        borderColor: colors.danger,
    },
    errorText: {
        fontSize: 12,
        color: colors.danger,
    },
    button: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
    },
    buttonText: {
        color: '#fff',
        fontSize: 18,
        fontWeight: '600',
    },
});
