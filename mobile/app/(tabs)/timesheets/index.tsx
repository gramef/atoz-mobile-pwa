import { View, Text, StyleSheet, FlatList, Pressable, RefreshControl, ActivityIndicator } from 'react-native';
import { useState, useEffect, useMemo } from 'react';
import { router } from 'expo-router';
import { useTheme, spacing, radius } from '../../../src/ui/theme';
import { getTimesheets } from '../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '../../../src/state/auth';


type TabType = 'pending' | 'submitted' | 'approved' | 'rejected';

interface Timesheet {
    id: number;
    job_id: number;
    status: string;
    agent_start_time: string;
    agent_end_time: string;
    duration_hours: number;
    duration_minutes: number;
    created_at: string;
    job?: {
        id: number;
        client_reference?: string;
        appointment_date?: string;
        to_language?: { name: string };
    };
    expenses_count?: number;
    expenses_total?: number;
}

export default function TimesheetsListScreen() {
    const { colors } = useTheme();
    const styles = useMemo(() => createStyles(colors), [colors]);
    const user = useAuthStore((s) => s.user);
    const [activeTab, setActiveTab] = useState<TabType>('pending');
    const [timesheets, setTimesheets] = useState<Timesheet[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    useEffect(() => {
        loadTimesheets();
    }, [activeTab]);

    const loadTimesheets = async () => {
        try {
            setLoading(true);
            const response = await getTimesheets({ status: activeTab });
            setTimesheets(response.data.data || []);
        } catch (error) {
            console.error('Error loading timesheets:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const handleRefresh = () => {
        setRefreshing(true);
        loadTimesheets();
    };

    const handleTimesheetPress = (timesheet: Timesheet) => {
        router.push(`/timesheets/${timesheet.id}`);
    };

    const getStatusColor = (status: string): string => {
        switch (status.toLowerCase()) {
            case 'pending': return colors.primary + '40';
            case 'submitted': return colors.primary;
            case 'approved': return '#22c55e';
            case 'rejected': return colors.danger;
            case 'paid': return '#10b981';
            default: return colors.subtext;
        }
    };

    const getStatusTextColor = (status: string): string => {
        return ['submitted', 'approved', 'paid'].includes(status.toLowerCase()) ? '#fff' : colors.text;
    };

    const formatDate = (dateStr?: string): string => {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
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

    const renderTimesheetCard = ({ item }: { item: Timesheet }) => (
        <Pressable
            style={styles.card}
            onPress={() => handleTimesheetPress(item)}
        >
            <View style={styles.cardHeader}>
                <View style={styles.cardHeaderLeft}>
                    <Text style={styles.jobReference}>
                        {item.job?.to_language?.name || 'Job'} #{item.job_id}
                    </Text>
                    <Text style={styles.cardSubtitle}>
                        {item.job?.client_reference || `Job ${item.job_id}`}
                    </Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
                    <Text style={[styles.statusText, { color: getStatusTextColor(item.status) }]}>
                        {item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                    </Text>
                </View>
            </View>

            <View style={styles.cardDetails}>
                <View style={styles.detailRow}>
                    <Ionicons name="calendar-outline" size={16} color={colors.subtext} />
                    <Text style={styles.detailText}>
                        {formatDate(item.job?.appointment_date)}
                    </Text>
                </View>

                <View style={styles.detailRow}>
                    <Ionicons name="time-outline" size={16} color={colors.subtext} />
                    <Text style={styles.detailText}>
                        {formatTime(item.agent_start_time)} - {formatTime(item.agent_end_time)}
                    </Text>
                </View>

                <View style={styles.detailRow}>
                    <Ionicons name="hourglass-outline" size={16} color={colors.subtext} />
                    <Text style={styles.detailText}>
                        {item.duration_hours}h {item.duration_minutes}m
                    </Text>
                </View>

                {item.expenses_count && item.expenses_count > 0 && (
                    <View style={styles.detailRow}>
                        <Ionicons name="receipt-outline" size={16} color={colors.subtext} />
                        <Text style={styles.detailText}>
                            {item.expenses_count} expense{item.expenses_count !== 1 ? 's' : ''} • £{item.expenses_total?.toFixed(2)}
                        </Text>
                    </View>
                )}
            </View>

            <View style={styles.cardFooter}>
                <Text style={styles.viewDetails}>View Details →</Text>
            </View>
        </Pressable>
    );

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Timesheets</Text>
            </View>

            {/* Tabs */}
            <View style={styles.tabs}>
                <Pressable
                    style={[styles.tab, activeTab === 'pending' && styles.tabActive]}
                    onPress={() => setActiveTab('pending')}
                >
                    <Text style={[styles.tabText, activeTab === 'pending' && styles.tabTextActive]}>
                        Pending
                    </Text>
                </Pressable>
                <Pressable
                    style={[styles.tab, activeTab === 'submitted' && styles.tabActive]}
                    onPress={() => setActiveTab('submitted')}
                >
                    <Text style={[styles.tabText, activeTab === 'submitted' && styles.tabTextActive]}>
                        Submitted
                    </Text>
                </Pressable>
                <Pressable
                    style={[styles.tab, activeTab === 'approved' && styles.tabActive]}
                    onPress={() => setActiveTab('approved')}
                >
                    <Text style={[styles.tabText, activeTab === 'approved' && styles.tabTextActive]}>
                        Approved
                    </Text>
                </Pressable>
                <Pressable
                    style={[styles.tab, activeTab === 'rejected' && styles.tabActive]}
                    onPress={() => setActiveTab('rejected')}
                >
                    <Text style={[styles.tabText, activeTab === 'rejected' && styles.tabTextActive]}>
                        Rejected
                    </Text>
                </Pressable>
            </View>

            {/* List */}
            {loading ? (
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={colors.primary} />
                </View>
            ) : (
                <FlatList
                    data={timesheets}
                    renderItem={renderTimesheetCard}
                    keyExtractor={(item) => item.id.toString()}
                    contentContainerStyle={styles.listContent}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={handleRefresh}
                            tintColor={colors.primary}
                        />
                    }
                    ListEmptyComponent={
                        <View style={styles.emptyContainer}>
                            <Ionicons name="document-text-outline" size={64} color={colors.subtext} />
                            <Text style={styles.emptyTitle}>No timesheets found</Text>
                            <Text style={styles.emptySubtitle}>
                                {activeTab === 'pending'
                                    ? 'Complete a job to create a timesheet'
                                    : `No ${activeTab} timesheets`}
                            </Text>
                        </View>
                    }
                />
            )}
        </View>
    );
}

const createStyles = (colors: any) => StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    header: {
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
    },
    headerTitle: {
        fontSize: 28,
        fontWeight: '700',
        color: colors.text,
    },
    tabs: {
        flexDirection: 'row',
        paddingHorizontal: spacing.lg,
        paddingVertical: spacing.md,
        backgroundColor: colors.surface,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
        gap: spacing.sm,
    },
    tab: {
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        borderRadius: radius.md,
        backgroundColor: colors.bg,
    },
    tabActive: {
        backgroundColor: colors.primary,
    },
    tabText: {
        fontSize: 13,
        fontWeight: '600',
        color: colors.subtext,
    },
    tabTextActive: {
        color: '#fff',
    },
    listContent: {
        padding: spacing.lg,
        gap: spacing.md,
    },
    loadingContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
    },
    card: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    cardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: spacing.sm,
    },
    cardHeaderLeft: {
        flex: 1,
    },
    jobReference: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
        marginBottom: 2,
    },
    cardSubtitle: {
        fontSize: 13,
        color: colors.subtext,
    },
    statusBadge: {
        paddingHorizontal: spacing.sm,
        paddingVertical: 4,
        borderRadius: radius.sm,
    },
    statusText: {
        fontSize: 11,
        fontWeight: '600',
    },
    cardDetails: {
        gap: spacing.sm,
        marginBottom: spacing.md,
    },
    detailRow: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    detailText: {
        fontSize: 14,
        color: colors.text,
    },
    cardFooter: {
        borderTopWidth: 1,
        borderTopColor: colors.border,
        paddingTop: spacing.sm,
    },
    viewDetails: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.primary,
    },
    emptyContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.xl * 2,
    },
    emptyTitle: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
        marginTop: spacing.md,
    },
    emptySubtitle: {
        fontSize: 14,
        color: colors.subtext,
        marginTop: spacing.xs,
    },
});

