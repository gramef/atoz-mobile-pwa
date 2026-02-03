import { View, Text, StyleSheet, ScrollView, ActivityIndicator, Alert, Pressable, Linking } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { useState, useEffect } from 'react';
import { useTheme, spacing, radius, typography } from '../../../src/ui/theme';
import Screen from '../../../src/ui/components/Screen';
import Button from '../../../src/ui/components/Button';
import { Ionicons } from '@expo/vector-icons';
import { getDocument, deleteDocument } from '../../../src/api/client';
import Constants from 'expo-constants';

const API_BASE_URL = Constants.expoConfig?.extra?.apiBaseUrl || process.env.EXPO_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000/api';

export default function DocumentDetailScreen() {
    const { colors } = useTheme();
    const { id } = useLocalSearchParams();
    const [document, setDocument] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(false);

    useEffect(() => {
        loadDocument();
    }, [id]);

    const loadDocument = async () => {
        try {
            setLoading(true);
            const response = await getDocument(Number(id));
            setDocument(response.data);
        } catch (error) {
            console.error('Error loading document:', error);
            Alert.alert('Error', 'Failed to load document details');
        } finally {
            setLoading(false);
        }
    };

    const handleDownload = async () => {
        try {
            setActionLoading(true);
            // Open the download URL in browser
            const downloadUrl = `${API_BASE_URL}/documents/${id}/download`;
            const canOpen = await Linking.canOpenURL(downloadUrl);
            if (canOpen) {
                await Linking.openURL(downloadUrl);
            } else {
                Alert.alert('Error', 'Unable to open download link');
            }
        } catch (error) {
            console.error('Error downloading document:', error);
            Alert.alert('Error', 'Failed to download document');
        } finally {
            setActionLoading(false);
        }
    };

    const handleDelete = () => {
        Alert.alert(
            'Delete Document',
            `Are you sure you want to delete "${document?.name}"?`,
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Delete',
                    style: 'destructive',
                    onPress: async () => {
                        try {
                            setActionLoading(true);
                            await deleteDocument(Number(id));
                            Alert.alert('Success', 'Document deleted');
                            router.back();
                        } catch (error) {
                            Alert.alert('Error', 'Failed to delete document');
                        } finally {
                            setActionLoading(false);
                        }
                    },
                },
            ]
        );
    };

    const getDocumentIcon = (type: any) => {
        const typeStr = String(type || '').toLowerCase();
        if (typeStr.includes('pdf')) return 'document-text';
        if (typeStr.includes('image') || typeStr.includes('photo')) return 'image';
        if (typeStr.includes('word') || typeStr.includes('doc')) return 'document';
        return 'document-outline';
    };

    const formatFileSize = (bytes: number) => {
        if (!bytes) return 'Unknown size';
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    };

    const styles = StyleSheet.create({
        container: {
            flex: 1,
            backgroundColor: colors.bg,
        },
        content: {
            padding: spacing.lg,
        },
        header: {
            flexDirection: 'row',
            alignItems: 'center',
            paddingHorizontal: spacing.lg,
            paddingVertical: spacing.md,
            backgroundColor: colors.surface,
            borderBottomWidth: 1,
            borderBottomColor: colors.border,
        },
        backButton: {
            padding: spacing.sm,
            marginRight: spacing.sm,
        },
        headerTitle: {
            ...typography.title,
            color: colors.text,
            flex: 1,
        },
        card: {
            backgroundColor: colors.surface,
            borderRadius: radius.lg,
            padding: spacing.xl,
            marginBottom: spacing.lg,
            alignItems: 'center',
            shadowColor: '#000',
            shadowOpacity: 0.1,
            shadowRadius: 10,
            shadowOffset: { width: 0, height: 4 },
            elevation: 3,
        },
        iconContainer: {
            width: 80,
            height: 80,
            borderRadius: 40,
            backgroundColor: '#e3f2fd',
            justifyContent: 'center',
            alignItems: 'center',
            marginBottom: spacing.md,
        },
        documentName: {
            ...typography.h1,
            color: colors.text,
            textAlign: 'center',
            marginBottom: spacing.sm,
        },
        documentType: {
            ...typography.body,
            color: colors.subtext,
            marginBottom: spacing.xs,
        },
        documentDate: {
            ...typography.hint,
            color: colors.subtext,
        },
        infoSection: {
            backgroundColor: colors.surface,
            borderRadius: radius.lg,
            padding: spacing.lg,
            marginBottom: spacing.lg,
        },
        infoRow: {
            flexDirection: 'row',
            justifyContent: 'space-between',
            paddingVertical: spacing.sm,
            borderBottomWidth: 1,
            borderBottomColor: colors.border,
        },
        infoLabel: {
            ...typography.body,
            color: colors.subtext,
        },
        infoValue: {
            ...typography.body,
            color: colors.text,
            fontWeight: '600',
        },
        buttonsContainer: {
            gap: spacing.md,
        },
        loadingContainer: {
            flex: 1,
            justifyContent: 'center',
            alignItems: 'center',
        },
    });

    if (loading) {
        return (
            <Screen>
                <View style={styles.header}>
                    <Pressable style={styles.backButton} onPress={() => router.back()}>
                        <Ionicons name="arrow-back" size={24} color={colors.text} />
                    </Pressable>
                    <Text style={styles.headerTitle}>Document</Text>
                </View>
                <View style={styles.loadingContainer}>
                    <ActivityIndicator size="large" color={colors.primary} />
                    <Text style={{ color: colors.subtext, marginTop: spacing.md }}>Loading document...</Text>
                </View>
            </Screen>
        );
    }

    if (!document) {
        return (
            <Screen>
                <View style={styles.header}>
                    <Pressable style={styles.backButton} onPress={() => router.back()}>
                        <Ionicons name="arrow-back" size={24} color={colors.text} />
                    </Pressable>
                    <Text style={styles.headerTitle}>Document</Text>
                </View>
                <View style={styles.loadingContainer}>
                    <Ionicons name="document-outline" size={64} color={colors.subtext} />
                    <Text style={{ color: colors.subtext, marginTop: spacing.md }}>Document not found</Text>
                    <Button title="Go Back" onPress={() => router.back()} variant="secondary" style={{ marginTop: spacing.lg }} />
                </View>
            </Screen>
        );
    }

    return (
        <Screen>
            <View style={styles.header}>
                <Pressable style={styles.backButton} onPress={() => router.back()}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Document</Text>
            </View>
            <ScrollView style={styles.container} contentContainerStyle={styles.content}>
                {/* Document Card */}
                <View style={styles.card}>
                    <View style={styles.iconContainer}>
                        <Ionicons name={getDocumentIcon(document.type)} size={40} color={colors.primary} />
                    </View>
                    <Text style={styles.documentName}>{document.name || 'Untitled Document'}</Text>
                    <Text style={styles.documentType}>{document.type_name || document.type || 'Document'}</Text>
                    {document.created_at && (
                        <Text style={styles.documentDate}>
                            Uploaded: {new Date(document.created_at).toLocaleDateString()}
                        </Text>
                    )}
                </View>

                {/* Document Info */}
                <View style={styles.infoSection}>
                    {document.size && (
                        <View style={styles.infoRow}>
                            <Text style={styles.infoLabel}>File Size</Text>
                            <Text style={styles.infoValue}>{formatFileSize(document.size)}</Text>
                        </View>
                    )}
                    {document.mime_type && (
                        <View style={styles.infoRow}>
                            <Text style={styles.infoLabel}>File Type</Text>
                            <Text style={styles.infoValue}>{document.mime_type}</Text>
                        </View>
                    )}
                    {document.job_id && (
                        <View style={styles.infoRow}>
                            <Text style={styles.infoLabel}>Job Reference</Text>
                            <Text style={styles.infoValue}>#{document.job_id}</Text>
                        </View>
                    )}
                </View>

                {/* Action Buttons */}
                <View style={styles.buttonsContainer}>
                    <Button
                        title={actionLoading ? 'Opening...' : '📥 Download / View'}
                        onPress={handleDownload}
                        variant="primary"
                        disabled={actionLoading}
                    />
                    <Button
                        title="🗑️ Delete Document"
                        onPress={handleDelete}
                        variant="outline"
                        style={{ borderColor: colors.danger }}
                        textStyle={{ color: colors.danger }}
                        disabled={actionLoading}
                    />
                </View>
            </ScrollView>
        </Screen>
    );
}
