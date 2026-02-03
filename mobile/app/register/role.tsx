import { View, Text, StyleSheet, Pressable, ScrollView } from 'react-native';
import { router } from 'expo-router';
import { useState } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import Screen from '../../src/ui/components/Screen';

type Role = 'agent' | 'client';

const roles = [
    {
        id: 'agent' as Role,
        title: 'I am an Interpreter/Translator',
        description: 'Find jobs, manage bookings, and get paid for your services',
        icon: 'briefcase-outline' as const,
    },
    {
        id: 'client' as Role,
        title: 'I need Interpretation/Translation',
        description: 'Book qualified professionals for your language needs',
        icon: 'people-outline' as const,
    },
];

export default function RoleSelectionScreen() {
    const [selectedRole, setSelectedRole] = useState<Role | null>(null);

    const handleContinue = () => {
        if (selectedRole) {
            // Navigate to registration with role context
            router.push({
                pathname: '/register/details',
                params: { role: selectedRole },
            });
        }
    };

    return (
        <Screen>
            <ScrollView contentContainerStyle={styles.container}>
                {/* Header */}
                <View style={styles.header}>
                    <Pressable onPress={() => router.back()} style={styles.backButton}>
                        <Ionicons name="arrow-back" size={24} color={colors.text} />
                    </Pressable>
                </View>

                {/* Title */}
                <Text style={styles.title}>Choose Your Role</Text>
                <Text style={styles.subtitle}>
                    Select how you'll be using AtoZ
                </Text>

                {/* Role Cards */}
                <View style={styles.rolesContainer}>
                    {roles.map((role) => (
                        <Pressable
                            key={role.id}
                            style={[
                                styles.roleCard,
                                selectedRole === role.id && styles.roleCardSelected,
                            ]}
                            onPress={() => setSelectedRole(role.id)}
                        >
                            <View style={styles.roleCardHeader}>
                                <View
                                    style={[
                                        styles.iconContainer,
                                        selectedRole === role.id && styles.iconContainerSelected,
                                    ]}
                                >
                                    <Ionicons
                                        name={role.icon}
                                        size={32}
                                        color={selectedRole === role.id ? '#fff' : colors.primary}
                                    />
                                </View>
                                <View style={styles.checkmark}>
                                    {selectedRole === role.id && (
                                        <Ionicons name="checkmark-circle" size={24} color={colors.primary} />
                                    )}
                                </View>
                            </View>

                            <Text style={styles.roleTitle}>{role.title}</Text>
                            <Text style={styles.roleDescription}>{role.description}</Text>
                        </Pressable>
                    ))}
                </View>

                {/* Continue Button */}
                <Pressable
                    style={[styles.button, !selectedRole && styles.buttonDisabled]}
                    onPress={handleContinue}
                    disabled={!selectedRole}
                >
                    <Text style={styles.buttonText}>Continue</Text>
                </Pressable>

                {/* Login Link */}
                <View style={styles.loginPrompt}>
                    <Text style={styles.loginText}>Already have an account? </Text>
                    <Pressable onPress={() => router.push('/login')}>
                        <Text style={styles.loginLink}>Log In</Text>
                    </Pressable>
                </View>
            </ScrollView>
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
    rolesContainer: {
        gap: spacing.md,
        marginBottom: spacing.xl,
    },
    roleCard: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.lg,
        borderWidth: 2,
        borderColor: colors.border,
    },
    roleCardSelected: {
        borderColor: colors.primary,
        backgroundColor: '#F0F7FF',
    },
    roleCardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: spacing.md,
    },
    iconContainer: {
        width: 60,
        height: 60,
        borderRadius: 30,
        backgroundColor: colors.blueLight || '#E7F1FF',
        alignItems: 'center',
        justifyContent: 'center',
    },
    iconContainerSelected: {
        backgroundColor: colors.primary,
    },
    checkmark: {
        width: 24,
        height: 24,
    },
    roleTitle: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    roleDescription: {
        fontSize: 14,
        color: colors.subtext,
        lineHeight: 20,
    },
    button: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
        marginTop: spacing.lg,
    },
    buttonDisabled: {
        backgroundColor: colors.border,
    },
    buttonText: {
        color: '#fff',
        fontSize: 18,
        fontWeight: '600',
    },
    loginPrompt: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginTop: spacing.lg,
    },
    loginText: {
        fontSize: 14,
        color: colors.subtext,
    },
    loginLink: {
        fontSize: 14,
        color: colors.primary,
        fontWeight: '600',
    },
});
