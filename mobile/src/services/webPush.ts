/**
 * Web Push Notification Service
 * Provides push notification support for PWA using Web Push API
 */

const WEB_PUSH_PUBLIC_KEY = process.env.EXPO_PUBLIC_VAPID_PUBLIC_KEY || '';
const API_BASE_URL = process.env.EXPO_PUBLIC_API_BASE_URL || 'https://portal.atozinterpreting.com/api';

/**
 * Check if web push is supported
 */
export function isWebPushSupported(): boolean {
    return typeof window !== 'undefined' &&
        'serviceWorker' in navigator &&
        'PushManager' in window &&
        'Notification' in window;
}

/**
 * Get current notification permission status
 */
export function getNotificationPermission(): NotificationPermission | null {
    if (typeof window === 'undefined' || !('Notification' in window)) {
        return null;
    }
    return Notification.permission;
}

/**
 * Request notification permission
 */
export async function requestNotificationPermission(): Promise<NotificationPermission> {
    if (!isWebPushSupported()) {
        throw new Error('Push notifications are not supported in this browser');
    }
    return await Notification.requestPermission();
}

/**
 * Register service worker
 */
export async function registerServiceWorker(): Promise<ServiceWorkerRegistration | null> {
    if (!('serviceWorker' in navigator)) {
        console.log('Service workers not supported');
        return null;
    }

    try {
        const registration = await navigator.serviceWorker.register('/sw.js', {
            scope: '/',
        });
        console.log('Service Worker registered:', registration.scope);
        return registration;
    } catch (error) {
        console.error('Service Worker registration failed:', error);
        return null;
    }
}

/**
 * Subscribe to push notifications
 */
export async function subscribeToPush(
    registration: ServiceWorkerRegistration
): Promise<PushSubscription | null> {
    if (!WEB_PUSH_PUBLIC_KEY) {
        console.warn('VAPID public key not configured');
        return null;
    }

    try {
        // Check if already subscribed
        const existingSubscription = await registration.pushManager.getSubscription();
        if (existingSubscription) {
            console.log('Already subscribed to push');
            return existingSubscription;
        }

        // Convert VAPID key to Uint8Array
        const applicationServerKey = urlBase64ToUint8Array(WEB_PUSH_PUBLIC_KEY);

        // Subscribe
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey,
        });

        console.log('Push subscription created:', subscription.endpoint);

        // Send subscription to backend
        await sendSubscriptionToServer(subscription);

        return subscription;
    } catch (error) {
        console.error('Push subscription failed:', error);
        return null;
    }
}

/**
 * Unsubscribe from push notifications
 */
export async function unsubscribeFromPush(): Promise<boolean> {
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (subscription) {
            await subscription.unsubscribe();
            await removeSubscriptionFromServer(subscription);
            return true;
        }
        return false;
    } catch (error) {
        console.error('Push unsubscription failed:', error);
        return false;
    }
}

/**
 * Send push subscription to backend server
 */
async function sendSubscriptionToServer(subscription: PushSubscription): Promise<void> {
    const token = localStorage.getItem('token');
    if (!token) {
        console.warn('No auth token, cannot register push subscription');
        return;
    }

    try {
        await fetch(`${API_BASE_URL}/push-subscriptions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: arrayBufferToBase64(subscription.getKey('p256dh')),
                    auth: arrayBufferToBase64(subscription.getKey('auth')),
                },
                platform: 'web',
                user_agent: navigator.userAgent,
            }),
        });
    } catch (error) {
        console.error('Failed to send subscription to server:', error);
    }
}

/**
 * Remove push subscription from backend server
 */
async function removeSubscriptionFromServer(subscription: PushSubscription): Promise<void> {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        await fetch(`${API_BASE_URL}/push-subscriptions`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
            }),
        });
    } catch (error) {
        console.error('Failed to remove subscription from server:', error);
    }
}

/**
 * Show a local web notification
 */
export function showLocalNotification(title: string, options?: NotificationOptions): void {
    if (Notification.permission === 'granted') {
        new Notification(title, {
            icon: '/assets/icon-192.png',
            badge: '/assets/icon-96.png',
            ...options,
        });
    }
}

/**
 * Convert VAPID key from base64 to Uint8Array
 */
function urlBase64ToUint8Array(base64String: string): Uint8Array {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

/**
 * Convert ArrayBuffer to base64 string
 */
function arrayBufferToBase64(buffer: ArrayBuffer | null): string {
    if (!buffer) return '';
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
}

/**
 * Initialize web push notifications
 * Call this after user logs in
 */
export async function initializeWebPush(): Promise<boolean> {
    if (!isWebPushSupported()) {
        console.log('Web Push not supported');
        return false;
    }

    const permission = await requestNotificationPermission();
    if (permission !== 'granted') {
        console.log('Notification permission not granted');
        return false;
    }

    const registration = await registerServiceWorker();
    if (!registration) {
        return false;
    }

    const subscription = await subscribeToPush(registration);
    return subscription !== null;
}
