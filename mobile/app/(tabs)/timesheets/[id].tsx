import { View, Text, StyleSheet, ScrollView, Pressable, ActivityIndicator, Alert } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { useState, useEffect, useMemo } from 'react';
import { useTheme, spacing, radius } from '../../../src/ui/theme';
import { getTimesheet, deleteTimesheet } from '../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';


interface Timesheet {
    id: number;
    job_id: number;
    agent_id: number;
    status: string;
    agent_start_time: string;
    agent_end_time: string;
    duration_hours: number;
    duration_minutes: number;
    notes?: string;
    created_at: string;
    updated_at: string;
    job?: {
        id: number;
        client_reference?: string;
        appointment_date?: string;
        start_time?: string;
        to_language?: { id: number; name: string };
        from_language?: { id: number; name: string };
    };
    expenses?: Array<{
        id: number;
        type: string;
        amount: number;
    }>;
    total_expenses?: number;
}

export default function TimesheetDetailsScreen() {
    const { colors } = useTheme();
    const styles = useMemo(() => createStyles(colors), [colors]);
    const params = useLocalSearchParams();
    const timesheetId = Number(params.id);

    const [timesheet, setTimesheet] = useState<Timesheet | null>(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(false);

    // InfoRow component needs to be inside to access colors and styles
    const InfoRow = ({ icon, label, value }: { icon: string; label: string; value: string }) => (
        <View style={styles.infoRow}>
            <View style={styles.infoLabel}>
                <Ionicons name={icon as any} size={20} color={colors.subtext} />
                <Text style={styles.labelText}>{label}</Text>
            </View>
            <Text style={styles.valueText}>{value}</Text>
        </View>
    );

    useEffect(() => {
        loadTimesheetDetails();
    }, [timesheetId]);

    const loadTimesheetDetails = async () => {
        try {
            setLoading(true);
            const response = await getTimesheet(timesheetId);
            setTimesheet(response.data);
        } catch (error) {
            console.error('Error loading timesheet:', error);
            Alert.alert('Error', 'Failed to load timesheet details');
        } finally {
            setLoading(false);
        }
    };

    const handleEdit = () => {
        // Navigate to edit screen (to be implemented)
        Alert.alert('Coming Soon', 'Edit functionality will be available soon');
    };

    const handleDelete = () => {
        if (!timesheet) return;

        Alert.alert(
            'Delete Timesheet',
            'Are you sure you want to delete this timesheet? This action cannot be undone.',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Delete',
                    style: 'destructive',
                    onPress: async () => {
                        setActionLoading(true);
                        try {
                            await deleteTimesheet(timesheet.id);
                            Alert.alert('Success', 'Timesheet deleted successfully');
                            router.back();
                        } catch (error: any) {
                            Alert.alert('Error', error.response?.data?.message || 'Failed to delete timesheet');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const formatDate = (dateStr?: string): string => {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        } catch {
            return dateStr;
        }
    };

    const formatTime = (time: string): string => {
        if (!time) return '';
        try {
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        } catch {
            return time;
        }
    };

    const getStatusColor = (status: string): string => {
        switch (status.toLowerCase()) {
            case 'pending': return colors.primary;
            case 'submitted': return colors.primary;
            case 'approved': return '#22c55e';
            case 'rejected': return colors.danger;
            case 'paid': return '#10b981';
            default: return colors.subtext;
        }
    };

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    if (!timesheet) {
        return (
            <View style={styles.errorContainer}>
                <Text style={styles.errorText}>Timesheet not found</Text>
                <Pressable style={styles.backButton} onPress={() => router.back()}>
                    <Text style={styles.backButtonText}>Go Back</Text>
                </Pressable>
            </View>
        );
    }

    const isPending = timesheet.status.toLowerCase() === 'pending';

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Timesheet Details</Text>
                <View style={{ width: 40 }} />
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Status Badge */}
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(timesheet.status) + '20' }]}>
                    <Ionicons
                        name={timesheet.status === 'approved' ? 'checkmark-circle' : timesheet.status === 'rejected' ? 'close-circle' : 'time'}
                        size={20}
                        color={getStatusColor(timesheet.status)}
                    />
                    <Text style={[styles.statusText, { color: getStatusColor(timesheet.status) }]}>
                        {timesheet.status.charAt(0).toUpperCase() + timesheet.status.slice(1)}
                    </Text>
                </View>

                {/* Job Info Card */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Job Information</Text>
                    <InfoRow
                        icon="language-outline"
                        label="Language"
                        value={timesheet.job?.to_language?.name || 'N/A'}
                    />
                    {timesheet.job?.from_language && (
                        <InfoRow
                            icon="swap-horizontal-outline"
                            label="From"
                            value={timesheet.job.from_language.name}
                        />
                    )}
                    <InfoRow
                        icon="document-text-outline"
                        label="Reference"
                        value={timesheet.job?.client_reference || `Job #${timesheet.job_id}`}
                    />
                    <InfoRow
                        icon="calendar-outline"
                        label="Date"
                        value={formatDate(timesheet.job?.appointment_date)}
                    />
                </View>

                {/* Time Details Card */}
                <View style={styles.card}>
                    <Text style={styles.cardTitle}>Time Details</Text>
                    <InfoRow
                        icon="play-outline"
                        label="Started"
                        value={formatTime(timesheet.agent_start_time)}
                    />
                    <InfoRow
                        icon="stop-outline"
                        label="Ended"
                        value={formatTime(timesheet.agent_end_time)}
                    />
                    <InfoRow
                        icon="hourglass-outline"
                        label="Duration"
                        value={`${timesheet.duration_hours}h ${timesheet.duration_minutes}m`}
                    />
                </View>

                {/* Expenses Card */}
                {timesheet.expenses && timesheet.expenses.length > 0 && (
                    <View style={styles.card}>
                        <Text style={styles.cardTitle}>Expenses ({timesheet.expenses.length})</Text>
                        {timesheet.expenses.map((expense, index) => (
                            <View key={expense.id} style={styles.expenseRow}>
                                <View style={styles.expenseLeft}>
                                    <Ionicons name="receipt-outline" size={16} color={colors.subtext} />
                                    <Text style={styles.expenseType}>{expense.type}</Text>
                                </View>
                                <Text style={styles.expenseAmount}>£{expense.amount.toFixed(2)}</Text>
                            </View>
                        ))}
                        <View style={styles.expenseTotalRow}>
                            <Text style={styles.expenseTotalLabel}>Total Expenses</Text>
                            <Text style={styles.expenseTotalAmount}>£{timesheet.total_expenses?.toFixed(2)}</Text>
                        </View>
                    </View>
                )}

                {/* Notes Card */}
                {timesheet.notes && (
                    <View style={styles.card}>
                        <Text style={styles.cardTitle}>Notes</Text>
                        <Text style={styles.notesText}>{timesheet.notes}</Text>
                    </View>
                )}

                {/* Timestamps */}
                <View style={styles.timestamps}>
                    <Text style={styles.timestampText}>
                        Created: {formatDate(timesheet.created_at)}
                    </Text>
                    {timesheet.updated_at !== timesheet.created_at && (
                        <Text style={styles.timestampText}>
                            Updated: {formatDate(timesheet.updated_at)}
                        </Text>
                    )}
                </View>
            </ScrollView>

            {/* Action Buttons */}
            {isPending && !actionLoading && (
                <View style={styles.actionsContainer}>
                    <Pressable
                        style={styles.editButton}
                        onPress={handleEdit}
                    >
                        <Ionicons name="create-outline" size={20} color="#fff" />
                        <Text style={styles.editButtonText}>Edit</Text>
                    </Pressable>
                    <Pressable
                        style={styles.deleteButton}
                        onPress={handleDelete}
                    >
                        <Ionicons name="trash-outline" size={20} color={colors.danger} />
                        <Text style={styles.deleteButtonText}>Delete</Text>
                    </Pressable>
                </View>
            )}

            {actionLoading && (
                <View style={styles.actionsContainer}>
                    <ActivityIndicator size="small" color={colors.primary} />
                </View>
            )}
        </View>
    );
}

