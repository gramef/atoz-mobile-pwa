import React, { useEffect, useRef } from 'react';
import { router } from 'expo-router';
import * as Notifications from 'expo-notifications';
import {
    registerForPushNotifications,
    addNotificationReceivedListener,
    addNotificationResponseListener,
    getLastNotificationResponse,
    clearBadge,
} from '../services/notifications';
import { useAuthStore } from '../state/auth';

interface NotificationProviderProps {
    children: React.ReactNode;
}

export function NotificationProvider({ children }: NotificationProviderProps) {
    const token = useAuthStore((s: any) => s.token);
    const notificationListener = useRef<Notifications.Subscription>();
    const responseListener = useRef<Notifications.Subscription>();

    useEffect(() => {
        // Only set up notifications if user is logged in
        if (!token) return;

        // Register for push notifications
        registerForPushNotifications().then((pushToken) => {
            if (pushToken) {
                console.log('Push token:', pushToken);
                // TODO: Send push token to backend for server-side notifications
            }
        });

        // Handle notifications received while app is foregrounded
        const notifListener = addNotificationReceivedListener((notification) => {
            console.log('Notification received:', notification);
            // Handle in-app notification display if needed
        });
        if (notifListener) {
            notificationListener.current = notifListener;
        }

        // Handle notification taps (user interaction)
        const respListener = addNotificationResponseListener((response) => {
            handleNotificationResponse(response);
        });
        if (respListener) {
            responseListener.current = respListener;
        }

        // Check if app was opened from a notification
        getLastNotificationResponse().then((response) => {
            if (response) {
                handleNotificationResponse(response);
            }
        });

        // Clear badge when app is opened
        clearBadge();

        return () => {
            if (notificationListener.current) {
                Notifications.removeNotificationSubscription(notificationListener.current);
            }
            if (responseListener.current) {
                Notifications.removeNotificationSubscription(responseListener.current);
            }
        };
    }, [token]);

    /**
     * Handle notification tap - navigate to relevant screen
     */
    function handleNotificationResponse(response: Notifications.NotificationResponse) {
        const data = response.notification.request.content.data;

        if (!data?.type) return;

        switch (data.type) {
            case 'new_job':
            case 'job_update':
                if (data.jobId) {
                    router.push(`/(tabs)/jobs/${data.jobId}`);
                } else {
                    router.push('/(tabs)/jobs');
                }
                break;

            case 'message':
                if (data.conversationId) {
                    router.push(`/chat/${data.conversationId}`);
                } else {
                    router.push('/(tabs)/messages');
                }
                break;

            case 'payment':
                router.push('/finances/agent-invoices');
                break;

            case 'reminder':
                if (data.jobId) {
                    router.push(`/(tabs)/jobs/${data.jobId}`);
                }
                break;

            default:
                // Navigate to home for general notifications
                router.push('/(tabs)');
        }
    }

    return <>{children}</>;
}
