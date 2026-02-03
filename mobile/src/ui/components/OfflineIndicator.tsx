import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useTheme, spacing } from '../theme';
import { useNetworkStatus } from '../../hooks/useCache';

interface OfflineIndicatorProps {
    message?: string;
}

/**
 * Banner that shows when device is offline
 */
export function OfflineIndicator({ message = 'You are offline' }: OfflineIndicatorProps) {
    const { colors } = useTheme();
    const { isOnline, isLoading } = useNetworkStatus();

    if (isLoading || isOnline) {
        return null;
    }

    return (
        <View style={[styles.container, { backgroundColor: colors.accent }]}>
            <Ionicons name="cloud-offline-outline" size={18} color="#fff" />
            <Text style={styles.text}>{message}</Text>
        </View>
    );
}

/**
 * Small indicator showing data is from cache
 */
export function CacheIndicator({ visible }: { visible: boolean }) {
    const { colors } = useTheme();

    if (!visible) return null;

    return (
        <View style={[styles.cacheTag, { backgroundColor: colors.subtext + '30' }]}>
            <Ionicons name="time-outline" size={12} color={colors.subtext} />
            <Text style={[styles.cacheText, { color: colors.subtext }]}>Cached</Text>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.sm,
        paddingHorizontal: spacing.md,
        gap: spacing.sm,
    },
    text: {
        color: '#fff',
        fontSize: 13,
        fontWeight: '600',
    },
    cacheTag: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: spacing.sm,
        paddingVertical: 2,
        borderRadius: 4,
        gap: 4,
    },
    cacheText: {
        fontSize: 11,
        fontWeight: '500',
    },
});
