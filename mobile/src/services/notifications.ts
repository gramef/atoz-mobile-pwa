import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const PUSH_TOKEN_KEY = 'push_token';
const NOTIFICATIONS_ENABLED_KEY = 'notifications_enabled';

// Check if we're on native platform (iOS or Android)
const isNative = Platform.OS === 'ios' || Platform.OS === 'android';

// Configure notification handling behavior (only on native)
if (isNative) {
    Notifications.setNotificationHandler({
        handleNotification: async () => ({
            shouldShowAlert: true,
            shouldPlaySound: true,
            shouldSetBadge: true,
        }),
    });
}

export interface NotificationData {
    type: 'new_job' | 'job_update' | 'message' | 'payment' | 'reminder' | 'general';
    title: string;
    body: string;
    data?: Record<string, any>;
}

/**
 * Register for push notifications and get the Expo push token
 */
export async function registerForPushNotifications(): Promise<string | null> {
    // Skip on web - push notifications require native platform
    if (!isNative) {
        console.log('Push notifications are not supported on web');
        return null;
    }

    try {
        // Check if physical device
        if (!Device.isDevice) {
            console.log('Push notifications require a physical device');
            return null;
        }

        // Check existing permissions
        const { status: existingStatus } = await Notifications.getPermissionsAsync();
        let finalStatus = existingStatus;

        // Request permissions if not already granted
        if (existingStatus !== 'granted') {
            const { status } = await Notifications.requestPermissionsAsync();
            finalStatus = status;
        }

        if (finalStatus !== 'granted') {
            console.log('Push notification permission not granted');
            return null;
        }

        // Get Expo push token
        const projectId = Constants.expoConfig?.extra?.eas?.projectId;
        const token = await Notifications.getExpoPushTokenAsync({
            projectId,
        });

        // Store token locally
        await AsyncStorage.setItem(PUSH_TOKEN_KEY, token.data);

        // Configure Android channel
        if (Platform.OS === 'android') {
            await Notifications.setNotificationChannelAsync('default', {
                name: 'Default',
                importance: Notifications.AndroidImportance.MAX,
                vibrationPattern: [0, 250, 250, 250],
                lightColor: '#0d6efd',
            });

            await Notifications.setNotificationChannelAsync('jobs', {
                name: 'Job Notifications',
                importance: Notifications.AndroidImportance.HIGH,
                sound: 'default',
            });

            await Notifications.setNotificationChannelAsync('messages', {
                name: 'Message Notifications',
                importance: Notifications.AndroidImportance.HIGH,
                sound: 'default',
            });
        }

        return token.data;
    } catch (error) {
        console.error('Error registering for push notifications:', error);
        return null;
    }
}

/**
 * Get the stored push token
 */
export async function getStoredPushToken(): Promise<string | null> {
    try {
        return await AsyncStorage.getItem(PUSH_TOKEN_KEY);
    } catch {
        return null;
    }
}

/**
 * Check if notifications are enabled
 */
export async function areNotificationsEnabled(): Promise<boolean> {
    try {
        const enabled = await AsyncStorage.getItem(NOTIFICATIONS_ENABLED_KEY);
        return enabled !== 'false'; // Default to true
    } catch {
        return true;
    }
}

/**
 * Set notifications enabled/disabled
 */
export async function setNotificationsEnabled(enabled: boolean): Promise<void> {
    await AsyncStorage.setItem(NOTIFICATIONS_ENABLED_KEY, enabled ? 'true' : 'false');
}

/**
 * Schedule a local notification
 */
export async function scheduleLocalNotification(
    title: string,
    body: string,
    data?: Record<string, any>,
    trigger?: Notifications.NotificationTriggerInput
): Promise<string> {
    if (!isNative) {
        console.log('Local notifications are not supported on web');
        return '';
    }
    return await Notifications.scheduleNotificationAsync({
        content: {
            title,
            body,
            data,
            sound: 'default',
        },
        trigger: trigger || null, // null = immediate
    });
}

/**
 * Schedule a job reminder notification
 */
export async function scheduleJobReminder(
    jobId: string,
    jobTitle: string,
    startTime: Date,
    minutesBefore: number = 30
): Promise<string | null> {
    const triggerTime = new Date(startTime.getTime() - minutesBefore * 60 * 1000);

    if (triggerTime <= new Date()) {
        return null; // Don't schedule past reminders
    }

    return await Notifications.scheduleNotificationAsync({
        content: {
            title: 'Upcoming Job Reminder',
            body: `"${jobTitle}" starts in ${minutesBefore} minutes`,
            data: { type: 'reminder', jobId },
            sound: 'default',
        },
        trigger: { date: triggerTime },
    });
}

/**
 * Cancel a scheduled notification
 */
export async function cancelNotification(notificationId: string): Promise<void> {
    if (!isNative) return;
    await Notifications.cancelScheduledNotificationAsync(notificationId);
}

/**
 * Cancel all scheduled notifications
 */
export async function cancelAllNotifications(): Promise<void> {
    if (!isNative) return;
    await Notifications.cancelAllScheduledNotificationsAsync();
}

/**
 * Get badge count
 */
export async function getBadgeCount(): Promise<number> {
    if (!isNative) return 0;
    return await Notifications.getBadgeCountAsync();
}

/**
 * Set badge count
 */
export async function setBadgeCount(count: number): Promise<void> {
    if (!isNative) return;
    await Notifications.setBadgeCountAsync(count);
}

/**
 * Clear badge
 */
export async function clearBadge(): Promise<void> {
    if (!isNative) return;
    await Notifications.setBadgeCountAsync(0);
}

/**
 * Add notification received listener
 */
export function addNotificationReceivedListener(
    listener: (notification: Notifications.Notification) => void
): Notifications.Subscription | null {
    if (!isNative) return null;
    return Notifications.addNotificationReceivedListener(listener);
}

/**
 * Add notification response listener (when user taps notification)
 */
export function addNotificationResponseListener(
    listener: (response: Notifications.NotificationResponse) => void
): Notifications.Subscription | null {
    if (!isNative) return null;
    return Notifications.addNotificationResponseReceivedListener(listener);
}

/**
 * Get last notification response (for app launched from notification)
 */
export async function getLastNotificationResponse(): Promise<Notifications.NotificationResponse | null> {
    if (!isNative) return null;
    return await Notifications.getLastNotificationResponseAsync();
}
