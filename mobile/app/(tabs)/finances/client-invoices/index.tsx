import { View, Text, StyleSheet, FlatList, Pressable, ActivityIndicator, RefreshControl } from 'react-native';
import { useState, useEffect } from 'react';
import { router } from 'expo-router';
import { colors, spacing, radius } from '../../../../src/ui/theme';
import { getClientInvoices } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';

interface ClientInvoice {
    id: number;
    invoice_number: string;
    invoice_date?: string;
    date_range_start?: string;
    date_range_end?: string;
    subtotal: number;
    vat_amount: number;
    total: number;
    status: string;
    items_count?: number;
    has_pdf: boolean;
    has_remittance: boolean;
    remittance?: {
        status: string;
    };
}

export default function ClientInvoicesListScreen() {
    const [invoices, setInvoices] = useState<ClientInvoice[]>([]);
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
            const response = await getClientInvoices(params);
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
            sent: colors.primary,
            paid: '#22c55e',
            overdue: colors.danger,
            cancelled: '#64748b',
        };
        return statusMap[status] || colors.subtext;
    };

    const renderInvoiceCard = ({ item }: { item: ClientInvoice }) => (
        <Pressable
            style={styles.card}
            onPress={() => router.push(`/finances/client-invoices/${item.id}`)}
        >
            <View style={styles.cardHeader}>
                <View style={styles.cardHeaderLeft}>
                    <Text style={styles.invoiceNumber}>{item.invoice_number}</Text>
                    <Text style={styles.dateRange}>
                        {item.date_range_start && item.date_range_end
                            ? `${formatDate(item.date_range_start)} - ${formatDate(item.date_range_end)}`
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
                    <Text style={styles.amountValue}>£{Number(item.total || 0).toFixed(2)}</Text>
                </View>

                {item.items_count && (
                    <View style={styles.infoRow}>
                        <Ionicons name="list-outline" size={16} color={colors.subtext} />
                        <Text style={styles.infoText}>{item.items_count} items</Text>
                    </View>
                )}

                {item.has_remittance && item.remittance && (
                    <View style={styles.infoRow}>
                        <Ionicons name="checkmark-circle" size={16} color="#22c55e" />
                        <Text style={[styles.infoText, { color: '#22c55e' }]}>
                            Payment {item.remittance.status}
                        </Text>
                    </View>
                )}

                {!item.has_remittance && item.status === 'sent' && (
                    <View style={styles.infoRow}>
                        <Ionicons name="alert-circle-outline" size={16} color={colors.primary} />
                        <Text style={[styles.infoText, { color: colors.primary }]}>Payment pending</Text>
                    </View>
                )}
            </View>
        </Pressable>
    );

    const renderEmptyState = () => (
        <View style={styles.emptyState}>
            <Ionicons name="receipt-outline" size={64} color={colors.subtext} />
            <Text style={styles.emptyText}>No invoices found</Text>
            <Text style={styles.emptySubtext}>Your invoices will appear here</Text>
        </View>
    );

    const formatDate = (dateStr?: string): string => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    const tabs = [
        { key: 'all', label: 'All' },
        { key: 'sent', label: 'Unpaid' },
        { key: 'paid', label: 'Paid' },
        { key: 'overdue', label: 'Overdue' },
    ];

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>My Invoices</Text>
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
