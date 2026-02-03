import { View, Text, StyleSheet, FlatList, Pressable, ActivityIndicator, RefreshControl } from 'react-native';
import { useState, useEffect } from 'react';
import { router } from 'expo-router';
import { colors, spacing, radius } from '../../../../src/ui/theme';
import { getRemittances } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';

interface Remittance {
    id: number;
    remittance_number: string;
    client_invoice_id: number;
    invoice_number?: string;
    amount: number;
    payment_date: string;
    status: string;
    notes?: string;
    submitted_at?: string;
}

export default function RemittancesListScreen() {
    const [remittances, setRemittances] = useState<Remittance[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [activeTab, setActiveTab] = useState<string>('all');

    useEffect(() => {
        loadRemittances();
    }, [activeTab]);

    const loadRemittances = async () => {
        try {
            setLoading(true);
            const params = activeTab === 'all' ? {} : { status: activeTab };
            const response = await getRemittances(params);
            setRemittances(response.data.data || []);
        } catch (error) {
            console.error('Error loading remittances:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const handleRefresh = () => {
        setRefreshing(true);
        loadRemittances();
    };

    const getStatusColor = (status: string): string => {
        const statusMap: { [key: string]: string } = {
            pending: colors.primary,
            approved: '#22c55e',
            rejected: colors.danger,
        };
        return statusMap[status] || colors.subtext;
    };

    const renderRemittanceCard = ({ item }: { item: Remittance }) => (
        <Pressable
            style={styles.card}
            onPress={() => router.push(`/finances/remittances/${item.id}` as any)}
        >
            <View style={styles.cardHeader}>
                <View style={styles.cardHeaderLeft}>
                    <Text style={styles.remittanceNumber}>{item.remittance_number}</Text>
                    {item.invoice_number && (
                        <Text style={styles.invoiceNumber}>Invoice: {item.invoice_number}</Text>
                    )}
                </View>
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
                    <Text style={styles.statusText}>{item.status.toUpperCase()}</Text>
                </View>
            </View>

            <View style={styles.cardBody}>
                <View style={styles.amountRow}>
                    <Text style={styles.amountLabel}>Amount Paid</Text>
                    <Text style={styles.amountValue}>£{Number(item.amount || 0).toFixed(2)}</Text>
                </View>

                <View style={styles.infoRow}>
                    <Ionicons name="calendar-outline" size={16} color={colors.subtext} />
                    <Text style={styles.infoText}>{formatDate(item.payment_date)}</Text>
                </View>

                {item.submitted_at && (
                    <View style={styles.infoRow}>
                        <Ionicons name="checkmark-circle-outline" size={16} color={colors.subtext} />
                        <Text style={styles.infoText}>Submitted {formatDate(item.submitted_at)}</Text>
                    </View>
                )}
            </View>
        </Pressable>
    );

    const renderEmptyState = () => (
        <View style={styles.emptyState}>
            <Ionicons name="receipt-outline" size={64} color={colors.subtext} />
            <Text style={styles.emptyText}>No payment submissions found</Text>
            <Text style={styles.emptySubtext}>Your payment proofs will appear here</Text>
        </View>
    );

    const formatDate = (dateStr: string): string => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const tabs = [
        { key: 'all', label: 'All' },
        { key: 'pending', label: 'Pending' },
        { key: 'approved', label: 'Approved' },
        { key: 'rejected', label: 'Rejected' },
    ];

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Payment Submissions</Text>
            </View>

            {/* Tabs */}
            <View style={styles.tabs}>
                {tabs.map((tab) => (
                    <Pressable
                        key={tab.key}
                        style={[styles.tab, activeTab === tab.key && styles.activeTab]}
                        onPress={() => setActiveTab(tab.key)}
                    >
                        <Text style={[styles.tabText, activeTab === tab.key && styles.activeTabText]}>
                            {tab.label}
                        </Text>
                    </Pressable>
                ))}
            </View>

            {/* Content */}
            {loading ? (
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={colors.primary} />
                </View>
            ) : (
                <FlatList
                    data={remittances}
                    renderItem={renderRemittanceCard}
                    keyExtractor={(item) => item.id.toString()}
                    contentContainerStyle={styles.listContent}
                    ListEmptyComponent={renderEmptyState}
                    refreshControl={
                        <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} colors={[colors.primary]} />
                    }
                />
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    header: {
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    headerTitle: {
        fontSize: 24,
        fontWeight: '700',
        color: colors.text,
    },
    tabs: {
        flexDirection: 'row',
        backgroundColor: colors.surface,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    tab: {
        flex: 1,
        paddingVertical: spacing.md,
        alignItems: 'center',
        borderBottomWidth: 2,
        borderBottomColor: 'transparent',
    },
    activeTab: {
        borderBottomColor: colors.primary,
    },
    tabText: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.subtext,
    },
    activeTabText: {
        color: colors.primary,
    },
    loadingContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
    },
    listContent: {
        padding: spacing.lg,
        gap: spacing.md,
    },
    card: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        gap: spacing.sm,
    },
    cardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
    },
    cardHeaderLeft: {
        flex: 1,
    },
    remittanceNumber: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    invoiceNumber: {
        fontSize: 13,
        color: colors.subtext,
        marginTop: 2,
    },
    statusBadge: {
        paddingHorizontal: spacing.sm,
        paddingVertical: 4,
        borderRadius: radius.sm,
    },
    statusText: {
        fontSize: 11,
        fontWeight: '700',
        color: '#fff',
    },
    cardBody: {
        gap: spacing.xs,
        marginTop: spacing.xs,
    },
    amountRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingTop: spacing.xs,
        borderTopWidth: 1,
        borderTopColor: colors.border,
    },
    amountLabel: {
        fontSize: 14,
        color: colors.subtext,
    },
    amountValue: {
        fontSize: 18,
        fontWeight: '700',
        color: colors.primary,
    },
    infoRow: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.xs,
    },
    infoText: {
        fontSize: 13,
        color: colors.subtext,
    },
    emptyState: {
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.xl * 2,
    },
    emptyText: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
        marginTop: spacing.md,
    },
    emptySubtext: {
        fontSize: 14,
        color: colors.subtext,
        marginTop: spacing.xs,
    },
});
