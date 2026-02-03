import {
    View,
    Text,
    StyleSheet,
    FlatList,
    TextInput,
    Pressable,
    KeyboardAvoidingView,
    Platform,
    ActivityIndicator,
} from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { useState, useEffect, useRef } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '../../src/state/auth';
import { getConversation, sendMessage } from '../../src/api/client';

export default function ChatScreen() {
    const { id } = useLocalSearchParams<{ id: string }>();
    const { user } = useAuthStore();
    const flatListRef = useRef<FlatList>(null);

    const [conversation, setConversation] = useState<any>(null);
    const [messages, setMessages] = useState<any[]>([]);
    const [inputText, setInputText] = useState('');
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);

    useEffect(() => {
        loadConversation();
        // Set up polling for new messages
        const interval = setInterval(loadConversation, 5000);
        return () => clearInterval(interval);
    }, [id]);

    const loadConversation = async () => {
        try {
            const response = await getConversation(Number(id));
            setConversation(response.data.conversation);
            setMessages(response.data.messages || []);
            setLoading(false);
        } catch (error) {
            console.error('Error loading conversation:', error);
            setLoading(false);
        }
    };

    const handleSend = async () => {
        if (!inputText.trim() || sending) return;

        const messageText = inputText.trim();
        setInputText('');
        setSending(true);

        try {
            await sendMessage(Number(id), messageText);
            // Reload to get the new message
            await loadConversation();
            // Scroll to bottom
            setTimeout(() => {
                flatListRef.current?.scrollToEnd({ animated: true });
            }, 100);
        } catch (error) {
            console.error('Error sending message:', error);
            setInputText(messageText); // Restore text on error
        } finally {
            setSending(false);
        }
    };

    const renderMessage = ({ item }: { item: any }) => (
        <View style={[styles.messageBubble, item.is_mine ? styles.myMessage : styles.theirMessage]}>
            {!item.is_mine && <Text style={styles.senderName}>{item.user?.name}</Text>}
            <Text style={[styles.messageText, item.is_mine && styles.myMessageText]}>{item.body}</Text>
            <Text style={[styles.messageTime, item.is_mine && styles.myMessageTime]}>
                {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
            </Text>
        </View>
    );

    if (loading) {
        return (
            <View style={[styles.container, styles.centerContent]}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    return (
        <KeyboardAvoidingView
            style={styles.container}
            behavior={Platform.OS === 'ios' ? 'padding' : undefined}
            keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
        >
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <View style={styles.headerInfo}>
                    <Text style={styles.headerTitle}>{conversation?.other_user?.name || 'Chat'}</Text>
                </View>
                <Pressable style={styles.headerIcon}>
                    <Ionicons name="videocam-outline" size={24} color={colors.primary} />
                </Pressable>
                <Pressable style={styles.headerIcon}>
                    <Ionicons name="call-outline" size={24} color={colors.primary} />
                </Pressable>
            </View>

            {/* Messages List */}
            <FlatList
                ref={flatListRef}
                data={messages}
                renderItem={renderMessage}
                keyExtractor={(item) => item.id.toString()}
                contentContainerStyle={styles.messagesContent}
                onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: false })}
                ListEmptyComponent={
                    <View style={styles.emptyMessages}>
                        <Text style={styles.emptyText}>No messages yet</Text>
                        <Text style={styles.emptySubtext}>Start the conversation!</Text>
                    </View>
                }
            />

            {/* Input */}
            <View style={styles.inputContainer}>
                <TextInput
                    style={styles.input}
                    value={inputText}
                    onChangeText={setInputText}
                    placeholder="Type a message..."
                    placeholderTextColor={colors.subtext}
                    multiline
                    maxLength={5000}
                />
                <Pressable
                    style={[styles.sendButton, (!inputText.trim() || sending) && styles.sendButtonDisabled]}
                    onPress={handleSend}
                    disabled={!inputText.trim() || sending}
                >
                    {sending ? (
                        <ActivityIndicator size="small" color="#fff" />
                    ) : (
                        <Ionicons name="send" size={20} color="#fff" />
                    )}
                </Pressable>
            </View>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    centerContent: {
        alignItems: 'center',
        justifyContent: 'center',
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: spacing.md,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
        gap: spacing.sm,
    },
    headerBack: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    headerInfo: {
        flex: 1,
    },
    headerTitle: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
    },
    headerIcon: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    messagesContent: {
        padding: spacing.md,
    },
    messageBubble: {
        maxWidth: '75%',
        padding: spacing.md,
        borderRadius: radius.lg,
        marginBottom: spacing.sm,
    },
    myMessage: {
        alignSelf: 'flex-end',
        backgroundColor: colors.primary,
    },
    theirMessage: {
        alignSelf: 'flex-start',
        backgroundColor: colors.surface,
    },
    senderName: {
        fontSize: 12,
        fontWeight: '600',
        color: colors.primary,
        marginBottom: 4,
    },
    messageText: {
        fontSize: 16,
        color: colors.text,
        marginBottom: 4,
    },
    myMessageText: {
        color: '#fff',
    },
    messageTime: {
        fontSize: 11,
        color: colors.subtext,
    },
    myMessageTime: {
        color: 'rgba(255, 255, 255, 0.7)',
    },
    emptyMessages: {
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.xl * 2,
    },
    emptyText: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.xs,
    },
    emptySubtext: {
        fontSize: 14,
        color: colors.subtext,
    },
    inputContainer: {
        flexDirection: 'row',
        alignItems: 'flex-end',
        padding: spacing.md,
        backgroundColor: colors.surface,
        gap: spacing.sm,
        borderTopWidth: 1,
        borderTopColor: colors.border,
    },
    input: {
        flex: 1,
        backgroundColor: colors.bg,
        borderRadius: radius.lg,
        paddingHorizontal: spacing.md,
        paddingVertical: spacing.sm,
        fontSize: 16,
        color: colors.text,
        maxHeight: 100,
    },
    sendButton: {
        width: 44,
        height: 44,
        borderRadius: 22,
        backgroundColor: colors.primary,
        alignItems: 'center',
        justifyContent: 'center',
    },
    sendButtonDisabled: {
        opacity: 0.5,
    },
});
