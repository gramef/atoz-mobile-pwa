import { View, Text, StyleSheet, ScrollView, Pressable, ActivityIndicator, Alert } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { useState, useEffect } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import {
    getInterpreterJob,
    getTranslatorJob,
    acceptInterpreterJob,
    declineInterpreterJob,
    acceptTranslatorJob,
    declineTranslatorJob,
    completeInterpreterJob,
    dnaInterpreterJob,
    returnInterpreterJob
} from '../../src/api/client';
import { Ionicons } from '@expo/vector-icons';

export default function JobDetailsScreen() {
    const params = useLocalSearchParams();
    const jobId = Number(params.id);
    const jobType = params.type as 'interpreter' | 'translator';

    const [job, setJob] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(false);

    useEffect(() => {
        loadJobDetails();
    }, [jobId, jobType]);

    const loadJobDetails = async () => {
        try {
            setLoading(true);
            const response = jobType === 'interpreter'
                ? await getInterpreterJob(jobId)
                : await getTranslatorJob(jobId);
            setJob(response.data);
        } catch (error) {
            console.error('Error loading job details:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleAccept = async () => {
        Alert.alert(
            'Accept Job',
            'Are you sure you want to accept this job?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Accept',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            if (jobType === 'interpreter') {
                                await acceptInterpreterJob(jobId);
                            } else {
                                await acceptTranslatorJob(jobId);
                            }
                            Alert.alert('Success', 'Job accepted successfully!');
                            router.back();
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to accept job');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const handleDecline = async () => {
        Alert.alert(
            'Decline Job',
            'Are you sure you want to decline this job?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Decline',
                    style: 'destructive',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            if (jobType === 'interpreter') {
                                await declineInterpreterJob(jobId, 'Declined via mobile app');
                            } else {
                                await declineTranslatorJob(jobId, 'Declined via mobile app');
                            }
                            Alert.alert('Success', 'Job declined');
                            router.back();
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to decline job');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const handleComplete = async () => {
        Alert.alert(
            'Mark as Complete',
            'Mark this job as completed?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Complete',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            await completeInterpreterJob(jobId);
                            Alert.alert('Success', 'Job marked as completed!');
                            loadJobDetails(); // Reload to update status
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to complete job');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const handleDNA = async () => {
        Alert.alert(
            'Mark as DNA',
            'Mark this job as Did Not Attend? This action will notify the client.',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Confirm DNA',
                    style: 'destructive',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            await dnaInterpreterJob(jobId);
                            Alert.alert('Job Marked', 'Job marked as Did Not Attend');
                            loadJobDetails();
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to mark as DNA');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const handleReturn = async () => {
        Alert.alert(
            'Return Job',
            'Return this job to the pool for reassignment?',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Return',
                    style: 'destructive',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            await returnInterpreterJob(jobId);
                            Alert.alert('Success', 'Job returned to pool');
                            router.back();
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to return job');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    if (!job) {
        return (
            <View style={styles.errorContainer}>
                <Text style={styles.errorText}>Job not found</Text>
                <Pressable style={styles.backButton} onPress={() => router.back()}>
                    <Text style={styles.backButtonText}>Go Back</Text>
                </Pressable>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Job Details</Text>
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Job Type Badge */}
                <View style={styles.typeBadge}>
                    <Text style={styles.typeText}>
                        {jobType === 'interpreter' ? '🎤 Interpretation' : '📄 Translation'}
                    </Text>
                </View>

                {/* Main Info Card */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Overview</Text>

                    <InfoRow
                        icon="language-outline"
                        label="Language"
                        value={job.to_language?.name || 'N/A'}
                    />

                    {job.from_language && (
                        <InfoRow
                            icon="swap-horizontal-outline"
                            label="From Language"
                            value={job.from_language.name}
                        />
                    )}

                    <InfoRow
                        icon="calendar-outline"
                        label="Date"
                        value={formatDate(job.appointment_date || job.target_date)}
                    />

                    {job.start_time && (
                        <InfoRow
                            icon="time-outline"
                            label="Time"
                            value={`${job.start_time} (${job.duration_hours || 0}h ${job.duration_minutes || 0}m)`}
                        />
                    )}

                    {job.client_reference && (
                        <InfoRow
                            icon="document-text-outline"
                            label="Reference"
                            value={job.client_reference}
                        />
                    )}
                </View>

                {/* Location Card */}
                {job.address_line_1 && (
                    <View style={styles.card}>
                        <Text style={styles.cardTitle}>Location</Text>
                        <Text style={styles.addressText}>
                            {job.address_line_1}
                            {job.address_line_2 && `\n${job.address_line_2}`}
                            {job.county && `\n${job.county}`}
                            {job.postcode && `\n${job.postcode}`}
                        </Text>
                    </View>
                )}

                {/* Details Card */}
                {job.department && (
                    <View style={styles.card}>
                        <Text style={styles.cardTitle}>Additional Information</Text>
                        <InfoRow
                            icon="business-outline"
                            label="Department"
                            value={job.department}
                        />
                    </View>
                )}
                {/* Action Buttons */}
                {!actionLoading && (
                    <View style={styles.actionsContainer}>
                        {/* Status 0 (pending) - Show Accept/Decline */}
                        {job.status === 0 && (
                            <>
                                <Pressable
                                    style={styles.acceptButton}
                                    onPress={handleAccept}
                                    disabled={actionLoading}
                                >
                                    <Ionicons name="checkmark-circle" size={20} color="#fff" />
                                    <Text style={styles.acceptButtonText}>Accept Job</Text>
                                </Pressable>
                                <Pressable
                                    style={styles.declineButton}
                                    onPress={handleDecline}
                                    disabled={actionLoading}
                                >
                                    <Ionicons name="close-circle" size={20} color={colors.danger} />
                                    <Text style={styles.declineButtonText}>Decline</Text>
                                </Pressable>
                            </>
                        )}

                        {/* Status 1 (assigned) - Show Complete/DNA/Return */}
                        {job.status === 1 && jobType === 'interpreter' && (
                            <>
                                {job.can_complete && (
                                    <Pressable
                                        style={styles.completeButton}
                                        onPress={handleComplete}
                                        disabled={actionLoading}
                                    >
                                        <Ionicons name="checkmark-done" size={20} color="#fff" />
                                        <Text style={styles.completeButtonText}>Mark Complete</Text>
                                    </Pressable>
                                )}
                                <View style={styles.secondaryActions}>
                                    {job.can_dna && (
                                        <Pressable
                                            style={styles.dnaButton}
                                            onPress={handleDNA}
                                            disabled={actionLoading}
                                        >
                                            <Ionicons name="alert-circle" size={18} color={colors.danger} />
                                            <Text style={styles.dnaButtonText}>DNA</Text>
                                        </Pressable>
                                    )}
                                    {job.can_return && (
                                        <Pressable
                                            style={styles.returnButton}
                                            onPress={handleReturn}
                                            disabled={actionLoading}
                                        >
                                            <Ionicons name="return-down-back" size={18} color={colors.text} />
                                            <Text style={styles.returnButtonText}>Return</Text>
                                        </Pressable>
                                    )}
                                </View>
                            </>
                        )}

                        {/* Final states (2, 4, 6) - Show status badge */}
                        {[2, 4, 6].includes(job.status) && (
                            <>
                                <View style={[
                                    styles.statusBadgeLarge,
                                    {
                                        backgroundColor:
                                            job.status === 4 ? '#22c55e' :
                                                job.status === 6 ? colors.danger :
                                                    colors.subtext
                                    }
                                ]}>
                                    <Text style={styles.statusBadgeLargeText}>
                                        {job.status === 4 ? 'COMPLETED' :
                                            job.status === 6 ? 'DNA' :
                                                'CANCELLED'}
                                    </Text>
                                </View>

                                {/* Show Create Timesheet button if completed */}
                                {job.status === 4 && (
                                    <Pressable
                                        style={styles.createTimesheetButton}
                                        onPress={() => router.push(`/timesheets/create/${job.id}`)}
                                    >
                                        <Ionicons name="time-outline" size={20} color={colors.primary} />
                                        <Text style={styles.createTimesheetButtonText}>Create Timesheet</Text>
                                    </Pressable>
                                )}
                            </>
                        )}
                    </View>
                )}
            </ScrollView>
        </View>
    );
}

const InfoRow = ({ icon, label, value }: { icon: any; label: string; value: string }) => (
    <View style={styles.infoRow}>
        <View style={styles.infoLabel}>
            <Ionicons name={icon} size={20} color={colors.subtext} />
            <Text style={styles.labelText}>{label}</Text>
        </View>
        <Text style={styles.valueText}>{value}</Text>
    </View>
);

const formatDate = (dateStr?: string): string => {
    if (!dateStr) return 'TBD';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', {
            weekday: 'short',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
};

const styles = StyleSheet.create({
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
    contentContainer: {
        padding: spacing.lg,
        gap: spacing.md,
    },
    typeBadge: {
        alignSelf: 'flex-start',
        backgroundColor: colors.blueLight || '#E7F1FF',
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        borderRadius: radius.full,
    },
    typeText: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.primary,
    },
    card: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        gap: spacing.md,
    },
    cardTitle: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    infoRow: {
        gap: spacing.sm,
    },
    infoLabel: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    labelText: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.subtext,
    },
    valueText: {
        fontSize: 16,
        color: colors.text,
        marginLeft: 28,
    },
    addressText: {
        fontSize: 16,
        color: colors.text,
        lineHeight: 24,
    },
    actionButtons: {
        gap: spacing.md,
        marginTop: spacing.md,
    },
    acceptButton: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
    },
    acceptButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    declineButton: {
        backgroundColor: colors.surface,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
        borderWidth: 1,
        borderColor: colors.danger,
    },
    declineButtonText: {
        color: colors.danger,
        fontSize: 16,
        fontWeight: '600',
    },
    actionsContainer: {
        padding: spacing.lg,
        backgroundColor: colors.bg,
        borderTopWidth: 1,
        borderTopColor: colors.border,
        gap: spacing.sm,
    },
    completeButton: {
        backgroundColor: '#22c55e', // green success color
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.lg,
        borderRadius: radius.lg,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        gap: spacing.sm,
    },
    completeButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    secondaryActions: {
        flexDirection: 'row',
        gap: spacing.sm,
    },
    dnaButton: {
        flex: 1,
        backgroundColor: colors.surface,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.danger,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        gap: spacing.xs,
    },
    dnaButtonText: {
        color: colors.danger,
        fontSize: 14,
        fontWeight: '600',
    },
    returnButton: {
        flex: 1,
        backgroundColor: colors.surface,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        gap: spacing.xs,
    },
    returnButtonText: {
        color: colors.text,
        fontSize: 14,
        fontWeight: '600',
    },
    statusBadge: {
        padding: spacing.md,
        borderRadius: radius.md,
        alignItems: 'center',
    },
    completedBadge: {
        backgroundColor: '#22c55e' + '20',
    },
    cancelledBadge: {
        backgroundColor: colors.subtext + '20',
    },
    dnaBadge: {
        backgroundColor: colors.danger + '20',
    },
    statusBadgeText: {
        fontSize: 16,
        fontWeight: '700',
        color: colors.text,
    },
    buttonDisabled: {
        opacity: 0.6,
    },
    loadingContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.bg,
    },
    errorContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.bg,
        padding: spacing.xl,
    },
    errorText: {
        fontSize: 18,
        color: colors.text,
        marginBottom: spacing.lg,
    },
    backButton: {
        backgroundColor: colors.primary,
        paddingHorizontal: spacing.xl,
        paddingVertical: spacing.md,
        borderRadius: radius.lg,
    },
    backButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    statusBadgeLarge: {
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.lg,
        borderRadius: radius.md,
    },
    statusBadgeLargeText: {
        fontSize: 18,
        fontWeight: '700',
        color: '#fff',
    },
    createTimesheetButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.surface,
        paddingVertical: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.primary,
        gap: spacing.sm,
        marginTop: spacing.md,
    },
    createTimesheetButtonText: {
        color: colors.primary,
        fontSize: 16,
        fontWeight: '600',
    },
});
