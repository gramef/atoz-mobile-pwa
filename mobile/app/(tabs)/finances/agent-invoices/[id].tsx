import { View, Text, StyleSheet, ScrollView, Pressable, ActivityIndicator, Alert } from 'react-native';
import { useState, useEffect } from 'react';
import { useLocalSearchParams, router } from 'expo-router';
import { colors, spacing, radius } from '../../../../src/ui/theme';
import { getAgentInvoice, downloadAgentInvoicePdf } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import * as FileSystem from 'expo-file-system';

interface InvoiceItem {
    id: number;
    description: string;
    quantity: number;
    rate: number;
    amount: number;
}

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
    items?: InvoiceItem[];
    items_count?: number;
    notes?: string;
    has_pdf: boolean;
    submitted_at?: string;
    approved_at?: string;
}

export default function AgentInvoiceDetailsScreen() {
    const params = useLocalSearchParams();
    const invoiceId = Number(params.id);

    const [invoice, setInvoice] = useState<AgentInvoice | null>(null);
    const [loading, setLoading] = useState(true);
    const [downloadingPdf, setDownloadingPdf] = useState(false);

    useEffect(() => {
        loadInvoiceDetails();
    }, [invoiceId]);

    const loadInvoiceDetails = async () => {
        try {
            setLoading(true);
            const response = await getAgentInvoice(invoiceId);
            setInvoice(response.data);
        } catch (error) {
            console.error('Error loading invoice:', error);
            Alert.alert('Error', 'Failed to load invoice details');
        } finally {
            setLoading(false);
        }
    };

    const handleDownloadPdf = async () => {
        if (!invoice?.has_pdf) {
            Alert.alert('Not Available', 'PDF is not available for this payslip');
            return;
        }

        try {
            setDownloadingPdf(true);
            const response = await downloadAgentInvoicePdf(invoiceId);

            // Save PDF to file system
            const filename = `payslip-${invoice.invoice_number}.pdf`;
            const fileUri = FileSystem.documentDirectory + filename;

            // Convert blob to base64
            const base64 = await blobToBase64(response.data);
            await FileSystem.writeAsStringAsync(fileUri, base64, {
                encoding: FileSystem.EncodingType.Base64,
            });

            Alert.alert('Success', `PDF saved to ${filename}`);
        } catch (error) {
            console.error('Error downloading PDF:', error);
            Alert.alert('Error', 'Failed to download PDF');
        } finally {
            setDownloadingPdf(false);
        }
    };

    const blobToBase64 = (blob: Blob): Promise<string> => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = reject;
            reader.onload = () => {
                const dataUrl = reader.result as string;
                const base64 = dataUrl.split(',')[1];
                resolve(base64);
            };
            reader.readAsDataURL(blob);
        });
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

    const getStatusIcon = (status: string): string => {
        const iconMap: { [key: string]: string } = {
            draft: 'create-outline',
            submitted: 'send-outline',
            sent: 'mail-outline',
            paid: 'checkmark-circle-outline',
            cancelled: 'close-circle-outline',
        };
        return iconMap[status] || 'help-outline';
    };

    const formatDate = (dateStr?: string): string => {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    if (!invoice) {
        return (
            <View style={styles.errorContainer}>
                <Text style={styles.errorText}>Payslip not found</Text>
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
                <Text style={styles.headerTitle}>Payslip Details</Text>
                {invoice.has_pdf && (
                    <Pressable onPress={handleDownloadPdf} disabled={downloadingPdf} style={styles.headerAction}>
                        {downloadingPdf ? (
                            <ActivityIndicator size="small" color={colors.primary} />
                        ) : (
                            <Ionicons name="download-outline" size={24} color={colors.primary} />
                        )}
                    </Pressable>
                )}
                {!invoice.has_pdf && <View style={{ width: 40 }} />}
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Status Badge */}
                <View style={[styles.statusCard, { backgroundColor: getStatusColor(invoice.status) }]}>
                    <Ionicons name={getStatusIcon(invoice.status) as any} size={32} color="#fff" />
                    <Text style={styles.statusText}>{invoice.status.toUpperCase()}</Text>
                </View>

                {/* Invoice Info */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="receipt-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Invoice Information</Text>
                    </View>
                    <InfoRow label="Invoice Number" value={invoice.invoice_number} />
                    <InfoRow label="Invoice Date" value={formatDate(invoice.invoice_date)} />
                    <InfoRow label="Period" value={`${formatDate(invoice.date_from)} - ${formatDate(invoice.date_to)}`} />
                    {invoice.submitted_at && (
                        <InfoRow label="Submitted" value={formatDate(invoice.submitted_at)} />
                    )}
                    {invoice.approved_at && (
                        <InfoRow label="Approved" value={formatDate(invoice.approved_at)} />
                    )}
                </View>

                {/* Line Items */}
                {invoice.items && invoice.items.length > 0 && (
                    <View style={styles.card}>
                        <View style={styles.cardHeader}>
                            <Ionicons name="list-outline" size={20} color={colors.primary} />
                            <Text style={styles.cardTitle}>Items</Text>
                        </View>
                        {invoice.items.map((item) => (
                            <View key={item.id} style={styles.itemRow}>
                                <View style={styles.itemDescription}>
                                    <Text style={styles.itemDescriptionText}>{item.description}</Text>
                                    <Text style={styles.itemDetails}>
                                        {item.quantity} × £{Number(item.rate || 0).toFixed(2)}
                                    </Text>
                                </View>
                                <Text style={styles.itemAmount}>£{Number(item.amount || 0).toFixed(2)}</Text>
                            </View>
                        ))}
                    </View>
                )}

                {/* Totals */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="calculator-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Summary</Text>
                    </View>
                    <InfoRow label="Subtotal" value={`£${Number(invoice.subtotal || 0).toFixed(2)}`} />
                    <InfoRow label="VAT" value={`£${Number(invoice.vat_amount || 0).toFixed(2)}`} />
                    <View style={styles.totalRow}>
                        <Text style={styles.totalLabel}>Total Amount</Text>
                        <Text style={styles.totalValue}>£{Number(invoice.total_amount || 0).toFixed(2)}</Text>
                    </View>
                </View>

                {/* Notes */}
                {invoice.notes && (
                    <View style={styles.card}>
                        <View style={styles.cardHeader}>
                            <Ionicons name="document-text-outline" size={20} color={colors.primary} />
                            <Text style={styles.cardTitle}>Notes</Text>
                        </View>
                        <Text style={styles.notesText}>{invoice.notes}</Text>
                    </View>
                )}
            </ScrollView>
        </View>
    );
}

const InfoRow = ({ label, value }: { label: string; value: string }) => (
    <View style={styles.infoRow}>
        <Text style={styles.infoLabel}>{label}</Text>
        <Text style={styles.infoValue}>{value}</Text>
    </View>
);

const styles = StyleSheet.create({
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
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
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
    headerAction: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    content: {
        flex: 1,
    },
    contentContainer: {
        padding: spacing.lg,
        gap: spacing.md,
    },
    statusCard: {
        alignItems: 'center',
        padding: spacing.lg,
        borderRadius: radius.lg,
        gap: spacing.sm,
    },
    statusText: {
        fontSize: 18,
        fontWeight: '700',
        color: '#fff',
    },
    card: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        gap: spacing.sm,
    },
    cardHeader: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
        marginBottom: spacing.xs,
    },
    cardTitle: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    infoRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.xs,
    },
    infoLabel: {
        fontSize: 14,
        color: colors.subtext,
    },
    infoValue: {
        fontSize: 14,
        fontWeight: '500',
        color: colors.text,
    },
    itemRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: spacing.sm,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    itemDescription: {
        flex: 1,
    },
    itemDescriptionText: {
        fontSize: 14,
        color: colors.text,
        fontWeight: '500',
    },
    itemDetails: {
        fontSize: 12,
        color: colors.subtext,
        marginTop: 2,
    },
    itemAmount: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    totalRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingTop: spacing.sm,
        borderTopWidth: 1,
        borderTopColor: colors.border,
        marginTop: spacing.xs,
    },
    totalLabel: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    totalValue: {
        fontSize: 18,
        fontWeight: '700',
        color: colors.primary,
    },
    notesText: {
        fontSize: 14,
        color: colors.text,
        lineHeight: 20,
    },
});
