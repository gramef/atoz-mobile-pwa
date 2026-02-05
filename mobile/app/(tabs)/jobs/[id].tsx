import { useEffect, useState } from 'react';
import { View, Text, ActivityIndicator, ScrollView, Alert, Platform, StyleSheet } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import * as Location from 'expo-location';
import * as ImagePicker from 'expo-image-picker';
import {
  getInterpreterJob,
  getTranslatorJob,
  updateInterpreterJob,
  uploadDocument,
  acceptInterpreterJob,
  declineInterpreterJob,
  acceptTranslatorJob,
  declineTranslatorJob,
  completeInterpreterJob,
  dnaInterpreterJob,
  returnInterpreterJob
} from '../../../src/api/client';
import Screen from '../../../src/ui/components/Screen';
import Header from '../../../src/ui/components/Header';
import MapCard from '../../../src/ui/components/MapCard';
import DocumentsList from '../../../src/ui/components/DocumentsList';
import Button from '../../../src/ui/components/Button';
import { useTheme, spacing, typography, radius } from '../../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';

type Doc = { id: number; type?: string; name?: string };

// Job status enum matching backend config/enums.php
const JOB_STATUS = {
  PENDING: 0,
  ASSIGNED: 1,
  CANCELLED: 2,
  REJECTED: 3,
  COMPLETED: 4,
  QUOTED: 5,
  DNA: 6,
} as const;

type InterpreterDetail = {
  id: number;
  language?: string;
  appointment_date?: string;
  start_time?: string | null;
  duration_hours?: number | null;
  duration_minutes?: number | null;
  status?: number;
  status_name?: string;
  client_name?: string;
  agent_name?: string | null;
  department?: string | null;
  address?: { line_1?: string; line_2?: string; county?: string; postcode?: string };
  coordinates?: { lat?: number; lng?: number };
  documents?: Doc[];
  // Backend-provided action flags
  can_complete?: boolean;
  can_dna?: boolean;
  can_return?: boolean;
  can_cancel?: boolean;
  dna?: boolean;
  retrn?: boolean;
  timesheet?: { id?: number; signature_method?: string; timesheet_status?: string };
};

type TranslatorDetail = {
  id: number;
  from_language?: string;
  to_language?: string;
  target_date?: string;
  word_count?: number | null;
  status?: number;
  status_name?: string;
  client_name?: string;
  agent_name?: string | null;
  notes?: string | null;
  documents?: Doc[];
};

