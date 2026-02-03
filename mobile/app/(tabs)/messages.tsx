import { View, Text, StyleSheet, FlatList, Pressable, RefreshControl } from 'react-native';
import { router } from 'expo-router';
import { useState, useEffect } from 'react';
import { useTheme, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import { getConversations } from '../../src/api/client';

export default function MessagesScreen() {
    const { colors } = useTheme();
    const [conversations, setConversations] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    useEffect(() => {
        loadConversations();
    }, []);

    const loadConversations = async () => {
        try {
            setLoading(true);
            const response = await getConversations();
            setConversations(response.data.data || []);
        } catch (error) {
            console.error('Error loading conversations:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    const handleRefresh = () => {
        setRefreshing(true);
        loadConversations();
    };

    const styles = createStyles(colors);

    const renderConversation = ({ item }: { item: any }) => (
        <Pressable
            style={styles.conversationCard}
            onPress={() => router.push(`/chat/${item.id}`)}
        >
            <View style={styles.avatar}>
                <Ionicons name="person" size={28} color={colors.primary} />
            </View>
            <View style={styles.conversationInfo}>
                <View style={styles.conversationHeader}>
                    <Text style={styles.userName}>{item.other_user?.name || 'Unknown'}</Text>
                    {item.last_message && (
                        <Text style={styles.timestamp}>
                            {new Date(item.last_message.created_at).toLocaleDateString()}
                        </Text>
                    )}
                </View>
                {item.last_message && (
                    <Text style={styles.lastMessage} numberOfLines={1}>
                        {item.last_message.is_mine ? 'You: ' : ''}
                        {item.last_message.body}
                    </Text>
                )}
            </View>
            {item.has_unread && <View style={styles.unreadBadge} />}
        </Pressable>
    );

    const renderEmpty = () => (
        <View style={styles.emptyContainer}>
            <Ionicons name="chatbubbles-outline" size={80} color={colors.subtext} />
            <Text style={styles.emptyText}>No conversations yet</Text>
            <Text style={styles.emptySubtext}>Start a conversation with a colleague</Text>
        </View>
    );

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Messages</Text>
            </View>

            {/* Conversations List */}
            <FlatList
                data={conversations}
                renderItem={renderConversation}
                keyExtractor={(item) => item.id.toString()}
                contentContainerStyle={[
                    styles.listContent,
                    conversations.length === 0 && styles.emptyList,
                ]}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} tintColor={colors.primary} />
                }
                ListEmptyComponent={!loading ? renderEmpty : null}
            />
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
    listContent: {
        padding: spacing.lg,
    },
    emptyList: {
        flex: 1,
    },
    conversationCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        marginBottom: spacing.md,
        gap: spacing.md,
    },
    avatar: {
        width: 50,
        height: 50,
        borderRadius: 25,
        backgroundColor: colors.bg,
        alignItems: 'center',
        justifyContent: 'center',
    },
    conversationInfo: {
        flex: 1,
    },
    conversationHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 4,
    },
    userName: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    timestamp: {
        fontSize: 12,
        color: colors.subtext,
    },
    lastMessage: {
        fontSize: 14,
        color: colors.subtext,
    },
    unreadBadge: {
        width: 10,
        height: 10,
        borderRadius: 5,
        backgroundColor: colors.primary,
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
});
