import React, { useRef, useState, useMemo } from 'react';
import {
    View,
    Text,
    StyleSheet,
    Modal,
    Pressable,
    Dimensions,
} from 'react-native';
import SignatureScreen, { SignatureViewRef } from 'react-native-signature-canvas';
import { useTheme, spacing, radius } from '../theme';
import { Ionicons } from '@expo/vector-icons';

interface SignatureCaptureProps {
    visible: boolean;
    onClose: () => void;
    onSave: (signature: string) => void;
    title?: string;
    subtitle?: string;
}

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

export default function SignatureCapture({
    visible,
    onClose,
    onSave,
    title = 'Sign Here',
    subtitle = 'Please sign using your finger',
}: SignatureCaptureProps) {
    const { colors, isDark } = useTheme();
    const styles = useMemo(() => createStyles(colors), [colors]);
    const signatureRef = useRef<SignatureViewRef>(null);
    const [isEmpty, setIsEmpty] = useState(true);

    const handleClear = () => {
        signatureRef.current?.clearSignature();
        setIsEmpty(true);
    };

    const handleSave = () => {
        signatureRef.current?.readSignature();
    };

    const handleOK = (signature: string) => {
        if (signature) {
            onSave(signature);
            handleClear();
        }
    };

    const handleEmpty = () => {
        setIsEmpty(true);
    };

    const handleBegin = () => {
        setIsEmpty(false);
    };

    // Configure signature canvas style
    const webStyle = `
        .m-signature-pad {
            box-shadow: none;
            border: none;
            background-color: ${isDark ? '#1f1f1f' : '#ffffff'};
        }
        .m-signature-pad--body {
            border: none;
        }
        .m-signature-pad--footer {
            display: none;
        }
        body, html {
            background-color: ${isDark ? '#1f1f1f' : '#ffffff'};
        }
    `;

    return (
        <Modal
            visible={visible}
            animationType="slide"
            presentationStyle="fullScreen"
            onRequestClose={onClose}
        >
            <View style={styles.container}>
                {/* Header */}
                <View style={styles.header}>
                    <Pressable onPress={onClose} style={styles.closeButton}>
                        <Ionicons name="close" size={28} color={colors.text} />
                    </Pressable>
                    <View style={styles.headerContent}>
                        <Text style={styles.title}>{title}</Text>
                        <Text style={styles.subtitle}>{subtitle}</Text>
                    </View>
                    <View style={{ width: 44 }} />
                </View>

                {/* Signature Canvas */}
                <View style={styles.signatureContainer}>
                    <SignatureScreen
                        ref={signatureRef}
                        onOK={handleOK}
                        onEmpty={handleEmpty}
                        onBegin={handleBegin}
                        webStyle={webStyle}
                        backgroundColor={isDark ? '#1f1f1f' : '#ffffff'}
                        penColor={isDark ? '#ffffff' : '#000000'}
                        minWidth={2}
                        maxWidth={4}
                        style={styles.signature}
                    />
                    <View style={styles.signatureLine} />
                    <Text style={styles.signatureLabel}>Sign above this line</Text>
                </View>

                {/* Action Buttons */}
                <View style={styles.actions}>
                    <Pressable
                        style={[styles.actionButton, styles.clearButton]}
                        onPress={handleClear}
                    >
                        <Ionicons name="trash-outline" size={20} color={colors.danger} />
                        <Text style={[styles.actionButtonText, { color: colors.danger }]}>
                            Clear
                        </Text>
                    </Pressable>

                    <Pressable
                        style={[
                            styles.actionButton,
                            styles.saveButton,
                            isEmpty && styles.disabledButton
                        ]}
                        onPress={handleSave}
                        disabled={isEmpty}
                    >
                        <Ionicons name="checkmark" size={20} color="#fff" />
                        <Text style={styles.saveButtonText}>Save Signature</Text>
                    </Pressable>
                </View>
            </View>
        </Modal>
    );
}

const createStyles = (colors: any) => StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingHorizontal: spacing.md,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    closeButton: {
        width: 44,
        height: 44,
        alignItems: 'center',
        justifyContent: 'center',
    },
    headerContent: {
        flex: 1,
        alignItems: 'center',
    },
    title: {
        fontSize: 20,
        fontWeight: '700',
        color: colors.text,
    },
    subtitle: {
        fontSize: 14,
        color: colors.subtext,
        marginTop: 2,
    },
    signatureContainer: {
        flex: 1,
        margin: spacing.lg,
        borderRadius: radius.lg,
        overflow: 'hidden',
        borderWidth: 2,
        borderColor: colors.border,
        borderStyle: 'dashed',
    },
    signature: {
        flex: 1,
    },
    signatureLine: {
        position: 'absolute',
        bottom: 60,
        left: 20,
        right: 20,
        height: 2,
        backgroundColor: colors.subtext + '40',
    },
    signatureLabel: {
        position: 'absolute',
        bottom: 35,
        left: 0,
        right: 0,
        textAlign: 'center',
        fontSize: 12,
        color: colors.subtext,
    },
    actions: {
        flexDirection: 'row',
        padding: spacing.lg,
        paddingBottom: spacing.xl,
        gap: spacing.md,
        backgroundColor: colors.surface,
        borderTopWidth: 1,
        borderTopColor: colors.border,
    },
    actionButton: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.md,
        borderRadius: radius.md,
        gap: spacing.sm,
    },
    clearButton: {
        backgroundColor: colors.bg,
        borderWidth: 1,
        borderColor: colors.danger,
    },
    saveButton: {
        backgroundColor: colors.primary,
    },
    disabledButton: {
        opacity: 0.5,
    },
    actionButtonText: {
        fontSize: 16,
        fontWeight: '600',
    },
    saveButtonText: {
        fontSize: 16,
        fontWeight: '600',
        color: '#fff',
    },
});
