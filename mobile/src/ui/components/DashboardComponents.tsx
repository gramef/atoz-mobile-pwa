import { View, Text, Pressable, Image, ViewStyle, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import { useTheme, radius, spacing, typography } from '../theme';

// --- Types ---
type StatBlockProps = {
    label: string;
    value: string | number;
    color: string;
    icon: keyof typeof Ionicons.glyphMap;
};

type UpcomingJobProps = {
    clientName: string;
    languagePair: string;
    date: string;
    time: string;
    duration: string;
    location: string;
    onView: () => void;
    onComplete: () => void;
};

// --- Components ---

export function HomeHeader({ name, imageUrl }: { name: string; imageUrl?: string }) {
    const { colors } = useTheme();

    return (
        <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: spacing.lg }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: spacing.md }}>
                <View style={{ width: 48, height: 48, borderRadius: 24, backgroundColor: colors.blueLight, overflow: 'hidden' }}>
                    {imageUrl ? (
                        <Image source={{ uri: imageUrl }} style={{ width: '100%', height: '100%' }} />
                    ) : (
                        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center' }}>
                            <Ionicons name="person" size={24} color={colors.primary} />
                        </View>
                    )}
                </View>
                <View>
                    <Text style={{ fontSize: 13, color: colors.subtext }}>Good day,</Text>
                    <Text style={{ fontSize: 18, fontWeight: '700', color: colors.text }}>{name}</Text>
                </View>
            </View>
            <View style={{ flexDirection: 'row', gap: spacing.sm }}>
                <Pressable style={{
                    width: 40,
                    height: 40,
                    borderRadius: 20,
                    borderWidth: 1,
                    borderColor: colors.border,
                    alignItems: 'center',
                    justifyContent: 'center',
                    backgroundColor: colors.surface
                }}>
                    <Ionicons name="search-outline" size={20} color={colors.text} />
                </Pressable>
                <Pressable style={{
                    width: 40,
                    height: 40,
                    borderRadius: 20,
                    borderWidth: 1,
                    borderColor: colors.border,
                    alignItems: 'center',
                    justifyContent: 'center',
                    backgroundColor: colors.surface
                }}>
                    <Ionicons name="notifications-outline" size={20} color={colors.text} />
                </Pressable>
            </View>
        </View>
    );
}

export function EarningsCard({ amount }: { amount: string }) {
    const { colors } = useTheme();

    return (
        <View style={{
            backgroundColor: colors.primary,
            borderRadius: radius.lg,
            padding: spacing.lg,
            marginBottom: spacing.lg,
            shadowColor: colors.primary,
            shadowOffset: { width: 0, height: 4 },
            shadowOpacity: 0.2,
            shadowRadius: 8,
            elevation: 4
        }}>
            <Text style={{ color: 'rgba(255,255,255,0.8)', fontSize: 13, marginBottom: 4 }}>Total Earnings</Text>
            <Text style={{ color: colors.onPrimary, fontSize: 32, fontWeight: '700' }}>{amount}</Text>
            <Text style={{ color: 'rgba(255,255,255,0.6)', fontSize: 11, marginTop: 4 }}>Last updated 5 days ago</Text>
        </View>
    );
}

// --- New Components for Horizontal Scroll ---

export function UpcomingCarousel({ jobs }: { jobs: UpcomingJobProps[] }) {
    const { colors } = useTheme();

    if (!jobs || jobs.length === 0) {
        return (
            <View style={{
                backgroundColor: colors.surface,
                borderRadius: radius.lg,
                padding: spacing.lg,
                marginBottom: spacing.lg,
                borderWidth: 1,
                borderColor: colors.border
            }}>
                <Text style={{ ...typography.h1, color: colors.text }}>Upcoming Schedule</Text>
                <View style={{ padding: spacing.lg, alignItems: 'center' }}>
                    <Text style={{ ...typography.hint, color: colors.subtext }}>No upcoming jobs scheduled.</Text>
                </View>
            </View>
        );
    }

    return (
        <View style={{ marginBottom: spacing.lg }}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md }}>
                <Text style={{ ...typography.h1, color: colors.text }}>Upcoming Schedule</Text>
                <Text style={{ fontSize: 13, color: colors.subtext }}>See all</Text>
            </View>

            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: spacing.md }}>
                {jobs.map((job, idx) => (
                    <View key={idx} style={{
                        width: 300,
                        backgroundColor: colors.surface,
                        borderRadius: radius.lg,
                        padding: spacing.md,
                        borderWidth: 1,
                        borderColor: colors.border
                    }}>
                        <View style={{ backgroundColor: colors.bg, borderRadius: radius.md, padding: spacing.md }}>
                            <InfoRow label="Client Name" value={job.clientName} colors={colors} />
                            <InfoRow label="Language Pair" value={job.languagePair} colors={colors} />
                            <InfoRow label="Date & Time" value={`${job.date}, ${job.time}`} colors={colors} />
                            <InfoRow label="Duration" value={job.duration} colors={colors} />
                            <InfoRow label="Location" value={job.location} colors={colors} />

                            <View style={{ flexDirection: 'row', gap: spacing.md, marginTop: spacing.md }}>
                                <Pressable onPress={job.onComplete} style={{ flex: 1, backgroundColor: colors.primary, padding: 10, borderRadius: 8, alignItems: 'center' }}>
                                    <Text style={{ color: 'white', fontWeight: '600' }}>Complete</Text>
                                </Pressable>
                                <Pressable onPress={job.onView} style={{ flex: 1, backgroundColor: colors.accent, padding: 10, borderRadius: 8, alignItems: 'center' }}>
                                    <Text style={{ color: 'white', fontWeight: '600' }}>View</Text>
                                </Pressable>
                            </View>
                        </View>
                    </View>
                ))}
            </ScrollView>
        </View>
    );
}

