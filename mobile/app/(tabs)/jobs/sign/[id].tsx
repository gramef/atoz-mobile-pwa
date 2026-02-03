import React, { useState, useEffect, useMemo } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Pressable,
    TextInput,
    ActivityIndicator,
    Alert,
    KeyboardAvoidingView,
    Platform,
} from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { useTheme, spacing, radius } from '../../../../src/ui/theme';
import { getTimesheet, signTimesheet } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import SignatureCapture from '../../../../src/ui/components/SignatureCapture';

interface Timesheet {
    id: number;
    job_id: number;
    status: string;
    has_agent_signature?: boolean;
    has_client_signature?: boolean;
    client_name?: string;
    job?: {
        id: number;
        client_reference?: string;
        to_language?: { name: string };
    };
}

type SignatureStep = 'agent' | 'client-info' | 'client' | 'complete';

export default function SignTimesheetScreen() {
    const { colors } = useTheme();
    const styles = useMemo(() => createStyles(colors), [colors]);
    const params = useLocalSearchParams();
    const timesheetId = Number(params.id);

    const [timesheet, setTimesheet] = useState<Timesheet | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [step, setStep] = useState<SignatureStep>('agent');

    // Signatures
    const [agentSignature, setAgentSignature] = useState<string>('');
    const [clientSignature, setClientSignature] = useState<string>('');
    const [showSignaturePad, setShowSignaturePad] = useState(false);
    const [currentSigner, setCurrentSigner] = useState<'agent' | 'client'>('agent');

    // Client info
    const [clientName, setClientName] = useState('');
    const [clientPhone, setClientPhone] = useState('');
    const [clientDesignation, setClientDesignation] = useState('');

    useEffect(() => {
        loadTimesheet();
    }, [timesheetId]);

    const loadTimesheet = async () => {
        try {
            setLoading(true);
            const response = await getTimesheet(timesheetId);
            setTimesheet(response.data);
            if (response.data.client_name) {
                setClientName(response.data.client_name);
            }
        } catch (error) {
            console.error('Error loading timesheet:', error);
            Alert.alert('Error', 'Failed to load timesheet');
        } finally {
            setLoading(false);
        }
    };

    const handleOpenSignaturePad = (signer: 'agent' | 'client') => {
        setCurrentSigner(signer);
        setShowSignaturePad(true);
    };

    const handleSaveSignature = (signature: string) => {
        if (currentSigner === 'agent') {
            setAgentSignature(signature);
            setStep('client-info');
        } else {
            setClientSignature(signature);
            setStep('complete');
            handleSubmit(signature);
        }
        setShowSignaturePad(false);
    };

    const handleSubmit = async (clientSig?: string) => {
        if (!timesheet) return;

        const clientSigToUse = clientSig || clientSignature;
        if (!agentSignature || !clientSigToUse || !clientName.trim()) {
            Alert.alert('Missing Information', 'Please complete all required fields');
            return;
        }

        setSubmitting(true);
        try {
            await signTimesheet(timesheet.id, {
                agent_signature: agentSignature,
                client_signature: clientSigToUse,
                client_name: clientName.trim(),
                client_phone: clientPhone.trim() || undefined,
                client_designation: clientDesignation.trim() || undefined,
            });
            Alert.alert(
                'Success!',
                'Signatures captured successfully. The timesheet has been submitted.',
                [{ text: 'OK', onPress: () => router.back() }]
            );
        } catch (error: any) {
            console.error('Error submitting signatures:', error);
            Alert.alert(
                'Error',
                error.response?.data?.message || 'Failed to submit signatures'
            );
            setStep('client');
        } finally {
            setSubmitting(false);
        }
    };

    const handleNextStep = () => {
        if (step === 'client-info') {
            if (!clientName.trim()) {
                Alert.alert('Required', 'Please enter the client name');
                return;
            }
            setStep('client');
        }
    };

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    if (!timesheet) {
        return (
            <View style={styles.errorContainer}>
                <Text style={styles.errorText}>Timesheet not found</Text>
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
                <Text style={styles.headerTitle}>Sign Timesheet</Text>
                <View style={{ width: 40 }} />
            </View>

            {/* Progress Steps */}
            <View style={styles.progressContainer}>
                <View style={styles.progressStep}>
                    <View style={[styles.stepCircle, step !== 'agent' && styles.stepComplete]}>
                        {step !== 'agent' ? (
                            <Ionicons name="checkmark" size={16} color="#fff" />
                        ) : (
                            <Text style={styles.stepNumber}>1</Text>
                        )}
                    </View>
                    <Text style={styles.stepLabel}>Agent Signs</Text>
                </View>
                <View style={styles.progressLine} />
                <View style={styles.progressStep}>
                    <View style={[
                        styles.stepCircle,
                        (step === 'client' || step === 'complete') && styles.stepComplete,
                        (step === 'client-info') && styles.stepActive
                    ]}>
                        {(step === 'client' || step === 'complete') ? (
                            <Ionicons name="checkmark" size={16} color="#fff" />
                        ) : (
                            <Text style={styles.stepNumber}>2</Text>
                        )}
                    </View>
                    <Text style={styles.stepLabel}>Client Info</Text>
                </View>
                <View style={styles.progressLine} />
                <View style={styles.progressStep}>
                    <View style={[
                        styles.stepCircle,
                        step === 'complete' && styles.stepComplete,
                        step === 'client' && styles.stepActive
                    ]}>
                        {step === 'complete' ? (
                            <Ionicons name="checkmark" size={16} color="#fff" />
                        ) : (
                            <Text style={styles.stepNumber}>3</Text>
                        )}
                    </View>
                    <Text style={styles.stepLabel}>Client Signs</Text>
                </View>
            </View>

            <KeyboardAvoidingView
                behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
                style={{ flex: 1 }}
            >
                <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                    {/* Job Info Card */}
                    <View style={styles.card}>
                        <Text style={styles.cardTitle}>
                            Job #{timesheet.job?.id || timesheet.job_id}
                        </Text>
                        {timesheet.job?.to_language && (
                            <Text style={styles.cardSubtitle}>
                                {timesheet.job.to_language.name}
                            </Text>
                        )}
                        {timesheet.job?.client_reference && (
                            <Text style={styles.cardRef}>
                                Ref: {timesheet.job.client_reference}
                            </Text>
                        )}
                    </View>

                    {/* Step Content */}
                    {step === 'agent' && (
                        <View style={styles.stepContent}>
                            <View style={styles.iconContainer}>
                                <Ionicons name="create-outline" size={64} color={colors.primary} />
                            </View>
                            <Text style={styles.stepTitle}>Agent Signature</Text>
                            <Text style={styles.stepDescription}>
                                Please sign to confirm you completed this job.
                            </Text>
                            <Pressable
                                style={styles.signButton}
                                onPress={() => handleOpenSignaturePad('agent')}
                            >
                                <Ionicons name="pencil" size={20} color="#fff" />
                                <Text style={styles.signButtonText}>Sign Now</Text>
                            </Pressable>
                        </View>
                    )}

                    {step === 'client-info' && (
                        <View style={styles.stepContent}>
                            <View style={[styles.signaturePreview, agentSignature ? styles.signatureCaptured : null]}>
                                <Ionicons name="checkmark-circle" size={24} color="#22c55e" />
                                <Text style={styles.previewText}>Agent signature captured</Text>
                            </View>

                            <Text style={styles.stepTitle}>Client Information</Text>
                            <Text style={styles.stepDescription}>
                                Enter the client's details before they sign.
                            </Text>

                            <View style={styles.formGroup}>
                                <Text style={styles.label}>Client Name *</Text>
                                <TextInput
                                    style={styles.input}
                                    value={clientName}
                                    onChangeText={setClientName}
                                    placeholder="Enter client's full name"
                                    placeholderTextColor={colors.subtext}
                                />
                            </View>

                            <View style={styles.formGroup}>
                                <Text style={styles.label}>Phone (optional)</Text>
                                <TextInput
                                    style={styles.input}
                                    value={clientPhone}
                                    onChangeText={setClientPhone}
                                    placeholder="Phone number"
                                    placeholderTextColor={colors.subtext}
                                    keyboardType="phone-pad"
                                />
                            </View>

                            <View style={styles.formGroup}>
                                <Text style={styles.label}>Designation (optional)</Text>
                                <TextInput
                                    style={styles.input}
                                    value={clientDesignation}
                                    onChangeText={setClientDesignation}
                                    placeholder="e.g. Manager, Nurse"
                                    placeholderTextColor={colors.subtext}
                                />
                            </View>

                            <Pressable style={styles.signButton} onPress={handleNextStep}>
                                <Text style={styles.signButtonText}>Continue</Text>
                                <Ionicons name="arrow-forward" size={20} color="#fff" />
                            </Pressable>
                        </View>
                    )}

                    {step === 'client' && (
                        <View style={styles.stepContent}>
                            <View style={[styles.signaturePreview, styles.signatureCaptured]}>
                                <Ionicons name="checkmark-circle" size={24} color="#22c55e" />
                                <Text style={styles.previewText}>Agent signature captured</Text>
                            </View>

                            <View style={styles.clientInfoSummary}>
                                <Text style={styles.summaryLabel}>Client: {clientName}</Text>
                                {clientPhone && <Text style={styles.summaryValue}>{clientPhone}</Text>}
                            </View>

                            <View style={styles.iconContainer}>
                                <Ionicons name="person-outline" size={64} color={colors.primary} />
                            </View>
                            <Text style={styles.stepTitle}>Client Signature</Text>
                            <Text style={styles.stepDescription}>
                                Hand the device to the client to sign.
                            </Text>
                            <Pressable
                                style={styles.signButton}
                                onPress={() => handleOpenSignaturePad('client')}
                            >
                                <Ionicons name="pencil" size={20} color="#fff" />
                                <Text style={styles.signButtonText}>Client Signs Now</Text>
                            </Pressable>
                        </View>
                    )}

                    {step === 'complete' && submitting && (
                        <View style={styles.stepContent}>
                            <ActivityIndicator size="large" color={colors.primary} />
                            <Text style={styles.stepTitle}>Submitting...</Text>
                            <Text style={styles.stepDescription}>
                                Please wait while we save your signatures.
                            </Text>
                        </View>
                    )}
                </ScrollView>
            </KeyboardAvoidingView>

            {/* Signature Capture Modal */}
            <SignatureCapture
                visible={showSignaturePad}
                onClose={() => setShowSignaturePad(false)}
                onSave={handleSaveSignature}
                title={currentSigner === 'agent' ? 'Agent Signature' : 'Client Signature'}
                subtitle={currentSigner === 'agent'
                    ? 'Sign to confirm job completion'
                    : `${clientName}, please sign below`}
            />
        </View>
    );
}

const createStyles = (colors: any) => StyleSheet.create({
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
    progressContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.lg,
        backgroundColor: colors.surface,
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    progressStep: {
        alignItems: 'center',
    },
    progressLine: {
        width: 40,
        height: 2,
        backgroundColor: colors.border,
        marginHorizontal: spacing.sm,
    },
    stepCircle: {
        width: 28,
        height: 28,
        borderRadius: 14,
        backgroundColor: colors.bg,
        borderWidth: 2,
        borderColor: colors.border,
        alignItems: 'center',
        justifyContent: 'center',
    },
    stepActive: {
        borderColor: colors.primary,
        backgroundColor: colors.primary + '20',
    },
    stepComplete: {
        backgroundColor: '#22c55e',
        borderColor: '#22c55e',
    },
    stepNumber: {
        fontSize: 12,
        fontWeight: '600',
        color: colors.subtext,
    },
    stepLabel: {
        fontSize: 10,
        color: colors.subtext,
        marginTop: 4,
    },
    content: {
        flex: 1,
    },
    contentContainer: {
        padding: spacing.lg,
    },
    card: {
        backgroundColor: colors.surface,
        borderRadius: radius.lg,
        padding: spacing.md,
        marginBottom: spacing.lg,
    },
    cardTitle: {
        fontSize: 18,
        fontWeight: '700',
        color: colors.text,
    },
    cardSubtitle: {
        fontSize: 15,
        color: colors.primary,
        marginTop: 2,
    },
    cardRef: {
        fontSize: 13,
        color: colors.subtext,
        marginTop: 4,
    },
    stepContent: {
        alignItems: 'center',
        paddingVertical: spacing.lg,
    },
    iconContainer: {
        marginBottom: spacing.md,
    },
    stepTitle: {
        fontSize: 22,
        fontWeight: '700',
        color: colors.text,
        marginBottom: spacing.sm,
    },
    stepDescription: {
        fontSize: 15,
        color: colors.subtext,
        textAlign: 'center',
        marginBottom: spacing.lg,
        paddingHorizontal: spacing.lg,
    },
    signButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.primary,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.xl,
        borderRadius: radius.md,
        gap: spacing.sm,
        minWidth: 200,
    },
    signButtonText: {
        fontSize: 16,
        fontWeight: '600',
        color: '#fff',
    },
    signaturePreview: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.md,
        marginBottom: spacing.lg,
        width: '100%',
    },
    signatureCaptured: {
        backgroundColor: '#22c55e20',
    },
    previewText: {
        fontSize: 14,
        color: colors.text,
    },
    formGroup: {
        width: '100%',
        marginBottom: spacing.md,
    },
    label: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
        marginBottom: spacing.xs,
    },
    input: {
        backgroundColor: colors.surface,
        borderRadius: radius.md,
        padding: spacing.md,
        fontSize: 16,
        color: colors.text,
        borderWidth: 1,
        borderColor: colors.border,
    },
    clientInfoSummary: {
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.md,
        marginBottom: spacing.lg,
        width: '100%',
    },
    summaryLabel: {
        fontSize: 15,
        fontWeight: '600',
        color: colors.text,
    },
    summaryValue: {
        fontSize: 14,
        color: colors.subtext,
        marginTop: 2,
    },
});
