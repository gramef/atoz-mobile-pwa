import { View, Text, StyleSheet, FlatList, Pressable, RefreshControl, Alert } from 'react-native';
import { router } from 'expo-router';
import { useState, useEffect } from 'react';
import { colors, spacing, radius } from '../../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import { getDocuments, deleteDocument } from '../../../src/api/client';

export default function DocumentsScreen() {
    const [documents, setDocuments] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        loadDocuments();
    }, []);

    const loadDocuments = async () => {
        try {
            setLoading(true);
            const response = await getDocuments();
            setDocuments(response.data.data || []);
        } catch (error) {
            console.error('Error loading documents:', error);
            Alert.alert('Error', 'Failed to load documents');
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const handleRefresh = () => {
        setRefreshing(true);
        loadDocuments();
    };

    const handleUpload = () => {
        // Temporarily disabled - requires native rebuild
        // router.push('/documents/upload');
        Alert.alert(
            'Upload Disabled',
            'Document upload requires rebuilding the app with native modules.\n\nRun: cd mobile && npx expo run:ios',
            [{ text: 'OK' }]
        );
    };

    const handleDelete = (doc: any) => {
        Alert.alert(
            'Delete Document',
            `Are you sure you want to delete "${doc.name}"?`,
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Delete',
                    style: 'destructive',
                    onPress: async () => {
                        try {
                            await deleteDocument(doc.id);
                            Alert.alert('Success', 'Document deleted');
                            loadDocuments();
                        } catch (error) {
                            Alert.alert('Error', 'Failed to delete document');
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
        return 'document';
    };

    const renderDocument = ({ item }: { item: any }) => (
        <Pressable
            style={styles.documentCard}
            onPress={() => router.push(`/documents/${item.id}` as any)}
            onLongPress={() => handleDelete(item)}
        >
            <View style={styles.documentIcon}>
                <Ionicons name={getDocumentIcon(item.type)} size={32} color={colors.primary} />
            </View>
            <View style={styles.documentInfo}>
                <Text style={styles.documentName} numberOfLines={1}>
                    {item.name || 'Untitled Document'}
                </Text>
                <Text style={styles.documentType}>{item.type || 'Document'}</Text>
                <Text style={styles.documentDate}>{new Date(item.created_at).toLocaleDateString()}</Text>
            </View>
            <Ionicons name="chevron-forward" size={20} color={colors.subtext} />
        </Pressable >
    );

    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Ionicons name="folder-open-outline" size={80} color={colors.subtext} />
            <Text style={styles.emptyText}>No documents yet</Text>
            <Text style={styles.emptySubtext}>Upload your first document to get started</Text>
        </View>
    );

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Documents</Text>
            </View>

            {/* Document List */}
            <FlatList
                data={documents}
                renderItem={renderDocument}
                keyExtractor={(item) => item.id.toString()}
                contentContainerStyle={[
                    styles.listContent,
                    documents.length === 0 && styles.emptyList,
                ]}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor={colors.primary} />
                }
                ListEmptyComponent={!loading ? renderEmpty : null}
            />

            {/* Upload FAB */}
            <Pressable style={styles.fab} onPress={handleUpload}>
                <Ionicons name="add" size={28} color="#fff" />
            </Pressable>
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
    },
    headerTitle: {
        fontSize: 28,
        fontWeight: '700',
        color: colors.text,
    },
    listContent: {
        padding: spacing.lg,
    },
    emptyList: {
        flex: 1,
    },
    documentCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        marginBottom: spacing.md,
        gap: spacing.md,
    },
    documentIcon: {
        width: 56,
        height: 56,
        borderRadius: radius.md,
        backgroundColor: colors.bg,
        alignItems: 'center',
        justifyContent: 'center',
    },
    documentInfo: {
        flex: 1,
    },
    documentName: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
        marginBottom: 4,
    },
    documentType: {
        fontSize: 14,
        color: colors.subtext,
        marginBottom: 2,
    },
    documentDate: {
        fontSize: 12,
        color: colors.subtext,
    },
    emptyContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.xl * 2,
    },
    emptyText: {
        fontSize: 20,
        fontWeight: '600',
        color: colors.text,
        marginTop: spacing.lg,
        marginBottom: spacing.sm,
    },
    emptySubtext: {
        fontSize: 14,
        color: colors.subtext,
        textAlign: 'center',
    },
    fab: {
        position: 'absolute',
        right: spacing.lg,
        bottom: spacing.xl,
        width: 56,
        height: 56,
        borderRadius: 28,
        backgroundColor: colors.primary,
        alignItems: 'center',
        justifyContent: 'center',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
        elevation: 8,
    },
});
