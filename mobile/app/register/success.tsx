import { View, Text, StyleSheet, Pressable } from 'react-native';
import { router } from 'expo-router';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import Screen from '../../src/ui/components/Screen';

export default function RegistrationSuccessScreen() {
    const handleContinue = () => {
        // User is already logged in from registration, go to app
        router.replace('/(tabs)');
    };

    return (
        <Screen>
            <View style={styles.container}>
                {/* Success Icon */}
                <View style={styles.iconContainer}>
                    <Ionicons name="checkmark-circle" size={100} color={colors.secondary} />
                </View>

                {/* Title */}
                <Text style={styles.title}>Account Created!</Text>
                <Text style={styles.subtitle}>
                    Your account has been successfully created. You can now log in and start using AtoZ.
                </Text>

                {/* Continue Button */}
                <Pressable style={styles.button} onPress={handleContinue}>
                    <Text style={styles.buttonText}>Get Started</Text>
                </Pressable>
            </View>
        </Screen>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: spacing.xl,
    },
    iconContainer: {
        marginBottom: spacing.xl,
    },
    title: {
        fontSize: 28,
        fontWeight: '700',
        color: colors.text,
        textAlign: 'center',
        marginBottom: spacing.md,
    },
    subtitle: {
        fontSize: 16,
        color: colors.subtext,
        textAlign: 'center',
        lineHeight: 24,
        marginBottom: spacing.xl * 2,
    },
    button: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        paddingHorizontal: 60,
        borderRadius: radius.lg,
        alignItems: 'center',
    },
    buttonText: {
        color: '#fff',
        fontSize: 18,
        fontWeight: '600',
    },
});
