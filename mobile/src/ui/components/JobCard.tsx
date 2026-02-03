import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { colors, radius, spacing, typography } from '../theme';
import { Ionicons } from '@expo/vector-icons';

type JobCardProps = {
    job: {
        id: number;
        title: string;
        date: string;
        location: string;
        status: string;
        language: string;
    };
    onPress: () => void;
};

export default function JobCard({ job, onPress }: JobCardProps) {
    const getStatusColor = (status: string) => {
        switch (status.toLowerCase()) {
            case 'assigned':
                return colors.secondary;
            case 'pending':
                return colors.accent;
            case 'cancelled':
                return colors.danger;
            default:
                return colors.subtext;
        }
    };

    return (
        <TouchableOpacity onPress={onPress} activeOpacity={0.7}>
            <View style={styles.card}>
                <View style={styles.header}>
                    <Text style={styles.title}>{job.title}</Text>
                    <View style={[styles.badge, { backgroundColor: getStatusColor(job.status) }]}>
                        <Text style={styles.badgeText}>{job.status}</Text>
                    </View>
                </View>

                <View style={styles.row}>
                    <Ionicons name="calendar-outline" size={16} color={colors.subtext} />
                    <Text style={styles.rowText}>{job.date}</Text>
                </View>

                <View style={styles.row}>
                    <Ionicons name="location-outline" size={16} color={colors.subtext} />
                    <Text style={styles.rowText}>{job.location}</Text>
                </View>

                <View style={styles.row}>
                    <Ionicons name="language-outline" size={16} color={colors.subtext} />
                    <Text style={styles.rowText}>{job.language}</Text>
                </View>
            </View>
        </TouchableOpacity>
    );
}

const styles = StyleSheet.create({
    card: {
        backgroundColor: colors.card,
        borderRadius: radius.md,
        padding: spacing.md,
        marginBottom: spacing.md,
        borderWidth: 1,
        borderColor: colors.border,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.05,
        shadowRadius: 4,
        elevation: 2,
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: spacing.sm,
    },
    title: {
        ...typography.h1,
        flex: 1,
        marginRight: spacing.sm,
    },
    badge: {
        paddingHorizontal: spacing.sm,
        paddingVertical: 2,
        borderRadius: radius.sm,
    },
    badgeText: {
        color: '#FFFFFF',
        fontSize: 12,
        fontWeight: '600',
        textTransform: 'uppercase',
    },
    row: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: spacing.xs,
    },
    rowText: {
        ...typography.body,
        marginLeft: spacing.xs,
        color: colors.subtext,
    },
});
