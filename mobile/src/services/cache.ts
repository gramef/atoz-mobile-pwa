import AsyncStorage from '@react-native-async-storage/async-storage';
import NetInfo from '@react-native-community/netinfo';

// Cache keys
const CACHE_PREFIX = 'cache_';
const CACHE_METADATA_KEY = 'cache_metadata';

interface CacheEntry<T> {
    data: T;
    timestamp: number;
    expiresAt: number;
}

interface CacheMetadata {
    [key: string]: {
        timestamp: number;
        expiresAt: number;
        size: number;
    };
}

// Default cache durations in milliseconds
export const CACHE_DURATION = {
    SHORT: 5 * 60 * 1000,        // 5 minutes
    MEDIUM: 30 * 60 * 1000,      // 30 minutes
    LONG: 2 * 60 * 60 * 1000,    // 2 hours
    DAY: 24 * 60 * 60 * 1000,    // 24 hours
    WEEK: 7 * 24 * 60 * 60 * 1000, // 7 days
};

/**
 * Cache data with expiration
 */
export async function cacheData<T>(
    key: string,
    data: T,
    duration: number = CACHE_DURATION.MEDIUM
): Promise<void> {
    try {
        const cacheKey = `${CACHE_PREFIX}${key}`;
        const now = Date.now();

        const entry: CacheEntry<T> = {
            data,
            timestamp: now,
            expiresAt: now + duration,
        };

        const serialized = JSON.stringify(entry);
        await AsyncStorage.setItem(cacheKey, serialized);

        // Update metadata
        await updateCacheMetadata(key, {
            timestamp: now,
            expiresAt: entry.expiresAt,
            size: serialized.length,
        });
    } catch (error) {
        console.error('Error caching data:', error);
    }
}

/**
 * Get cached data (returns null if expired or not found)
 */
export async function getCachedData<T>(key: string): Promise<T | null> {
    try {
        const cacheKey = `${CACHE_PREFIX}${key}`;
        const cached = await AsyncStorage.getItem(cacheKey);

        if (!cached) return null;

        const entry: CacheEntry<T> = JSON.parse(cached);
        const now = Date.now();

        // Check if expired
        if (now > entry.expiresAt) {
            // Clean up expired entry
            await AsyncStorage.removeItem(cacheKey);
            await removeCacheMetadata(key);
            return null;
        }

        return entry.data;
    } catch (error) {
        console.error('Error getting cached data:', error);
        return null;
    }
}

/**
 * Get cached data even if expired (for offline fallback)
 */
export async function getCachedDataForced<T>(key: string): Promise<T | null> {
    try {
        const cacheKey = `${CACHE_PREFIX}${key}`;
        const cached = await AsyncStorage.getItem(cacheKey);

        if (!cached) return null;

        const entry: CacheEntry<T> = JSON.parse(cached);
        return entry.data;
    } catch (error) {
        return null;
    }
}

/**
 * Remove cached data
 */
export async function removeCachedData(key: string): Promise<void> {
    try {
        const cacheKey = `${CACHE_PREFIX}${key}`;
        await AsyncStorage.removeItem(cacheKey);
        await removeCacheMetadata(key);
    } catch (error) {
        console.error('Error removing cached data:', error);
    }
}

/**
 * Clear all cached data
 */
export async function clearAllCache(): Promise<void> {
    try {
        const keys = await AsyncStorage.getAllKeys();
        const cacheKeys = keys.filter((k) => k.startsWith(CACHE_PREFIX));
        await AsyncStorage.multiRemove(cacheKeys);
        await AsyncStorage.removeItem(CACHE_METADATA_KEY);
    } catch (error) {
        console.error('Error clearing cache:', error);
    }
}

/**
 * Clean up expired cache entries
 */
export async function cleanExpiredCache(): Promise<void> {
    try {
        const metadata = await getCacheMetadata();
        const now = Date.now();
        const expiredKeys: string[] = [];

        for (const [key, info] of Object.entries(metadata)) {
            if (now > info.expiresAt) {
                expiredKeys.push(key);
            }
        }

        for (const key of expiredKeys) {
            await removeCachedData(key);
        }
    } catch (error) {
        console.error('Error cleaning expired cache:', error);
    }
}

/**
 * Check if device is online
 */
export async function isOnline(): Promise<boolean> {
    try {
        const state = await NetInfo.fetch();
        return state.isConnected ?? false;
    } catch {
        return false;
    }
}

/**
 * Fetch with cache - tries cache first when offline
 */
export async function fetchWithCache<T>(
    key: string,
    fetcher: () => Promise<T>,
    cacheDuration: number = CACHE_DURATION.MEDIUM
): Promise<{ data: T; fromCache: boolean }> {
    const online = await isOnline();

    if (!online) {
        // Offline - try to get cached data
        const cached = await getCachedDataForced<T>(key);
        if (cached !== null) {
            return { data: cached, fromCache: true };
        }
        throw new Error('No internet connection and no cached data available');
    }

    try {
        // Online - fetch fresh data
        const data = await fetcher();

        // Cache the fresh data
        await cacheData(key, data, cacheDuration);

        return { data, fromCache: false };
    } catch (error) {
        // Fetch failed - try cache as fallback
        const cached = await getCachedDataForced<T>(key);
        if (cached !== null) {
            console.log('Using cached data due to fetch error');
            return { data: cached, fromCache: true };
        }
        throw error;
    }
}

// --- Metadata helpers ---

async function getCacheMetadata(): Promise<CacheMetadata> {
    try {
        const metadata = await AsyncStorage.getItem(CACHE_METADATA_KEY);
        return metadata ? JSON.parse(metadata) : {};
    } catch {
        return {};
    }
}

async function updateCacheMetadata(
    key: string,
    info: { timestamp: number; expiresAt: number; size: number }
): Promise<void> {
    const metadata = await getCacheMetadata();
    metadata[key] = info;
    await AsyncStorage.setItem(CACHE_METADATA_KEY, JSON.stringify(metadata));
}

async function removeCacheMetadata(key: string): Promise<void> {
    const metadata = await getCacheMetadata();
    delete metadata[key];
    await AsyncStorage.setItem(CACHE_METADATA_KEY, JSON.stringify(metadata));
}

/**
 * Get cache statistics
 */
export async function getCacheStats(): Promise<{
    totalEntries: number;
    totalSize: number;
    expiredEntries: number;
}> {
    const metadata = await getCacheMetadata();
    const now = Date.now();

    let totalSize = 0;
    let expiredEntries = 0;

    for (const info of Object.values(metadata)) {
        totalSize += info.size;
        if (now > info.expiresAt) {
            expiredEntries++;
        }
    }

    return {
        totalEntries: Object.keys(metadata).length,
        totalSize,
        expiredEntries,
    };
}
