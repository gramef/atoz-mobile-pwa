import { View, Text, StyleSheet, ScrollView, Pressable, TextInput, KeyboardAvoidingView, Platform, ActivityIndicator, Alert } from 'react-native';
import { router } from 'expo-router';
import { useState, useEffect } from 'react';
import { colors, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '../../src/state/auth';
import { updateProfile } from '../../src/api/client';

const TITLES = ['Mr', 'Mrs', 'Miss', 'Ms', 'Dr', 'Prof'];

export default function EditProfileScreen() {
    const { user, setAuth, token } = useAuthStore();

    const [title, setTitle] = useState(user?.title || '');
    const [firstName, setFirstName] = useState(user?.name?.split(' ')[0] || '');
    const [lastName, setLastName] = useState(user?.name?.split(' ').slice(1).join(' ') || '');
    const [email, setEmail] = useState(user?.email || '');
    const [phone, setPhone] = useState(user?.phone || '');
    const [showTitlePicker, setShowTitlePicker] = useState(false);

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(false);

    const validate = () => {
        const newErrors: Record<string, string> = {};

        if (!firstName.trim()) {
            newErrors.firstName = 'First name is required';
        }

        if (!lastName.trim()) {
            newErrors.lastName = 'Last name is required';
        }

        if (!email.trim()) {
            newErrors.email = 'Email is required';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            newErrors.email = 'Invalid email format';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSave = async () => {
        if (!validate()) return;

        setLoading(true);
        try {
            const response = await updateProfile({
                title,
                firstName: firstName.trim(),
                lastName: lastName.trim(),
                email: email.trim(),
                phone: phone.trim(),
            });

            // Update auth store with new user data
            if (token) {
                await setAuth(token, response.data.user);
            }

            Alert.alert('Success', 'Profile updated successfully!');
            router.back();
        } catch (error: any) {
            Alert.alert('Error', error.response?.data?.message || 'Failed to update profile');
        } finally {
            setLoading(false);
        }
    };

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Edit Profile</Text>
            </View>

            <KeyboardAvoidingView
                behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
                style={{ flex: 1 }}
            >
                <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                    {/* Profile Photo Placeholder */}
                    <View style={styles.photoSection}>
                        <View style={styles.photoPlaceholder}>
                            <Ionicons name="person" size={48} color={colors.subtext} />
                        </View>
                        <Pressable>
                            <Text style={styles.changePhotoText}>Change Photo</Text>
                        </Pressable>
                    </View>

                    {/* Form */}
                    <View style={styles.form}>
                        {/* Title Selector */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Title</Text>
                            <Pressable
                                style={styles.pickerButton}
                                onPress={() => setShowTitlePicker(!showTitlePicker)}
                            >
                                <Text style={styles.pickerButtonText}>{title || 'Select Title'}</Text>
                                <Ionicons name="chevron-down" size={20} color={colors.subtext} />
                            </Pressable>
                            {showTitlePicker && (
                                <View style={styles.pickerDropdown}>
                                    {TITLES.map((t) => (
                                        <Pressable
                                            key={t}
                                            style={styles.pickerOption}
                                            onPress={() => {
                                                setTitle(t);
                                                setShowTitlePicker(false);
                                            }}
                                        >
                                            <Text style={styles.pickerOptionText}>{t}</Text>
                                            {title === t && <Ionicons name="checkmark" size={20} color={colors.primary} />}
                                        </Pressable>
                                    ))}
                                </View>
                            )}
                        </View>

                        {/* First Name */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>First Name *</Text>
                            <TextInput
                                style={[styles.input, errors.firstName ? styles.inputError : null]}
                                value={firstName}
                                onChangeText={(text) => {
                                    setFirstName(text);
                                    if (errors.firstName) setErrors({ ...errors, firstName: '' });
                                }}
                                placeholder="Enter first name"
                                placeholderTextColor={colors.subtext}
                            />
                            {errors.firstName && <Text style={styles.errorText}>{errors.firstName}</Text>}
                        </View>

                        {/* Last Name */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Last Name *</Text>
                            <TextInput
                                style={[styles.input, errors.lastName ? styles.inputError : null]}
                                value={lastName}
                                onChangeText={(text) => {
                                    setLastName(text);
                                    if (errors.lastName) setErrors({ ...errors, lastName: '' });
                                }}
                                placeholder="Enter last name"
                                placeholderTextColor={colors.subtext}
                            />
                            {errors.lastName && <Text style={styles.errorText}>{errors.lastName}</Text>}
                        </View>

                        {/* Email */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Email *</Text>
                            <TextInput
                                style={[styles.input, errors.email ? styles.inputError : null]}
                                value={email}
                                onChangeText={(text) => {
                                    setEmail(text);
                                    if (errors.email) setErrors({ ...errors, email: '' });
                                }}
                                placeholder="Enter email"
                                placeholderTextColor={colors.subtext}
                                keyboardType="email-address"
                                autoCapitalize="none"
                            />
                            {errors.email && <Text style={styles.errorText}>{errors.email}</Text>}
                        </View>

                        {/* Phone */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>Phone</Text>
                            <TextInput
                                style={styles.input}
                                value={phone}
                                onChangeText={setPhone}
                                placeholder="Enter phone number"
                                placeholderTextColor={colors.subtext}
                                keyboardType="phone-pad"
                            />
                        </View>
                    </View>

                    {/* Save Button */}
                    <Pressable
                        style={[styles.saveButton, loading && styles.buttonDisabled]}
                        onPress={handleSave}
                        disabled={loading}
                    >
                        {loading ? (
                            <ActivityIndicator color="#fff" />
                        ) : (
                            <Text style={styles.saveButtonText}>Save Changes</Text>
                        )}
                    </Pressable>
                </ScrollView>
            </KeyboardAvoidingView>
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
    photoSection: {
        alignItems: 'center',
        marginBottom: spacing.xl,
    },
    photoPlaceholder: {
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: colors.surface,
        alignItems: 'center',
        justifyContent: 'center',
        borderWidth: 3,
        borderColor: colors.primary,
        marginBottom: spacing.md,
    },
    changePhotoText: {
        fontSize: 16,
        color: colors.primary,
        fontWeight: '600',
    },
    form: {
        gap: spacing.lg,
    },
    inputGroup: {
        gap: spacing.sm,
    },
    label: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    input: {
        backgroundColor: colors.surface,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: radius.md,
        paddingHorizontal: spacing.md,
        paddingVertical: 14,
        fontSize: 16,
        color: colors.text,
    },
    inputError: {
        borderColor: colors.danger,
    },
    errorText: {
        fontSize: 12,
        color: colors.danger,
    },
    pickerButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.surface,
        borderWidth: 1,
        borderColor: colors.border,
        borderRadius: radius.md,
        paddingHorizontal: spacing.md,
        paddingVertical: 14,
    },
    pickerButtonText: {
        fontSize: 16,
        color: colors.text,
    },
    pickerDropdown: {
        backgroundColor: colors.surface,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        marginTop: spacing.xs,
    },
    pickerOption: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        padding: spacing.md,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    pickerOptionText: {
        fontSize: 16,
        color: colors.text,
    },
    saveButton: {
        backgroundColor: colors.primary,
        paddingVertical: 16,
        borderRadius: radius.lg,
        alignItems: 'center',
        marginTop: spacing.xl,
    },
    saveButtonText: {
        color: '#fff',
        fontSize: 16,
        fontWeight: '600',
    },
    buttonDisabled: {
        opacity: 0.6,
    },
});
