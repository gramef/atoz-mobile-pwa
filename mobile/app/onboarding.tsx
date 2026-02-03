import { View, Text, StyleSheet, Pressable, Image } from 'react-native';
import { router } from 'expo-router';
import { useState } from 'react';
import { colors, spacing, radius, typography } from '../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';

const onboardingData = [
    {
        id: 1,
        title: 'Find Professional Interpreters',
        description: 'Easy, Track, and Pay for interpreting services. Connect with qualified interpreters for all your language needs.',
        icon: 'people-outline' as const,
    },
    {
        id: 2,
        title: 'Secure & Reliable',
        description: 'All interpreters are verified and qualified. Your bookings and payments are secure and protected.',
        icon: 'shield-checkmark-outline' as const,
    },
    {
        id: 3,
        title: 'Work Smarter, Earn More',
        description: 'For interpreters: Find jobs, manage your schedule, and get paid faster. Everything in one place.',
        icon: 'briefcase-outline' as const,
    },
];

export default function OnboardingScreen() {
    const [currentIndex, setCurrentIndex] = useState(0);
    const currentSlide = onboardingData[currentIndex];
    const isLastSlide = currentIndex === onboardingData.length - 1;

    const handleNext = () => {
        if (isLastSlide) {
            router.replace('/login');
        } else {
            setCurrentIndex(currentIndex + 1);
        }
    };

    const handleSkip = () => {
        router.replace('/login');
    };

    return (
        <View style={styles.container}>
            {/* Skip Button */}
            <Pressable style={styles.skipButton} onPress={handleSkip}>
                <Text style={styles.skipText}>Skip</Text>
            </Pressable>

            {/* Content */}
            <View style={styles.content}>
                {/* Icon */}
                <View style={styles.iconContainer}>
                    <Ionicons name={currentSlide.icon} size={120} color={colors.primary} />
                </View>

                {/* Title */}
                <Text style={styles.title}>{currentSlide.title}</Text>

                {/* Description */}
                <Text style={styles.description}>{currentSlide.description}</Text>
            </View>

            {/* Bottom Section */}
            <View style={styles.bottom}>
                {/* Pagination Dots */}
                <View style={styles.pagination}>
                    {onboardingData.map((_, index) => (
                        <View
                            key={index}
                            style={[
                                styles.dot,
                                index === currentIndex && styles.dotActive,
                            ]}
                        />
                    ))}
                </View>

                {/* Next Button */}
                <Pressable style={styles.button} onPress={handleNext}>
                    <Text style={styles.buttonText}>
                        {isLastSlide ? 'Get Started' : 'Next'}
                    </Text>
                    <Ionicons name="arrow-forward" size={20} color="#fff" />
                </Pressable>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
        paddingTop: 60,
    },
    skipButton: {
        alignSelf: 'flex-end',
        paddingHorizontal: spacing.lg,
        paddingVertical: spacing.sm,
    },
    skipText: {
        fontSize: 16,
        color: colors.subtext,
        fontWeight: '600',
    },
    content: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: spacing.xl,
    },
    iconContainer: {
        width: 200,
        height: 200,
        borderRadius: 100,
        backgroundColor: colors.blueLight || '#E7F1FF',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: spacing.xl,
    },
    title: {
        fontSize: 28,
        fontWeight: '700',
        color: colors.text,
        textAlign: 'center',
        marginBottom: spacing.md,
    },
    description: {
        fontSize: 16,
        color: colors.subtext,
        textAlign: 'center',
        lineHeight: 24,
    },
    bottom: {
        paddingHorizontal: spacing.xl,
        paddingBottom: 40,
    },
    pagination: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginBottom: spacing.lg,
        gap: spacing.sm,
    },
    dot: {
        width: 8,
        height: 8,
        borderRadius: 4,
        backgroundColor: colors.border,
    },
    dotActive: {
        width: 24,
        backgroundColor: colors.primary,
    },
    button: {
        backgroundColor: colors.primary,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: 16,
        borderRadius: radius.lg,
        gap: spacing.sm,
    },
    buttonText: {
        color: '#fff',
        fontSize: 18,
        fontWeight: '600',
    },
});
