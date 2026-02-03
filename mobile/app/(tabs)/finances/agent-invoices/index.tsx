import { View, Text, StyleSheet, FlatList, Pressable, ActivityIndicator, RefreshControl } from 'react-native';
import { useState, useEffect } from 'react';
import { router } from 'expo-router';
import { colors, spacing, radius } from '../../../../src/ui/theme';
import { getAgentInvoices } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';

interface AgentInvoice {
    id: number;
    invoice_number: string;
    invoice_date?: string;
    date_from?: string;
    date_to?: string;
    subtotal: number;
    vat_amount: number;
    total_amount: number;
    status: string;
    items_count?: number;
    has_pdf: boolean;
}

export default function AgentInvoicesListScreen() {
    const [invoices, setInvoices] = useState<AgentInvoice[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [activeTab, setActiveTab] = useState<string>('all');

    useEffect(() => {
        loadInvoices();
    }, [activeTab]);

    const loadInvoices = async () => {
        try {
            setLoading(true);
            const params = activeTab === 'all' ? {} : { status: activeTab };
            const response = await getAgentInvoices(params);
            setInvoices(response.data.data || []);
        } catch (error) {
            console.error('Error loading invoices:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const handleRefresh = () => {
        setRefreshing(true);
        loadInvoices();
    };

    const getStatusColor = (status: string): string => {
        const statusMap: { [key: string]: string } = {
            draft: colors.subtext,
            submitted: colors.primary,
            sent: '#3b82f6',
            paid: '#22c55e',
            cancelled: colors.danger,
        };
        return statusMap[status] || colors.subtext;
    };

    const renderInvoiceCard = ({ item }: { item: AgentInvoice }) => (
        <Pressable
            style={styles.card}
            onPress={() => router.push(`/finances/agent-invoices/${item.id}`)}
        >
            <View style={styles.cardHeader}>
                <View style={styles.cardHeaderLeft}>
                    <Text style={styles.invoiceNumber}>{item.invoice_number}</Text>
                    <Text style={styles.dateRange}>
                        {item.date_from && item.date_to
                            ? `${formatDate(item.date_from)} - ${formatDate(item.date_to)}`
                            : formatDate(item.invoice_date)}
                    </Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
                    <Text style={styles.statusText}>{item.status.toUpperCase()}</Text>
                </View>
            </View>

            <View style={styles.cardBody}>
                <View style={styles.amountRow}>
                    <Text style={styles.amountLabel}>Total Amount</Text>
                    <Text style={styles.amountValue}>£{Number(item.total_amount || 0).toFixed(2)}</Text>
                </View>

                {item.items_count && (
                    <View style={styles.infoRow}>
                        <Ionicons name="list-outline" size={16} color={colors.subtext} />
                        <Text style={styles.infoText}>{item.items_count} items</Text>
                    </View>
                )}

                {item.has_pdf && (
                    <View style={styles.infoRow}>
                        <Ionicons name="document-outline" size={16} color={colors.primary} />
                        <Text style={[styles.infoText, { color: colors.primary }]}>PDF Available</Text>
                    </View>
                )}
            </View>
        </Pressable>
    );

    const renderEmptyState = () => (
        <View style={styles.emptyState}>
            <Ionicons name="receipt-outline" size={64} color={colors.subtext} />
            <Text style={styles.emptyText}>No payslips found</Text>
            <Text style={styles.emptySubtext}>Your payslips will appear here</Text>
        </View>
    );

    const formatDate = (dateStr?: string): string => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const tabs = [
        { key: 'all', label: 'All' },
        { key: 'draft', label: 'Draft' },
        { key: 'submitted', label: 'Submitted' },
        { key: 'paid', label: 'Paid' },
    ];

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>My Payslips</Text>
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
                    data={invoices}
                    renderItem={renderInvoiceCard}
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
    invoiceNumber: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    dateRange: {
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
