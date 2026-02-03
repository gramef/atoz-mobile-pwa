import { View, Text, StyleSheet, ScrollView, Pressable, ActivityIndicator, Alert } from 'react-native';
import { router } from 'expo-router';
import { useState } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import * as DocumentPicker from 'expo-document-picker';
import * as ImagePicker from 'expo-image-picker';
import { uploadDocument } from '../../src/api/client';

export default function UploadDocumentScreen() {
    const [selectedFile, setSelectedFile] = useState<any>(null);
    const [uploading, setUploading] = useState(false);
    const [uploadProgress, setUploadProgress] = useState(0);

    const handlePickDocument = async () => {
        try {
            const result = await DocumentPicker.getDocumentAsync({
                type: ['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                copyToCacheDirectory: true,
            });

            if (!result.canceled && result.assets && result.assets.length > 0) {
                const file = result.assets[0];
                setSelectedFile(file);
            }
        } catch (error) {
            console.error('Error picking document:', error);
            Alert.alert('Error', 'Failed to pick document');
        }
    };

    const handleTakePhoto = async () => {
        try {
            const permission = await ImagePicker.requestCameraPermissionsAsync();
            if (!permission.granted) {
                Alert.alert('Permission Required', 'Camera permission is required to take photos');
                return;
            }

            const result = await ImagePicker.launchCameraAsync({
                mediaTypes: ImagePicker.MediaTypeOptions.Images,
                allowsEditing: true,
                quality: 0.8,
            });

            if (!result.canceled && result.assets && result.assets.length > 0) {
                const photo = result.assets[0];
                setSelectedFile(photo);
            }
        } catch (error) {
            console.error('Error taking photo:', error);
            Alert.alert('Error', 'Failed to take photo');
        }
    };

    const handleUpload = async () => {
        if (!selectedFile) {
            Alert.alert('Error', 'Please select a file first');
            return;
        }

        setUploading(true);
        setUploadProgress(0);

        try {
            const formData = new FormData();

            // @ts-ignore - React Native FormData handles file objects
            formData.append('file', {
                uri: selectedFile.uri,
                type: selectedFile.mimeType || 'application/octet-stream',
                name: selectedFile.name || 'document.pdf',
            });

            await uploadDocument(formData, (progressEvent) => {
                const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                setUploadProgress(progress);
            });

            Alert.alert('Success', 'Document uploaded successfully!');
            router.back();
        } catch (error: any) {
            console.error('Upload error:', error);
            Alert.alert('Error', error.response?.data?.message || 'Failed to upload document');
        } finally {
            setUploading(false);
        }
    };

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Upload Document</Text>
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Upload Options */}
                <View style={styles.optionsContainer}>
                    <Pressable style={styles.optionButton} onPress={handlePickDocument}>
                        <View style={styles.optionIcon}>
                            <Ionicons name="document-text-outline" size={32} color={colors.primary} />
                        </View>
                        <Text style={styles.optionText}>Choose File</Text>
                        <Text style={styles.optionSubtext}>Select from device</Text>
                    </Pressable>

                    <Pressable style={styles.optionButton} onPress={handleTakePhoto}>
                        <View style={styles.optionIcon}>
                            <Ionicons name="camera-outline" size={32} color={colors.primary} />
                        </View>
                        <Text style={styles.optionText}>Take Photo</Text>
                        <Text style={styles.optionSubtext}>Scan document</Text>
                    </Pressable>
                </View>

                {/* Selected File Preview */}
                {selectedFile && (
                    <View style={styles.previewContainer}>
                        <Text style={styles.previewLabel}>Selected File:</Text>
                        <View style={styles.fileCard}>
                            <Ionicons
                                name={selectedFile.mimeType?.includes('image') ? 'image' : 'document-text'}
                                size={32}
                                color={colors.primary}
                            />
                            <View style={styles.fileInfo}>
                                <Text style={styles.fileName} numberOfLines={1}>
                                    {selectedFile.name}
                                </Text>
                                <Text style={styles.fileSize}>
                                    {selectedFile.size ? `${(selectedFile.size / 1024 / 1024).toFixed(2)} MB` : 'Unknown size'}
                                </Text>
                            </View>
                            <Pressable onPress={() => setSelectedFile(null)}>
                                <Ionicons name="close-circle" size={24} color={colors.danger} />
                            </Pressable>
                        </View>
                    </View>
                )}

                {/* Upload Progress */}
                {uploading && (
                    <View style={styles.progressContainer}>
                        <ActivityIndicator size="large" color={colors.primary} />
                        <Text style={styles.progressText}>Uploading... {uploadProgress}%</Text>
                        <View style={styles.progressBar}>
                            <View style={[styles.progressFill, { width: `${uploadProgress}%` }]} />
                        </View>
                    </View>
                )}

                {/* Upload Button */}
                {selectedFile && !uploading && (
                    <Pressable style={styles.uploadButton} onPress={handleUpload}>
                        <Ionicons name="cloud-upload-outline" size={20} color="#fff" />
                        <Text style={styles.uploadButtonText}>Upload Document</Text>
                    </Pressable>
                )}
            </ScrollView>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: colors.bg,
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: spacing.lg,
        paddingTop: spacing.xl,
        paddingBottom: spacing.md,
        backgroundColor: colors.surface,
        gap: spacing.md,
    },
    headerBack: {
        width: 40,
        height: 40,
        alignItems: 'center',
        justifyContent: 'center',
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: '600',
        color: colors.text,
    },
    content: {
        flex: 1,
    },
    contentContainer: {
        padding: spacing.lg,
    },
    optionsContainer: {
        flexDirection: 'row',
        gap: spacing.md,
        marginBottom: spacing.xl,
    },
    optionButton: {
        flex: 1,
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.lg,
        alignItems: 'center',
        borderWidth: 2,
        borderColor: colors.border,
    },
    optionIcon: {
        width: 64,
        height: 64,
        borderRadius: radius.md,
        backgroundColor: colors.bg,
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: spacing.md,
    },
    optionText: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.xs,
    },
    optionSubtext: {
        fontSize: 12,
        color: colors.subtext,
    },
    previewContainer: {
        marginBottom: spacing.xl,
    },
    previewLabel: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    fileCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        gap: spacing.md,
    },
    fileInfo: {
        flex: 1,
    },
    fileName: {
        fontSize: 16,
        fontWeight: '500',
        color: colors.text,
        marginBottom: 4,
    },
    fileSize: {
        fontSize: 14,
        color: colors.subtext,
    },
    progressContainer: {
        alignItems: 'center',
        padding: spacing.xl,
    },
    progressText: {
        fontSize: 16,
        color: colors.text,
        marginVertical: spacing.md,
    },
    progressBar: {
        width: '100%',
        height: 8,
        backgroundColor: colors.border,
        borderRadius: radius.sm,
        overflow: 'hidden',
    },
    progressFill: {
        height: '100%',
        backgroundColor: colors.primary,
    },
    uploadButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        gap: spacing.sm,
    },
    uploadButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
});