export default function JobDetail() {
  const { colors } = useTheme();
  const params = useLocalSearchParams();
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<InterpreterDetail | TranslatorDetail | null>(null);
  const [actionLoading, setActionLoading] = useState(false);
  const [jobType, setJobType] = useState<'interpreter' | 'translator'>('interpreter');

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const key = String(params.id);
        const [type, rawId] = key.split('-');
        const id = Number(rawId);
        setJobType(type === 'interpreter' ? 'interpreter' : 'translator');
        if (type === 'interpreter') {
          const res = await getInterpreterJob(id);
          setData((res as any)?.data?.data ?? (res as any)?.data ?? null);
        } else {
          const res = await getTranslatorJob(id);
          setData((res as any)?.data?.data ?? (res as any)?.data ?? null);
        }
      } catch (e: any) {
        console.error('Failed to load job:', e);
        Alert.alert('Error', 'Failed to load job details');
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [params.id]);

  // Reload job data
  async function reloadJob() {
    if (!data) return;
    try {
      if (jobType === 'interpreter') {
        const res = await getInterpreterJob(data.id);
        setData((res as any)?.data?.data ?? (res as any)?.data ?? null);
      } else {
        const res = await getTranslatorJob(data.id);
        setData((res as any)?.data?.data ?? (res as any)?.data ?? null);
      }
    } catch (e) {
      console.error('Failed to reload job:', e);
    }
  }

  // Accept job
  async function handleAccept() {
    if (!data) return;
    setActionLoading(true);
    try {
      if (jobType === 'interpreter') {
        await acceptInterpreterJob(data.id);
      } else {
        await acceptTranslatorJob(data.id);
      }
      Alert.alert('Success', 'Job accepted successfully!');
      await reloadJob();
    } catch (e: any) {
      const message = e.response?.data?.message || e.message || 'Failed to accept job';
      Alert.alert('Error', message);
    } finally {
      setActionLoading(false);
    }
  }

  // Decline job
  async function handleDecline() {
    if (!data) return;
    Alert.alert(
      'Decline Job',
      'Are you sure you want to decline this job?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Decline',
          style: 'destructive',
          onPress: async () => {
            setActionLoading(true);
            try {
              if (jobType === 'interpreter') {
                await declineInterpreterJob(data.id, 'Declined via mobile app');
              } else {
                await declineTranslatorJob(data.id, 'Declined via mobile app');
              }
              Alert.alert('Success', 'Job declined successfully');
              router.back();
            } catch (e: any) {
              const message = e.response?.data?.message || e.message || 'Failed to decline job';
              Alert.alert('Error', message);
            } finally {
              setActionLoading(false);
            }
          },
        },
      ]
    );
  }

  // Check in / Check out with location
  async function handleAction(action: 'check-in' | 'check-out') {
    if (!data) return;
    setActionLoading(true);
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert('Permission denied', 'Location permission is required for this action.');
        setActionLoading(false);
        return;
      }
      const location = await Location.getCurrentPositionAsync({});
      await updateInterpreterJob(data.id, {
        action,
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
      });
      Alert.alert('Success', action === 'check-in' ? 'Checked in successfully' : 'Checked out successfully');
      await reloadJob();
    } catch (e: any) {
      Alert.alert('Error', e.message || 'Action failed');
    } finally {
      setActionLoading(false);
    }
  }

  // Upload timesheet document
  async function handleUpload() {
    if (!data) return;
    try {
      const result = await ImagePicker.launchCameraAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 0.7,
      });
      if (result.canceled) return;

      setActionLoading(true);
      const asset = result.assets[0];
      const formData = new FormData();
      formData.append('file', {
        uri: asset.uri,
        name: asset.fileName || 'timesheet.jpg',
        type: asset.mimeType || 'image/jpeg',
      } as any);

      const upRes = await uploadDocument(formData);
      await updateInterpreterJob(data.id, {
        documents: [{
          name: upRes.data.file_name,
          url: upRes.data.file_path,
          type: 12, // signed_timesheet
        }]
      });
      Alert.alert('Success', 'Signed timesheet uploaded');
      await reloadJob();
    } catch (e: any) {
      Alert.alert('Error', 'Upload failed');
    } finally {
      setActionLoading(false);
    }
  }

  // Complete job
  async function handleComplete() {
    if (!data || jobType !== 'interpreter') return;
    Alert.alert(
      'Complete Job',
      'Mark this job as completed?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Complete',
          onPress: async () => {
            setActionLoading(true);
            try {
              await completeInterpreterJob(data.id);
              Alert.alert('Success', 'Job marked as completed!');
              await reloadJob();
            } catch (e: any) {
              const message = e.response?.data?.message || e.message || 'Failed to complete job';
              Alert.alert('Error', message);
            } finally {
              setActionLoading(false);
            }
          },
        },
      ]
    );
  }

  // Mark as DNA
  async function handleDNA() {
    if (!data || jobType !== 'interpreter') return;
    Alert.alert(
      'Did Not Attend',
      'Mark this job as Did Not Attend? This records that the client did not show up.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Confirm DNA',
          style: 'destructive',
          onPress: async () => {
            setActionLoading(true);
            try {
              await dnaInterpreterJob(data.id);
              Alert.alert('Recorded', 'Job marked as Did Not Attend');
              await reloadJob();
            } catch (e: any) {
              const message = e.response?.data?.message || e.message || 'Failed to mark DNA';
              Alert.alert('Error', message);
            } finally {
              setActionLoading(false);
            }
          },
        },
      ]
    );
  }

  // Return job to pool
  async function handleReturn() {
    if (!data || jobType !== 'interpreter') return;
    Alert.alert(
      'Return Job',
      'Return this job? It will be unassigned and become available for others.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Return Job',
          style: 'destructive',
          onPress: async () => {
            setActionLoading(true);
            try {
              await returnInterpreterJob(data.id);
              Alert.alert('Success', 'Job returned successfully');
              router.back();
            } catch (e: any) {
              const message = e.response?.data?.message || e.message || 'Failed to return job';
              Alert.alert('Error', message);
            } finally {
              setActionLoading(false);
            }
          },
        },
      ]
    );
  }

  const styles = createStyles(colors);

  if (loading) {
    return (
      <Screen>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      </Screen>
    );
  }

  if (!data) {
    return (
      <Screen>
        <View style={styles.loadingContainer}>
          <Text style={[typography.body, { color: colors.text }]}>No data</Text>
        </View>
      </Screen>
    );
  }

  const raw: any = data;
  const isInterpreter =
    typeof raw?.language === 'string' ||
    typeof raw?.appointment_date === 'string' ||
    typeof raw?.start_time === 'string' ||
    !!raw?.to_language_id;

  const status = typeof raw?.status === 'number' ? raw.status : parseInt(String(raw?.status ?? -1));
  const statusName = raw?.status_name || raw?.statusName || '';

  const clientName =
    raw?.client_name ||
    raw?.client?.user?.full_name ||
    raw?.client?.user?.fullName ||
    raw?.client?.user?.name ||
    'N/A';

  const agentName =
    raw?.agent_name ||
    raw?.agent?.user?.full_name ||
    raw?.agent?.user?.fullName ||
    raw?.agent?.user?.name ||
    null;

  const interpreterLanguage =
    raw?.language ||
    raw?.to_language?.name ||
    raw?.toLanguage?.name ||
    'N/A';

  const translatorFrom =
    (typeof raw?.from_language === 'string' ? raw.from_language : raw?.from_language?.name) ||
    raw?.fromLanguage?.name ||
    'N/A';

  const translatorTo =
    (typeof raw?.to_language === 'string' ? raw.to_language : raw?.to_language?.name) ||
    raw?.toLanguage?.name ||
    'N/A';

  const coordsLat = raw?.coordinates?.lat ?? raw?.latitude ?? null;
  const coordsLng = raw?.coordinates?.lng ?? raw?.longitude ?? null;

  const documents: Doc[] = Array.isArray(raw?.documents)
    ? raw.documents
        .map((d: any) => ({
          id: Number(d?.id),
          type: d?.type,
          name: d?.name || d?.file_name || d?.file_reference,
        }))
        .filter((d: any) => Number.isFinite(d.id))
    : [];

  // Determine action availability based on status
  const canAccept = status === JOB_STATUS.PENDING || status === JOB_STATUS.QUOTED;
  const isAssigned = status === JOB_STATUS.ASSIGNED || status === JOB_STATUS.QUOTED;
  const isCompleted = status === JOB_STATUS.COMPLETED;
  const isDNA = status === JOB_STATUS.DNA;
  const isCancelled = status === JOB_STATUS.CANCELLED;
  const isRejected = status === JOB_STATUS.REJECTED;

  // Use backend-provided flags when available
  const interpreterData = data as InterpreterDetail;
  const canComplete = interpreterData.can_complete ?? (isInterpreter && isAssigned);
  const canDNA = interpreterData.can_dna ?? (isInterpreter && isAssigned);
  const canReturn = interpreterData.can_return ?? (isInterpreter && isAssigned);
  const canCheckIn = isInterpreter && isAssigned && !isCompleted && !isDNA;

  // Get status badge color
  const getStatusColor = () => {
    switch (status) {
      case JOB_STATUS.PENDING: return colors.primary + '30';
      case JOB_STATUS.ASSIGNED: return colors.primary;
      case JOB_STATUS.CANCELLED: return colors.subtext + '50';
      case JOB_STATUS.REJECTED: return colors.danger + '30';
      case JOB_STATUS.COMPLETED: return '#22c55e';
      case JOB_STATUS.QUOTED: return '#a855f7';
      case JOB_STATUS.DNA: return colors.danger;
      default: return colors.primary + '30';
    }
  };

  const getStatusTextColor = () => {
    return [JOB_STATUS.ASSIGNED, JOB_STATUS.COMPLETED, JOB_STATUS.DNA].includes(status as 1 | 4 | 6) ? '#fff' : colors.text;
  };

  return (
    <Screen>
      <Header title={`Job #${data.id}`} />
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Job Header */}
        <View style={styles.headerCard}>
          {isInterpreter && (
            <>
              <Text style={styles.jobTitle}>🎤 {interpreterLanguage}</Text>
              <Text style={styles.jobSubtitle}>
                {raw?.appointment_date} {raw?.start_time ? `at ${raw.start_time}` : ''}
              </Text>
            </>
          )}
          {!isInterpreter && (
            <>
              <Text style={styles.jobTitle}>📄 {translatorFrom} → {translatorTo}</Text>
              <Text style={styles.jobSubtitle}>{raw?.target_date}</Text>
            </>
          )}

          {/* Status Badge */}
          <View style={[styles.statusBadge, { backgroundColor: getStatusColor() }]}>
            <Text style={[styles.statusText, { color: getStatusTextColor() }]}>{statusName}</Text>
          </View>
        </View>

        {/* Info Cards */}
        <View style={styles.infoCard}>
          <View style={styles.infoRow}>
            <Ionicons name="person-outline" size={20} color={colors.primary} />
            <View style={styles.infoTextContainer}>
              <Text style={styles.infoLabel}>Client</Text>
                <Text style={styles.infoValue}>{clientName}</Text>
            </View>
          </View>

            {agentName && (
            <View style={styles.infoRow}>
              <Ionicons name="person-circle-outline" size={20} color={colors.primary} />
              <View style={styles.infoTextContainer}>
                <Text style={styles.infoLabel}>Assigned Agent</Text>
                  <Text style={styles.infoValue}>{agentName}</Text>
              </View>
            </View>
          )}

          {'department' in data && data.department && (
            <View style={styles.infoRow}>
              <Ionicons name="business-outline" size={20} color={colors.primary} />
              <View style={styles.infoTextContainer}>
                <Text style={styles.infoLabel}>Department</Text>
                <Text style={styles.infoValue}>{data.department}</Text>
              </View>
            </View>
          )}
        </View>

        {/* Accept/Decline Buttons for pending jobs */}
        {canAccept && (
          <View style={styles.actionRow}>
            <Button
              title={actionLoading ? 'Accepting...' : 'Accept'}
              onPress={handleAccept}
              variant="primary"
              disabled={actionLoading}
              style={{ flex: 1 }}
            />
            <Button
              title={actionLoading ? 'Declining...' : 'Decline'}
              onPress={handleDecline}
              variant="secondary"
              style={{ flex: 1, backgroundColor: colors.danger }}
              textStyle={{ color: 'white' }}
              disabled={actionLoading}
            />
          </View>
        )}

        {/* Map Card for interpreter jobs with address */}
        {isInterpreter && (interpreterData.address || raw?.address_line_1 || raw?.address_line_2 || raw?.postcode) && (
          <MapCard
            address={
              interpreterData.address || {
                line_1: raw?.address_line_1,
                line_2: raw?.address_line_2,
                county: raw?.county,
                postcode: raw?.postcode,
              }
            }
            lat={coordsLat}
            lng={coordsLng}
          />
        )}

        {/* Assigned Job Actions */}
        {isAssigned && isInterpreter && !isCompleted && !isDNA && (
          <View style={styles.actionsCard}>
            <Text style={styles.sectionTitle}>Job Actions</Text>

            {/* Check In/Out */}
            {canCheckIn && (
              <View style={styles.actionRow}>
                <Button
                  title={actionLoading ? '...' : '📍 Check In'}
                  onPress={() => handleAction('check-in')}
                  variant="secondary"
                  disabled={actionLoading}
                  style={{ flex: 1 }}
                />
                <Button
                  title={actionLoading ? '...' : '📍 Check Out'}
                  onPress={() => handleAction('check-out')}
                  variant="outline"
                  disabled={actionLoading}
                  style={{ flex: 1 }}
                />
              </View>
            )}

            {/* Complete/DNA */}
            <View style={styles.actionRow}>
              {canComplete && (
                <Button
                  title={actionLoading ? '...' : '✓ Complete'}
                  onPress={handleComplete}
                  variant="primary"
                  disabled={actionLoading}
                  style={{ flex: 1 }}
                />
              )}
              {canDNA && (
                <Button
                  title={actionLoading ? '...' : 'DNA'}
                  onPress={handleDNA}
                  variant="secondary"
                  style={{ flex: 1, backgroundColor: colors.danger }}
                  textStyle={{ color: 'white' }}
                  disabled={actionLoading}
                />
              )}
            </View>

            {/* Upload Timesheet */}
            <Button
              title={actionLoading ? 'Uploading...' : '📄 Upload Signed Timesheet'}
              onPress={handleUpload}
              variant="outline"
              disabled={actionLoading}
            />

            {/* Electronic Signature */}
            <Button
              title="✍️ Get Electronic Signature"
              onPress={() => {
                const timesheetId = (data as InterpreterDetail).timesheet?.id;
                if (!timesheetId) {
                  Alert.alert('Error', 'No timesheet found for this job. Please try again.');
                  return;
                }
                router.push(`/jobs/sign/${timesheetId}` as any);
              }}
              variant="primary"
              style={{ backgroundColor: '#22c55e' }}
              disabled={actionLoading}
            />

            {/* Return Job */}
            {canReturn && (
              <Button
                title={actionLoading ? '...' : 'Return Job to Pool'}
                onPress={handleReturn}
                variant="outline"
                disabled={actionLoading}
              />
            )}
          </View>
        )}

        {/* Completed Status */}
        {isCompleted && (
          <View style={[styles.statusCard, { backgroundColor: '#22c55e20' }]}>
            <Ionicons name="checkmark-circle" size={24} color="#22c55e" />
            <Text style={[styles.statusCardText, { color: '#22c55e' }]}>Job Completed</Text>
          </View>
        )}

        {/* DNA Status */}
        {isDNA && (
          <View style={[styles.statusCard, { backgroundColor: colors.danger + '20' }]}>
            <Ionicons name="alert-circle" size={24} color={colors.danger} />
            <Text style={[styles.statusCardText, { color: colors.danger }]}>Did Not Attend</Text>
          </View>
        )}

        {/* Cancelled Status */}
        {isCancelled && (
          <View style={[styles.statusCard, { backgroundColor: colors.subtext + '20' }]}>
            <Ionicons name="close-circle" size={24} color={colors.subtext} />
            <Text style={[styles.statusCardText, { color: colors.subtext }]}>Cancelled</Text>
          </View>
        )}

        {/* Documents */}
        {documents.length > 0 && (
          <View style={styles.documentsSection}>
            <Text style={styles.sectionTitle}>Documents</Text>
            <DocumentsList items={documents} />
          </View>
        )}
      </ScrollView>
    </Screen>
  );
}