export function HotJobsList({ jobs }: { jobs: any[] }) {
    const { colors } = useTheme();

    if (!jobs || jobs.length === 0) return null;

    return (
        <View style={{ marginBottom: spacing.lg }}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md }}>
                <Text style={{ ...typography.h1, color: colors.text }}>Hot jobs</Text>
                <Text style={{ fontSize: 13, color: colors.subtext }}>See all</Text>
            </View>
            <Text style={{ fontSize: 13, color: colors.subtext, marginBottom: spacing.sm }}>High-demand sessions and urgent bookings near you.</Text>

            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: spacing.md }}>
                {jobs.map((job, i) => (
                    <View key={i} style={{
                        width: 280,
                        backgroundColor: colors.surface,
                        borderRadius: radius.lg,
                        padding: spacing.md,
                        borderWidth: 1,
                        borderColor: colors.border
                    }}>
                        <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginBottom: spacing.sm }}>
                            <Text style={{ fontSize: 16, fontWeight: '700', color: colors.text, flex: 1 }} numberOfLines={1}>{job.title}</Text>
                        </View>

                        <View style={{ backgroundColor: colors.bg, borderRadius: radius.md, padding: spacing.md }}>
                            <InfoRow label="Language Pair" value={job.languagePair} colors={colors} />
                            <InfoRow label="Location" value={job.location} colors={colors} />
                            <InfoRow label="Time" value={job.time} colors={colors} />
                            <InfoRow label="Duration" value={job.duration} colors={colors} />

                            <View style={{ marginTop: spacing.sm, alignSelf: 'flex-start', backgroundColor: colors.danger, borderRadius: 12, paddingHorizontal: 8, paddingVertical: 2 }}>
                                <Text style={{ color: 'white', fontSize: 10, fontWeight: '700' }}>Urgent</Text>
                            </View>
                        </View>

                        <View style={{ flexDirection: 'row', gap: spacing.md, marginTop: spacing.md }}>
                            <Pressable
                                onPress={() => router.push(`/(tabs)/jobs/${job.id}`)}
                                style={{ flex: 1, backgroundColor: colors.primary, padding: 10, borderRadius: 8, alignItems: 'center' }}
                            >
                                <Text style={{ color: 'white', fontWeight: '600' }}>Accept</Text>
                            </Pressable>
                            <Pressable
                                onPress={() => router.push(`/(tabs)/jobs/${job.id}`)}
                                style={{ flex: 1, backgroundColor: colors.accent, padding: 10, borderRadius: 8, alignItems: 'center' }}
                            >
                                <Text style={{ color: 'white', fontWeight: '600' }}>View Details</Text>
                            </Pressable>
                        </View>
                    </View>
                ))}
            </ScrollView>
        </View>
    );
}

export function StatsRow({ stats }: { stats: { total: number; uninvoiced: number; successRate: string } }) {
    const { colors } = useTheme();

    return (
        <View style={{ flexDirection: 'row', gap: spacing.md, marginBottom: spacing.lg }}>
            <StatBox icon="briefcase" label="Total Jobs this Month" value={stats.total} color={colors.primary} />
            <StatBox icon="document-text" label="No. of Un-invoiced Jobs" value={stats.uninvoiced} color={colors.accent} />
            <StatBox icon="bar-chart" label="Success Rate" value={stats.successRate} color={colors.secondary} />
        </View>
    );
}

export function RecentActivityList({ activities }: { activities: any[] }) {
    const { colors } = useTheme();

    return (
        <View style={{ marginBottom: spacing.lg }}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md }}>
                <Text style={{ ...typography.h1, color: colors.text }}>Recent Activities</Text>
                <Text style={{ fontSize: 13, color: colors.subtext }}>View all</Text>
            </View>
            <View style={{ gap: spacing.md }}>
                {activities.map((act, i) => (
                    <View key={i} style={{
                        backgroundColor: colors.surface,
                        borderRadius: radius.md,
                        padding: spacing.md,
                        flexDirection: 'row',
                        alignItems: 'center',
                        gap: spacing.md,
                        borderWidth: 1,
                        borderColor: colors.border
                    }}>
                        <View style={{ width: 40, height: 40, borderRadius: 20, backgroundColor: act.iconColor || colors.greenLight, alignItems: 'center', justifyContent: 'center' }}>
                            <Ionicons name={act.icon || "checkmark"} size={20} color={act.iconColorText || colors.secondary} />
                        </View>
                        <Text style={{ fontSize: 14, color: colors.text, flex: 1 }}>{act.text}</Text>
                    </View>
                ))}
            </View>
        </View>
    )
}

// --- Helpers ---

function InfoRow({ label, value, colors }: { label: string; value: string; colors: any }) {
    return (
        <View style={{ flexDirection: 'row', marginBottom: 6 }}>
            <Text style={{ width: 100, fontSize: 12, color: colors.subtext }}>{label}:</Text>
            <Text style={{ flex: 1, fontSize: 12, fontWeight: '500', color: colors.text }}>{value}</Text>
        </View>
    );
}

function StatBox({ icon, label, value, color }: { icon: any; label: string; value: string | number; color: string }) {
    return (
        <View style={{ flex: 1, backgroundColor: color, borderRadius: radius.md, padding: spacing.md, alignItems: 'center', justifyContent: 'center', minHeight: 100 }}>
            <Ionicons name={icon} size={24} color="white" style={{ marginBottom: 8 }} />
            <Text style={{ color: 'white', fontSize: 10, textAlign: 'center', marginBottom: 4 }}>{label}</Text>
            <Text style={{ color: 'white', fontSize: 18, fontWeight: '700' }}>{value}</Text>
        </View>
    )
}
