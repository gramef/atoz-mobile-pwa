import { View, Text, StyleSheet, FlatList, Pressable, RefreshControl, ActivityIndicator } from 'react-native';
import { useState, useEffect } from 'react';
import { router } from 'expo-router';
import { useTheme, spacing, radius } from '../../../src/ui/theme';
import { getInterpreterJobs, getTranslatorJobs } from '../../../src/api/client';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '../../../src/state/auth';

type TabType = 'all' | 'applied' | 'completed';

interface Job {
  id: number;
  status: number | string;
  status_name?: string;
  appointment_date?: string;
  target_date?: string;
  start_time?: string;
  duration_hours?: number;
  duration_minutes?: number;
  to_language?: { name: string };
  from_language?: { name: string };
  department?: string;
  address_line_1?: string;
  county?: string;
  postcode?: string;
  client_reference?: string;
  type: 'interpreter' | 'translator';
}

export default function JobsListScreen() {
  const { colors } = useTheme();
  const user = useAuthStore((s) => s.user);
  const [activeTab, setActiveTab] = useState<TabType>('all');
  const [jobs, setJobs] = useState<Job[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  // Determine user role
  const roles = (user?.roles || []).map((r: string) => r.toLowerCase());
  const isInterpreter = roles.some((r: string) => r.includes('interpreter'));
  const isTranslator = roles.some((r: string) => r.includes('translator') || r.includes('transcription'));

  useEffect(() => {
    loadJobs();
  }, [activeTab]);

  const loadJobs = async () => {
    try {
      setLoading(true);
      const allJobs: Job[] = [];

      if (isInterpreter) {
        const interpreterResponse = await getInterpreterJobs({ per_page: 50 });
        const interpreterJobs = (interpreterResponse.data.data || []).map((job: any) => ({
          ...job,
          type: 'interpreter' as const,
        }));
        allJobs.push(...interpreterJobs);
      }

      if (isTranslator) {
        const translatorResponse = await getTranslatorJobs({ per_page: 50 });
        const translatorJobs = (translatorResponse.data.data || []).map((job: any) => ({
          ...job,
          type: 'translator' as const,
        }));
        allJobs.push(...translatorJobs);
      }

      const filteredJobs = filterJobsByTab(allJobs, activeTab);
      setJobs(filteredJobs);
    } catch (error) {
      console.error('Error loading jobs:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const filterJobsByTab = (allJobs: Job[], tab: TabType): Job[] => {
    switch (tab) {
      case 'applied':
        return allJobs.filter(job => {
          const status = typeof job.status === 'number' ? job.status : parseInt(String(job.status));
          return status === 1;
        });
      case 'completed':
        return allJobs.filter(job => {
          const status = typeof job.status === 'number' ? job.status : parseInt(String(job.status));
          return [2, 4, 6].includes(status);
        });
      case 'all':
      default:
        return allJobs.filter(job => {
          const status = typeof job.status === 'number' ? job.status : parseInt(String(job.status));
          return [0, 5].includes(status) || isNaN(status);
        });
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    loadJobs();
  };

  const handleJobPress = (job: Job) => {
    router.push(`/jobs/${job.type}-${job.id}`);
  };

  const getStatusLabel = (status: number | string): string => {
    const statusNum = typeof status === 'number' ? status : parseInt(String(status));
    switch (statusNum) {
      case 0: return 'Pending';
      case 1: return 'Assigned';
      case 2: return 'Cancelled';
      case 3: return 'Rejected';
      case 4: return 'Completed';
      case 5: return 'Quoted';
      case 6: return 'DNA';
      default: return 'Available';
    }
  };

  const getStatusColor = (status: number | string): string => {
    const statusNum = typeof status === 'number' ? status : parseInt(String(status));
    switch (statusNum) {
      case 0: return colors.primary + '30';
      case 1: return colors.primary;
      case 2: return colors.subtext + '50';
      case 3: return colors.danger + '30';
      case 4: return '#22c55e';
      case 5: return '#a855f7';
      case 6: return colors.danger;
      default: return colors.primary + '30';
    }
  };

  const getStatusTextColor = (status: number | string): string => {
    const statusNum = typeof status === 'number' ? status : parseInt(String(status));
    return [1, 4, 6].includes(statusNum) ? '#fff' : colors.text;
  };

  const formatDate = (dateStr?: string): string => {
    if (!dateStr) return 'TBD';
    try {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      });
    } catch {
      return dateStr;
    }
  };

  const styles = createStyles(colors);

  const renderJobCard = ({ item }: { item: Job }) => (
    <Pressable style={styles.jobCard} onPress={() => handleJobPress(item)}>
      <View style={styles.jobHeader}>
        <Text style={styles.jobTitle}>
          {item.type === 'interpreter' ? '🎤 Interpretation' : '📄 Translation'}
        </Text>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
          <Text style={[styles.statusText, { color: getStatusTextColor(item.status) }]}>
            {item.status_name || getStatusLabel(item.status)}
          </Text>
        </View>
      </View>

      <View style={styles.jobDetails}>
        <View style={styles.detailRow}>
          <Ionicons name="language-outline" size={16} color={colors.subtext} />
          <Text style={styles.detailText}>
            {item.to_language?.name || 'N/A'}
            {item.from_language && ` ← ${item.from_language.name}`}
          </Text>
        </View>

        <View style={styles.detailRow}>
          <Ionicons name="calendar-outline" size={16} color={colors.subtext} />
          <Text style={styles.detailText}>
            {formatDate(item.appointment_date || item.target_date)}
            {item.start_time && ` • ${item.start_time}`}
          </Text>
        </View>

        {item.address_line_1 && (
          <View style={styles.detailRow}>
            <Ionicons name="location-outline" size={16} color={colors.subtext} />
            <Text style={styles.detailText} numberOfLines={1}>
              {item.address_line_1}, {item.postcode}
            </Text>
          </View>
        )}

        {item.client_reference && (
          <View style={styles.detailRow}>
            <Ionicons name="document-text-outline" size={16} color={colors.subtext} />
            <Text style={styles.detailText}>Ref: {item.client_reference}</Text>
          </View>
        )}
      </View>

      <View style={styles.jobFooter}>
        <Text style={styles.viewDetails}>View Details →</Text>
      </View>
    </Pressable>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Jobs</Text>
      </View>

      <View style={styles.tabs}>
        <Pressable
          style={[styles.tab, activeTab === 'all' && styles.tabActive]}
          onPress={() => setActiveTab('all')}
        >
          <Text style={[styles.tabText, activeTab === 'all' && styles.tabTextActive]}>
            Available
          </Text>
        </Pressable>
        <Pressable
          style={[styles.tab, activeTab === 'applied' && styles.tabActive]}
          onPress={() => setActiveTab('applied')}
        >
          <Text style={[styles.tabText, activeTab === 'applied' && styles.tabTextActive]}>
            Active
          </Text>
        </Pressable>
        <Pressable
          style={[styles.tab, activeTab === 'completed' && styles.tabActive]}
          onPress={() => setActiveTab('completed')}
        >
          <Text style={[styles.tabText, activeTab === 'completed' && styles.tabTextActive]}>
            Completed
          </Text>
        </Pressable>
      </View>

      {loading && !refreshing ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      ) : (
        <FlatList
          data={jobs}
          renderItem={renderJobCard}
          keyExtractor={(item) => `${item.type}-${item.id}`}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={handleRefresh}
              tintColor={colors.primary}
            />
          }
          ListEmptyComponent={() => (
            <View style={styles.emptyContainer}>
              <Ionicons name="briefcase-outline" size={64} color={colors.border} />
              <Text style={styles.emptyTitle}>No jobs found</Text>
              <Text style={styles.emptySubtitle}>
                {activeTab === 'all'
                  ? 'There are no jobs available at the moment'
                  : `You have no ${activeTab} jobs`}
              </Text>
            </View>
          )}
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
  tabs: {
    flexDirection: 'row',
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    backgroundColor: colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    gap: spacing.sm,
  },
  tab: {
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
    borderRadius: radius.full,
    backgroundColor: colors.bg,
  },
  tabActive: {
    backgroundColor: colors.primary,
  },
  tabText: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.subtext,
  },
  tabTextActive: {
    color: '#fff',
  },
  listContent: {
    padding: spacing.lg,
    gap: spacing.md,
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  jobCard: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.md,
    borderWidth: 1,
    borderColor: colors.border,
  },
  jobHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.md,
  },
  jobTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: colors.text,
  },
  statusBadge: {
    paddingHorizontal: spacing.sm,
    paddingVertical: 4,
    borderRadius: radius.sm,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
  },
  jobDetails: {
    gap: spacing.sm,
    marginBottom: spacing.md,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
  },
  detailText: {
    fontSize: 14,
    color: colors.subtext,
    flex: 1,
  },
  jobFooter: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    paddingTop: spacing.sm,
    borderTopWidth: 1,
    borderTopColor: colors.border,
  },
  viewDetails: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.primary,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: spacing.xl * 2,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: colors.text,
    marginTop: spacing.lg,
  },
  emptySubtitle: {
    fontSize: 14,
    color: colors.subtext,
    marginTop: spacing.sm,
    textAlign: 'center',
  },
});
