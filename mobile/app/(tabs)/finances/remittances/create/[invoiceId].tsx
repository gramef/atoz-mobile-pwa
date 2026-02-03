import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Pressable,
    TextInput,
    ActivityIndicator,
    Alert,
    Image,
    Platform
} from 'react-native';
import { useState, useEffect } from 'react';
import { useLocalSearchParams, router } from 'expo-router';
import { colors, spacing, radius } from '../../../../../src/ui/theme';
import { getClientInvoice, createRemittance } from '../../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import DateTimePicker from '@react-native-community/datetimepicker';

interface ClientInvoice {
    id: number;
    invoice_number: string;
    total: number;
}

export default function CreateRemittanceScreen() {
    const params = useLocalSearchParams();
    const invoiceId = Number(params.invoiceId);

    const [invoice, setInvoice] = useState<ClientInvoice | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);

    // Form fields
    const [amount, setAmount] = useState('');
    const [paymentDate, setPaymentDate] = useState(new Date());
    const [notes, setNotes] = useState('');
    const [selectedFile, setSelectedFile] = useState<any>(null);
    const [showDatePicker, setShowDatePicker] = useState(false);

    useEffect(() => {
        loadInvoiceDetails();
    }, [invoiceId]);

    const loadInvoiceDetails = async () => {
        try {
            setLoading(true);
            const response = await getClientInvoice(invoiceId);
            setInvoice(response.data);
            // Pre-fill amount with invoice total
            setAmount(Number(response.data.total || 0).toFixed(2));
        } catch (error) {
            console.error('Error loading invoice:', error);
            Alert.alert('Error', 'Failed to load invoice details');
        } finally {
            setLoading(false);
        }
    };

    const handleTakePhoto = async () => {
        try {
            const permissionResult = await ImagePicker.requestCameraPermissionsAsync();

            if (!permissionResult.granted) {
                Alert.alert('Permission Required', 'Camera permission is required to take photos');
                return;
            }

            const result = await ImagePicker.launchCameraAsync({
                mediaTypes: ImagePicker.MediaTypeOptions.Images,
                quality: 0.8,
                allowsEditing: true,
            });

            if (!result.canceled && result.assets && result.assets.length > 0) {
                const photo = result.assets[0];
                setSelectedFile({
                    uri: photo.uri,
                    name: `payment-proof-${Date.now()}.jpg`,
                    type: 'image/jpeg',
                    size: photo.fileSize,
                });
            }
        } catch (error) {
            console.error('Error taking photo:', error);
            Alert.alert('Error', 'Failed to take photo');
        }
    };

    const handleChooseFromLibrary = async () => {
        try {
            const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync();

            if (!permissionResult.granted) {
                Alert.alert('Permission Required', 'Photo library permission is required');
                return;
            }

            const result = await ImagePicker.launchImageLibraryAsync({
                mediaTypes: ImagePicker.MediaTypeOptions.Images,
                quality: 0.8,
                allowsEditing: true,
            });

            if (!result.canceled && result.assets && result.assets.length > 0) {
                const image = result.assets[0];
                setSelectedFile({
                    uri: image.uri,
                    name: `payment-proof-${Date.now()}.jpg`,
                    type: 'image/jpeg',
                    size: image.fileSize,
                });
            }
        } catch (error) {
            console.error('Error choosing from library:', error);
            Alert.alert('Error', 'Failed to choose image');
        }
    };

    const showFilePickerOptions = () => {
        Alert.alert(
            'Upload Payment Proof',
            'Choose an option',
            [
                { text: 'Take Photo', onPress: handleTakePhoto },
                { text: 'Choose from Library', onPress: handleChooseFromLibrary },
                { text: 'Cancel', style: 'cancel' },
            ]
        );
    };

    const validateForm = (): boolean => {
        if (!amount || parseFloat(amount) <= 0) {
            Alert.alert('Invalid Amount', 'Please enter a valid payment amount');
            return false;
        }

        if (!selectedFile) {
            Alert.alert('File Required', 'Please upload payment proof (receipt, screenshot, etc.)');
            return false;
        }

        return true;
    };

    const handleSubmit = async () => {
        if (!validateForm()) return;

        setSubmitting(true);
        try {
            // Create FormData
            const formData = new FormData();
            formData.append('client_invoice_id', invoiceId.toString());
            formData.append('amount', amount);
            formData.append('payment_date', paymentDate.toISOString().split('T')[0]);

            if (notes.trim()) {
                formData.append('notes', notes.trim());
            }

            // Append file
            const file: any = {
                uri: selectedFile.uri,
                name: selectedFile.name,
                type: selectedFile.type || 'application/octet-stream',
            };
            formData.append('slip', file);

            await createRemittance(formData);

            Alert.alert(
                'Success',
                'Payment proof submitted successfully. We will review it shortly.',
                [
                    {
                        text: 'OK',
                        onPress: () => router.replace('/finances/remittances'),
                    },
                ]
            );
        } catch (error: any) {
            console.error('Error submitting remittance:', error);
            Alert.alert('Error', error.response?.data?.message || 'Failed to submit payment proof');
        } finally {
            setSubmitting(false);
        }
    };

    const formatDate = (date: Date): string => {
        return date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
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
                <Text style={styles.errorText}>Invoice not found</Text>
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
                <Text style={styles.headerTitle}>Submit Payment</Text>
                <View style={{ width: 40 }} />
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Invoice Info */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="receipt-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Invoice Details</Text>
                    </View>
                    <View style={styles.infoRow}>
                        <Text style={styles.infoLabel}>Invoice Number</Text>
                        <Text style={styles.infoValue}>{invoice.invoice_number}</Text>
                    </View>
                    <View style={styles.infoRow}>
                        <Text style={styles.infoLabel}>Total Amount</Text>
                        <Text style={styles.infoValue}>£{Number(invoice.total || 0).toFixed(2)}</Text>
                    </View>
                </View>

                {/* Payment Form */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="cash-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Payment Information</Text>
                    </View>

                    {/* Amount */}
                    <View style={styles.formField}>
                        <Text style={styles.fieldLabel}>Amount Paid</Text>
                        <TextInput
                            style={styles.input}
                            placeholder="0.00"
                            placeholderTextColor={colors.subtext}
                            value={amount}
                            onChangeText={setAmount}
                            keyboardType="decimal-pad"
                        />
                    </View>

                    {/* Payment Date */}
                    <View style={styles.formField}>
                        <Text style={styles.fieldLabel}>Payment Date</Text>
                        <Pressable
                            style={styles.dateButton}
                            onPress={() => setShowDatePicker(true)}
                        >
                            <Ionicons name="calendar-outline" size={20} color={colors.text} />
                            <Text style={styles.dateButtonText}>{formatDate(paymentDate)}</Text>
                            <Ionicons name="chevron-down-outline" size={20} color={colors.subtext} />
                        </Pressable>
                    </View>

                    {/* Notes */}
                    <View style={styles.formField}>
                        <Text style={styles.fieldLabel}>Notes (Optional)</Text>
                        <TextInput
                            style={styles.textArea}
                            placeholder="Add any notes about the payment..."
                            placeholderTextColor={colors.subtext}
                            value={notes}
                            onChangeText={setNotes}
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </View>
                </View>

                {/* File Upload */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="image-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Payment Proof</Text>
                    </View>
                    <Text style={styles.helpText}>
                        Upload a receipt, bank statement screenshot, or any proof of payment
                    </Text>

                    {selectedFile ? (
                        <View style={styles.filePreview}>
                            {selectedFile.type?.startsWith('image/') && (
                                <Image source={{ uri: selectedFile.uri }} style={styles.previewImage} />
                            )}
                            <View style={styles.fileInfo}>
                                <Ionicons name="document-outline" size={24} color={colors.primary} />
                                <Text style={styles.fileName} numberOfLines={1}>{selectedFile.name}</Text>
                            </View>
                            <Pressable style={styles.removeFileButton} onPress={() => setSelectedFile(null)}>
                                <Ionicons name="close-circle" size={24} color={colors.danger} />
                            </Pressable>
                        </View>
                    ) : (
                        <Pressable style={styles.uploadButton} onPress={showFilePickerOptions}>
                            <Ionicons name="cloud-upload-outline" size={32} color={colors.primary} />
                            <Text style={styles.uploadButtonText}>Tap to Upload</Text>
                            <Text style={styles.uploadButtonSubtext}>Take a photo or choose from library</Text>
                        </Pressable>
                    )}
                </View>
            </ScrollView>

            {/* Submit Button */}
            <View style={styles.submitContainer}>
                <Pressable
                    style={[styles.submitButton, submitting && styles.submitButtonDisabled]}
                    onPress={handleSubmit}
                    disabled={submitting}
                >
                    {submitting ? (
                        <ActivityIndicator color="#fff" />
                    ) : (
                        <>
                            <Ionicons name="checkmark-circle-outline" size={20} color="#fff" />
                            <Text style={styles.submitButtonText}>Submit Payment Proof</Text>
                        </>
                    )}
                </Pressable>
            </View>

            {/* Date Picker */}
            {showDatePicker && (
                <DateTimePicker
                    value={paymentDate}
                    mode="date"
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(event, selectedDate) => {
                        setShowDatePicker(Platform.OS === 'ios');
                        if (selectedDate) {
                            setPaymentDate(selectedDate);
                        }
                    }}
                    maximumDate={new Date()}
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
    content: {
        flex: 1,
    },
    contentContainer: {
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
    formField: {
        gap: spacing.xs,
    },
    fieldLabel: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    input: {
        backgroundColor: colors.bg,
        padding: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        fontSize: 15,
        color: colors.text,
    },
    dateButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.bg,
        padding: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    dateButtonText: {
        flex: 1,
        fontSize: 15,
        color: colors.text,
        marginLeft: spacing.sm,
    },
    textArea: {
        backgroundColor: colors.bg,
        padding: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        fontSize: 15,
        color: colors.text,
        minHeight: 80,
    },
    helpText: {
        fontSize: 13,
        color: colors.subtext,
        lineHeight: 18,
    },
    uploadButton: {
        alignItems: 'center',
        justifyContent: 'center',
        padding: spacing.xl,
        backgroundColor: colors.bg,
        borderRadius: radius.md,
        borderWidth: 2,
        borderColor: colors.primary,
        borderStyle: 'dashed',
        gap: spacing.sm,
    },
    uploadButtonText: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.primary,
    },
    uploadButtonSubtext: {
        fontSize: 13,
        color: colors.subtext,
    },
    filePreview: {
        padding: spacing.md,
        backgroundColor: colors.bg,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        gap: spacing.sm,
    },
    previewImage: {
        width: '100%',
        height: 200,
        borderRadius: radius.md,
    },
    fileInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    fileName: {
        flex: 1,
        fontSize: 14,
        color: colors.text,
    },
    removeFileButton: {
        alignSelf: 'flex-end',
    },
    submitContainer: {
        padding: spacing.lg,
        backgroundColor: colors.surface,
        borderTopWidth: 1,
        borderTopColor: colors.border,
    },
    submitButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.primary,
        paddingVertical: spacing.md,
        borderRadius: radius.md,
        gap: spacing.sm,
    },
    submitButtonDisabled: {
        opacity: 0.6,
    },
    submitButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
});
