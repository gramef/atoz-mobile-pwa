import { View, Text, StyleSheet, ScrollView, Pressable } from 'react-native';
import { router } from 'expo-router';
import { useTheme, spacing, radius } from '../../src/ui/theme';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '../../src/state/auth';

interface MenuItem {
    title: string;
    icon: string;
    route: string;
    description: string;
}

export default function MoreScreen() {
    const { colors } = useTheme();
    const user = useAuthStore((state) => state.user);
    const logout = useAuthStore((state) => state.logout);

    const handleLogout = () => {
        logout();
        router.replace('/login');
    };

    const menuItems: MenuItem[] = [
        {
            title: 'Timesheets',
            icon: 'time-outline',
            route: '/timesheets',
            description: 'Track your time  and expenses',
        },
        {
            title: 'Profile',
            icon: 'person-outline',
            route: '/profile',
            description: 'View and edit your profile',
        },
        {
            title: 'Documents',
            icon: 'folder-outline',
            route: '/documents',
            description: 'Manage your documents',
        },
        {
            title: 'Finances',
            icon: 'cash-outline',
            route: '/finances/agent-invoices',
            description: 'View invoices and payments',
        },
        {
            title: 'Settings',
            icon: 'settings-outline',
            route: '/profile/settings',
            description: 'App preferences and settings',
        },
    ];

    const styles = createStyles(colors);

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.header}>
                <Text style={styles.headerTitle}>More</Text>
            </View>

            <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
                {/* User Info Card */}
                <View style={styles.userCard}>
                    <View style={styles.userAvatar}>
                        <Ionicons name="person" size={32} color={colors.primary} />
                    </View>
                    <View style={styles.userInfo}>
                        <Text style={styles.userName}>
                            {user?.first_name} {user?.last_name}
                        </Text>
                        <Text style={styles.userEmail}>{user?.email}</Text>
                        <Text style={styles.userRole}>{user?.role?.toUpperCase()}</Text>
                    </View>
                </View>

                {/* Menu Items */}
                <View style={styles.menuSection}>
                    <Text style={styles.sectionTitle}>Menu</Text>
                    {menuItems.map((item, index) => (
                        <Pressable
                            key={index}
                            style={styles.menuItem}
                            onPress={() => router.push(item.route as any)}
                        >
                            <View style={styles.menuItemLeft}>
                                <View style={styles.menuItemIcon}>
                                    <Ionicons name={item.icon as any} size={24} color={colors.primary} />
                                </View>
                                <View style={styles.menuItemText}>
                                    <Text style={styles.menuItemTitle}>{item.title}</Text>
                                    <Text style={styles.menuItemDescription}>{item.description}</Text>
                                </View>
                            </View>
                            <Ionicons name="chevron-forward" size={20} color={colors.subtext} />
                        </Pressable>
                    ))}
                </View>

                {/* App Info */}
                <View style={styles.infoSection}>
                    <Text style={styles.sectionTitle}>About</Text>
                    <View style={styles.infoCard}>
                        <Text style={styles.infoLabel}>Version</Text>
                        <Text style={styles.infoValue}>1.0.0</Text>
                    </View>
                    <View style={styles.infoCard}>
                        <Text style={styles.infoLabel}>App Name</Text>
                        <Text style={styles.infoValue}>AtoZ Mobile</Text>
                    </View>
                </View>

                {/* Logout Button */}
                <Pressable style={styles.logoutButton} onPress={handleLogout}>
                    <Ionicons name="log-out-outline" size={20} color={colors.danger} />
                    <Text style={styles.logoutButtonText}>Log Out</Text>
                </Pressable>
            </ScrollView>
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
        borderBottomWidth: 1,
        borderBottomColor: colors.border,
    },
    headerTitle: {
        fontSize: 24,
        fontWeight: '700',
        color: colors.text,
    },
    content: {
        flex: 1,
    },
    contentContainer: {
        padding: spacing.lg,
        gap: spacing.lg,
    },
    userCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.lg,
        gap: spacing.md,
    },
    userAvatar: {
        width: 64,
        height: 64,
        borderRadius: 32,
        backgroundColor: colors.bg,
        alignItems: 'center',
        justifyContent: 'center',
    },
    userInfo: {
        flex: 1,
    },
    userName: {
        fontSize: 18,
        fontWeight: '600',
        color: colors.text,
    },
    userEmail: {
        fontSize: 14,
        color: colors.subtext,
        marginTop: 2,
    },
    userRole: {
        fontSize: 12,
        color: colors.primary,
        fontWeight: '600',
        marginTop: 4,
    },
    menuSection: {
        gap: spacing.xs,
    },
    sectionTitle: {
        fontSize: 14,
        fontWeight: '600',
        color: colors.subtext,
        marginBottom: spacing.xs,
        textTransform: 'uppercase',
        letterSpacing: 0.5,
    },
    menuItem: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.md,
        marginBottom: spacing.xs,
    },
    menuItemLeft: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
        gap: spacing.md,
    },
    menuItemIcon: {
        width: 40,
        height: 40,
        borderRadius: radius.md,
        backgroundColor: colors.bg,
        alignItems: 'center',
        justifyContent: 'center',
    },
    menuItemText: {
        flex: 1,
    },
    menuItemTitle: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.text,
    },
    menuItemDescription: {
        fontSize: 13,
        color: colors.subtext,
        marginTop: 2,
    },
    infoSection: {
        gap: spacing.xs,
    },
    infoCard: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.md,
        marginBottom: spacing.xs,
    },
    infoLabel: {
        fontSize: 14,
        color: colors.subtext,
    },
    infoValue: {
        fontSize: 14,
        fontWeight: '500',
        color: colors.text,
    },
    logoutButton: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: colors.surface,
        padding: spacing.md,
        borderRadius: radius.md,
        gap: spacing.sm,
        borderWidth: 1,
        borderColor: colors.danger,
    },
    logoutButtonText: {
        fontSize: 16,
        fontWeight: '600',
        color: colors.danger,
    },
});
