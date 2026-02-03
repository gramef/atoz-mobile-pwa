import { Redirect, router } from 'expo-router';
import { useEffect, useState } from 'react';
import { ScrollView, RefreshControl, Text, View } from 'react-native';
import { getInterpreterJobs, getTranslatorJobs } from '../../src/api/client';
import { useAuthStore } from '../../src/state/auth';
import Screen from '../../src/ui/components/Screen';
import { spacing } from '../../src/ui/theme';
import {
  HomeHeader,
  EarningsCard,
  UpcomingCarousel,
  StatsRow,
  RecentActivityList,
  HotJobsList
} from '../../src/ui/components/DashboardComponents';

export default function Index() {
  const token = useAuthStore((s: any) => s.token);
  const user = useAuthStore((s: any) => s.user);
  const clear = useAuthStore((s: any) => s.clear);

  const [loading, setLoading] = useState(false);
  const [upcomingJobs, setUpcomingJobs] = useState<any[]>([]);
  const [hotJobs, setHotJobs] = useState<any[]>([]);
  const [stats, setStats] = useState({ total: 0, uninvoiced: 0, successRate: '98%' });
  const [activities, setActivities] = useState<any[]>([]);
  const [earnings, setEarnings] = useState('$0.00');

  // Role Detection
  const roles = (user?.roles || []).map((r: string) => r.toLowerCase());
  const isClient = roles.some((r: string) => r.includes('client'));
  const isInterpreter = roles.some((r: string) => r.includes('interpreter'));
  const isTranslator = roles.some((r: string) => r.includes('translator') || r.includes('transcription'));
  // Default to linguist view if no roles or just basic user
  const isLinguist = isInterpreter || isTranslator || (!isClient && roles.length === 0);

  async function loadData() {
    if (!token) return;
    setLoading(true);
    try {

      // --- CLIENT VIEW LOGIC ---
      if (isClient) {
        // Mock Data for Client Demo
        setStats({ total: 12, uninvoiced: 3, successRate: '100%' }); // Reused stats structure for now: Total Bookings, Pending, Completion

        setUpcomingJobs([
          { clientName: 'Spanish Interpreter', languagePair: 'English ↔ Spanish', date: 'Tomorrow', time: '10:00 AM', duration: '2 hours', location: 'Office 301', onView: () => { }, onComplete: () => { } },
          { clientName: 'Document Translation', languagePair: 'French -> English', date: 'Pending', time: '-', duration: '500 words', location: 'Remote', onView: () => { }, onComplete: () => { } }
        ]);

        setActivities([
          { text: 'Quote received for "Annual Report"', icon: 'document-text', iconColor: '#E7F1FF', iconColorText: '#0D6EFD' },
          { text: 'Booking confirmed for Friday', icon: 'checkmark-circle', iconColor: '#D4EDDA', iconColorText: '#28A745' }
        ]);

        setHotJobs([]); // Clients don't see hot jobs
        setEarnings(''); // Clients don't see earnings
        setLoading(false);
        return;
      }

      // --- LINGUIST VIEW LOGIC (Existing) ---
      const promises = [];
      if (isInterpreter || !isTranslator) promises.push(getInterpreterJobs({ per_page: 20, sort: '-created_at' }));
      if (isTranslator || !isInterpreter) promises.push(getTranslatorJobs({ per_page: 20, sort: '-created_at' }));

      const results = await Promise.all(promises);

      let allJobs: any[] = [];
      results.forEach(res => {
        if (res.data?.data) allJobs = [...allJobs, ...res.data.data];
      });

      allJobs.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());

      // 1. Stats
      const total = results.reduce((acc, res) => acc + (res.data?.meta?.total || 0), 0);
      setStats(prev => ({ ...prev, total, uninvoiced: Math.floor(total * 0.2) }));

      // 2. Earnings 
      setEarnings('$25,564.56');

      // 3. Upcoming Jobs
      let upcomingList = allJobs
        .filter(j => j.status !== 'completed' && j.status !== 'cancelled' && j.status !== 'rejected')
        .slice(0, 5)
        .map(j => {
          // Determine job type: Interpreter jobs have appointment_date, Translator jobs have target_date
          const jobType = j.appointment_date ? 'interpreter' : 'translator';
          const toLang = typeof j.to_language === 'object' ? j.to_language?.name : (j.to_language || 'N/A');
          const fromLang = typeof j.from_language === 'object' ? j.from_language?.name : (j.from_language || 'English');
          return {
            clientName: j.meta?.client_name || 'Client',
            languagePair: j.language || `${fromLang} - ${toLang}`,
            date: new Date(j.appointment_date || j.target_date || j.created_at).toLocaleDateString(),
            time: j.appointment_date ? (j.start_time || new Date(j.appointment_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })) : 'N/A',
            duration: j.duration ? `${j.duration} mins` : (j.duration_hours ? `${j.duration_hours}h` : '45 mins'),
            location: j.location || j.address_line_1 || 'Online (Video Call)',
            onView: () => router.push(`/(tabs)/jobs/${jobType}-${j.id}`),
            onComplete: () => router.push(`/(tabs)/jobs/${jobType}-${j.id}`),
          };
        });

      // Demo Mode Fallback for Linguists
      if (upcomingList.length === 0) {
        upcomingList = [
          { clientName: 'Vanessa Joshua', languagePair: 'English - French', date: 'Tomorrow', time: '10:30 AM', duration: '45 mins', location: 'Online (Video Call)', onView: () => { }, onComplete: () => { } },
          { clientName: 'David Kim', languagePair: 'English - Korean', date: 'Oct 24', time: '02:00 PM', duration: '1 hour', location: 'London Court', onView: () => { }, onComplete: () => { } }
        ];
        setStats({ total: 245, uninvoiced: 56, successRate: '98%' });
      }
      setUpcomingJobs(upcomingList);

      // 4. Hot Jobs (Only for Linguists)
      let hotList = allJobs
        .filter(j => {
          const s = String(j.status).toLowerCase();
          return s === 'pending' || s === 'open' || s === '0' || s === '1';
        })
        .slice(0, 5)
        .map(j => {
          // Determine job type: Interpreter jobs have appointment_date, Translator jobs have target_date
          const jobType = j.appointment_date ? 'interpreter' : 'translator';
          const toLang = typeof j.to_language === 'object' ? j.to_language?.name : (j.to_language || 'N/A');
          const fromLang = typeof j.from_language === 'object' ? j.from_language?.name : (j.from_language || 'English');
          return {
            title: j.meta?.title || 'Mega Court Session',
            languagePair: j.language || `${fromLang} ↔ ${toLang}`,
            location: j.location || j.address_line_1 || 'Online (Video Call)',
            time: j.appointment_date ? (j.start_time || '02:00 PM') : 'N/A',
            duration: j.duration ? `${j.duration} mins` : '1 hour 30 mins',
            id: `${jobType}-${j.id}` // Include type prefix for job detail page
          };
        });

      if (hotList.length === 0) {
        hotList = [
          { title: 'Mega Court Session', languagePair: 'Spanish ↔ English', location: 'Online (Video Call)', time: '2:00 PM', duration: '1 hour 30 mins', id: 'interpreter-991' },
          { title: 'Business Conference', languagePair: 'German ↔ English', location: 'Hilton London', time: '09:00 AM', duration: '3 hours', id: 'interpreter-992' },
          { title: 'Medical Interpretation', languagePair: 'French ↔ English', location: 'City Hospital', time: '11:15 AM', duration: '45 mins', id: 'interpreter-993' }
        ];
      }
      setHotJobs(hotList);

      // 5. Recent Activities
      setActivities([
        { text: 'Your session with David Kim was completed successfully.', icon: 'checkmark-circle', iconColor: '#D4EDDA', iconColorText: '#28A745' },
        { text: 'Payment of $80 received.', icon: 'cash', iconColor: '#FFF3CD', iconColorText: '#856404' },
        { text: 'New booking request from Vanessa Joshua', icon: 'calendar', iconColor: '#E7F1FF', iconColorText: '#0D6EFD' }
      ]);

    } catch (e: any) {
      console.error('Dashboard load error', e);
      if (e?.response?.status === 401) {
        await clear();
        router.replace('/login');
      }
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadData();
  }, [token, user]);

  if (!token) return <Redirect href="/login" />;

  return (
    <Screen>
      <ScrollView
        contentContainerStyle={{ paddingBottom: spacing.xl }}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={loadData} />}
      >
        <HomeHeader name={user?.name || 'User'} />

        {/* Show Earnings only for Linguists */}
        {!isClient && <EarningsCard amount={earnings} />}

        {/* Re-use UpcomingCarousel for 'My Bookings' if Client */}
        {isClient && <Text style={{ paddingHorizontal: spacing.lg, fontSize: 18, fontWeight: '700', marginBottom: spacing.sm }}>My Bookings</Text>}
        <UpcomingCarousel jobs={upcomingJobs} />

        {/* Reuse Stats but with different labels handled in component or prop override? For now keeping same component */}
        <StatsRow stats={stats} />

        <RecentActivityList activities={activities} />

        {/* Hot Jobs only for Linguists */}
        {!isClient && <HotJobsList jobs={hotJobs} />}
      </ScrollView>
    </Screen>
  );
}