const createStyles = (colors: any) => StyleSheet.create({
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  scrollContent: {
    padding: spacing.lg,
    gap: spacing.md,
  },
  headerCard: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.lg,
    borderWidth: 1,
    borderColor: colors.border,
  },
  jobTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: colors.text,
  },
  jobSubtitle: {
    fontSize: 14,
    color: colors.subtext,
    marginTop: 4,
  },
  statusBadge: {
    alignSelf: 'flex-start',
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.sm,
    borderRadius: radius.sm,
    marginTop: spacing.md,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  infoCard: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.lg,
    borderWidth: 1,
    borderColor: colors.border,
    gap: spacing.md,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.md,
  },
  infoTextContainer: {
    flex: 1,
  },
  infoLabel: {
    fontSize: 12,
    color: colors.subtext,
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.text,
  },
  actionsCard: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.lg,
    borderWidth: 1,
    borderColor: colors.border,
    gap: spacing.md,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: colors.text,
    marginBottom: spacing.sm,
  },
  actionRow: {
    flexDirection: 'row',
    gap: spacing.md,
  },
  statusCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: spacing.lg,
    borderRadius: radius.lg,
    gap: spacing.sm,
  },
  statusCardText: {
    fontSize: 16,
    fontWeight: '700',
  },
  documentsSection: {
    marginTop: spacing.md,
  },
});
