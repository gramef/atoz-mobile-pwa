import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    Pressable,
    TextInput,
    ActivityIndicator,
    Alert,
    Platform
} from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { useState, useEffect, useMemo } from 'react';
import { useTheme, spacing, radius } from '../../../../src/ui/theme';
import { getInterpreterJob, createTimesheet, addTimesheetExpense } from '../../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';


interface Job {
    id: number;
    client_reference?: string;
    appointment_date?: string;
    start_time?: string;
    to_language?: { name: string };
    from_language?: { name: string };
}

interface Expense {
    type: string;
    amount: string;
}

export default function CreateTimesheetScreen() {
    const { colors } = useTheme();
    const styles = useMemo(() => createStyles(colors), [colors]);
    const params = useLocalSearchParams();
    const jobId = Number(params.jobId);

    const [job, setJob] = useState<Job | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);

    // Form fields
    const [startTime, setStartTime] = useState(new Date());
    const [endTime, setEndTime] = useState(new Date());
    const [notes, setNotes] = useState('');
    const [expenses, setExpenses] = useState<Expense[]>([]);

    // Time picker visibility
    const [showStartPicker, setShowStartPicker] = useState(false);
    const [showEndPicker, setShowEndPicker] = useState(false);

    useEffect(() => {
        loadJobDetails();
    }, [jobId]);

    const loadJobDetails = async () => {
        try {
            setLoading(true);
            const response = await getInterpreterJob(jobId);
            setJob(response.data);

            // Set default times from job if available
            if (response.data.start_time) {
                const [hours, minutes] = response.data.start_time.split(':');
                const defaultStart = new Date();
                defaultStart.setHours(parseInt(hours), parseInt(minutes), 0);
                setStartTime(defaultStart);

                // Set end time to start + 8 hours by default
                const defaultEnd = new Date(defaultStart);
                defaultEnd.setHours(defaultStart.getHours() + 8);
                setEndTime(defaultEnd);
            }
        } catch (error) {
            console.error('Error loading job:', error);
            Alert.alert('Error', 'Failed to load job details');
        } finally {
            setLoading(false);
        }
    };

    const formatTimeToString = (date: Date): string => {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    };

    const formatTimeDisplay = (date: Date): string => {
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        return `${displayHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
    };

    const calculateDuration = (): { hours: number; minutes: number } => {
        const diffMs = endTime.getTime() - startTime.getTime();
        const diffMinutes = Math.floor(diffMs / 60000);
        return {
            hours: Math.floor(diffMinutes / 60),
            minutes: diffMinutes % 60
        };
    };

    const addExpense = () => {
        setExpenses([...expenses, { type: '', amount: '' }]);
    };

    const updateExpense = (index: number, field: 'type' | 'amount', value: string) => {
        const updated = [...expenses];
        updated[index][field] = value;
        setExpenses(updated);
    };

    const removeExpense = (index: number) => {
        setExpenses(expenses.filter((_, i) => i !== index));
    };

    const validateForm = (): boolean => {
        if (endTime <= startTime) {
            Alert.alert('Invalid Time', 'End time must be after start time');
            return false;
        }

        // Validate expenses
        for (const expense of expenses) {
            if (!expense.type.trim()) {
                Alert.alert('Invalid Expense', 'Please enter expense type');
                return false;
            }
            if (!expense.amount || parseFloat(expense.amount) <= 0) {
                Alert.alert('Invalid Expense', 'Please enter valid expense amount');
                return false;
            }
        }

        return true;
    };

    const handleSubmit = async () => {
        if (!validateForm()) return;

        setSubmitting(true);
        try {
            // Create timesheet
            const timesheetData = {
                job_id: jobId,
                agent_start_time: formatTimeToString(startTime),
                agent_end_time: formatTimeToString(endTime),
                notes: notes.trim() || undefined,
            };

            const response = await createTimesheet(timesheetData);
            const timesheetId = response.data.timesheet.id;

            // Add expenses if any
            for (const expense of expenses) {
                if (expense.type.trim() && expense.amount) {
                    await addTimesheetExpense(timesheetId, {
                        type: expense.type.trim(),
                        amount: parseFloat(expense.amount),
                    });
                }
            }

            Alert.alert('Success', 'Timesheet created successfully', [
                {
                    text: 'OK',
                    onPress: () => router.replace('/timesheets'),
                },
            ]);
        } catch (error: any) {
            console.error('Error creating timesheet:', error);
            Alert.alert('Error', error.response?.data?.message || 'Failed to create timesheet');
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={colors.primary} />
            </View>
        );
    }

    if (!job) {
        return (
            <View style={styles.errorContainer}>
                <Text style={styles.errorText}>Job not found</Text>
                <Pressable style={styles.backButton} onPress={() => router.back()}>
                    <Text style={styles.backButtonText}>Go Back</Text>
                </Pressable>
            </View>
        );
    }

    const duration = calculateDuration();
    const totalExpenses = expenses.reduce((sum, exp) => sum + (parseFloat(exp.amount) || 0), 0);

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Pressable onPress={() => router.back()} style={styles.headerBack}>
                    <Ionicons name="arrow-back" size={24} color={colors.text} />
                </Pressable>
                <Text style={styles.headerTitle}>Create Timesheet</Text>
                <View style={{ width: 40 }} />
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* Job Info Card */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="briefcase-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Job Details</Text>
                    </View>
                    <Text style={styles.jobLanguage}>{job.to_language?.name || 'N/A'}</Text>
                    <Text style={styles.jobReference}>
                        {job.client_reference || `Job #${job.id}`}
                    </Text>
                    {job.appointment_date && (
                        <Text style={styles.jobDate}>
                            {new Date(job.appointment_date).toLocaleDateString('en-GB', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                            })}
                        </Text>
                    )}
                </View>

                {/* Time Entry Card */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="time-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Time Entry</Text>
                    </View>

                    {/* Start Time */}
                    <View style={styles.formField}>
                        <Text style={styles.fieldLabel}>Start Time</Text>
                        <Pressable
                            style={styles.timeButton}
                            onPress={() => setShowStartPicker(true)}
                        >
                            <Ionicons name="time-outline" size={20} color={colors.text} />
                            <Text style={styles.timeButtonText}>{formatTimeDisplay(startTime)}</Text>
                            <Ionicons name="chevron-down-outline" size={20} color={colors.subtext} />
                        </Pressable>
                    </View>

                    {/* End Time */}
                    <View style={styles.formField}>
                        <Text style={styles.fieldLabel}>End Time</Text>
                        <Pressable
                            style={styles.timeButton}
                            onPress={() => setShowEndPicker(true)}
                        >
                            <Ionicons name="time-outline" size={20} color={colors.text} />
                            <Text style={styles.timeButtonText}>{formatTimeDisplay(endTime)}</Text>
                            <Ionicons name="chevron-down-outline" size={20} color={colors.subtext} />
                        </Pressable>
                    </View>

                    {/* Duration Display */}
                    <View style={styles.durationContainer}>
                        <View style={styles.durationRow}>
                            <Text style={styles.durationLabel}>Duration</Text>
                            <Text style={styles.durationValue}>
                                {duration.hours}h {duration.minutes}m
                            </Text>
                        </View>
                    </View>
                </View>

                {/* Notes Card */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="document-text-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Notes (Optional)</Text>
                    </View>
                    <TextInput
                        style={styles.textArea}
                        placeholder="Add any notes about this timesheet..."
                        placeholderTextColor={colors.subtext}
                        value={notes}
                        onChangeText={setNotes}
                        multiline
                        numberOfLines={4}
                        textAlignVertical="top"
                    />
                </View>

                {/* Expenses Card */}
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <Ionicons name="receipt-outline" size={20} color={colors.primary} />
                        <Text style={styles.cardTitle}>Expenses</Text>
                    </View>

                    {expenses.map((expense, index) => (
                        <View key={index} style={styles.expenseItem}>
                            <View style={styles.expenseInputs}>
                                <TextInput
                                    style={[styles.input, { flex: 2 }]}
                                    placeholder="Type (e.g., Travel)"
                                    placeholderTextColor={colors.subtext}
                                    value={expense.type}
                                    onChangeText={(value) => updateExpense(index, 'type', value)}
                                />
                                <TextInput
                                    style={[styles.input, { flex: 1 }]}
                                    placeholder="Amount"
                                    placeholderTextColor={colors.subtext}
                                    value={expense.amount}
                                    onChangeText={(value) => updateExpense(index, 'amount', value)}
                                    keyboardType="decimal-pad"
                                />
                            </View>
                            <Pressable
                                style={styles.removeButton}
                                onPress={() => removeExpense(index)}
                            >
                                <Ionicons name="trash-outline" size={20} color={colors.danger} />
                            </Pressable>
                        </View>
                    ))}

                    <Pressable style={styles.addExpenseButton} onPress={addExpense}>
                        <Ionicons name="add-circle-outline" size={20} color={colors.primary} />
                        <Text style={styles.addExpenseText}>Add Expense</Text>
                    </Pressable>

                    {expenses.length > 0 && (
                        <View style={styles.totalExpenses}>
                            <Text style={styles.totalExpensesLabel}>Total Expenses</Text>
                            <Text style={styles.totalExpensesValue}>£{totalExpenses.toFixed(2)}</Text>
                        </View>
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
                            <Text style={styles.submitButtonText}>Submit Timesheet</Text>
                        </>
                    )}
                </Pressable>
            </View>

            {/* Time Pickers */}
            {showStartPicker && (
                <DateTimePicker
                    value={startTime}
                    mode="time"
                    is24Hour={false}
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(event, selectedDate) => {
                        setShowStartPicker(Platform.OS === 'ios');
                        if (selectedDate) {
                            setStartTime(selectedDate);
                        }
                    }}
                />
            )}

            {showEndPicker && (
                <DateTimePicker
                    value={endTime}
                    mode="time"
                    is24Hour={false}
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(event, selectedDate) => {
                        setShowEndPicker(Platform.OS === 'ios');
                        if (selectedDate) {
                            setEndTime(selectedDate);
                        }
                    }}
                />
            )}
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
        gap: spacing.md,
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
    jobLanguage: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
    },
    jobReference: {
        fontSize: 14,
        color: colors.subtext,
    },
    jobDate: {
        fontSize: 14,
        color: colors.subtext,
    },
    formField: {
        gap: spacing.xs,
    },
    fieldLabel: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.text,
    },
    timeButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.bg,
        padding: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
    },
    timeButtonText: {
        flex: 1,
        fontSize: 16,
        color: colors.text,
        marginLeft: spacing.sm,
    },
    durationContainer: {
        backgroundColor: colors.primary + '10',
        padding: spacing.md,
        borderRadius: radius.md,
    },
    durationRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
    },
    durationLabel: {
        fontSize: 15,
        fontWeight: '600',
        color: colors.text,
    },
    durationValue: {
        fontSize: 18,
        fontWeight: '700',
        color: colors.primary,
    },
    textArea: {
        backgroundColor: colors.bg,
        padding: spacing.md,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.border,
        fontSize: 15,
        color: colors.text,
        minHeight: 100,
    },
    expenseItem: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: spacing.sm,
    },
    expenseInputs: {
        flex: 1,
        flexDirection: 'row',
        gap: spacing.sm,
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
    removeButton: {
        padding: spacing.sm,
    },
    addExpenseButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        padding: spacing.md,
        backgroundColor: colors.bg,
        borderRadius: radius.md,
        borderWidth: 1,
        borderColor: colors.primary,
        borderStyle: 'dashed',
        gap: spacing.sm,
    },
    addExpenseText: {
        fontSize: 15,
        fontWeight: '600',
        color: colors.primary,
    },
    totalExpenses: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        borderTopWidth: 1,
        borderTopColor: colors.border,
        paddingTop: spacing.sm,
        marginTop: spacing.sm,
    },
    totalExpensesLabel: {
        fontSize: 15,
        fontWeight: '600',
        color: colors.text,
    },
    totalExpensesValue: {
        fontSize: 16,
        fontWeight: '700',
        color: colors.primary,
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