// InfoRow moved inside component to access theme colors

const createStyles = (colors: any) => StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
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
        padding: spacing.lg,
    },
    errorText: {
        fontSize: 16,
        color: colors.text,
        marginBottom: spacing.lg,
    },
    backButton: {
        paddingHorizontal: spacing.lg,
        paddingVertical: spacing.md,
        backgroundColor: colors.primary,
        borderRadius: radius.md,
    },
    backButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
    },
    headerBack: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    headerTitle: {
        fontSize: 18,
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
    statusBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        padding: spacing.md,
        borderRadius: radius.md,
        gap: spacing.sm,
    },
    statusText: {
        fontSize: 16,
        fontWeight: '700',
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
        marginBottom: spacing.xs,
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
    expenseRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: spacing.xs,
    },
    expenseLeft: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    expenseType: {
        fontSize: 14,
        color: colors.text,
        textTransform: 'capitalize',
    },
    expenseAmount: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    expenseTotalRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        borderTopWidth: 1,
        borderTopColor: colors.border,
        paddingTop: spacing.sm,
        marginTop: spacing.sm,
    },
    expenseTotalLabel: {
        fontSize: 15,
        fontWeight: '600',
        color: colors.text,
    },
    expenseTotalAmount: {
        fontSize: 16,
        fontWeight: '700',
        color: colors.primary,
    },
    notesText: {
        fontSize: 15,
        color: colors.text,
        lineHeight: 22,
    },
    timestamps: {
        gap: spacing.xs,
    },
    timestampText: {
        fontSize: 12,
        color: colors.subtext,
    },
    actionsContainer: {
        flexDirection: 'row',
        padding: spacing.lg,
        backgroundColor: colors.surface,
        borderTopWidth: 1,
        borderTopColor: colors.border,
        gap: spacing.sm,
    },
    editButton: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.primary,
        paddingVertical: spacing.md,
        borderRadius: radius.md,
        gap: spacing.sm,
    },
    editButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    deleteButton: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.surface,
        paddingVertical: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.danger,
        gap: spacing.sm,
    },
    deleteButtonText: {
        color: colors.danger,
        fontSize: 16,
        fontWeight: '600',
    },
});
