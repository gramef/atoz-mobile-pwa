import { useState, useEffect, useCallback } from 'react';
import { fetchWithCache, CACHE_DURATION, isOnline } from '../services/cache';

interface UseCachedDataResult<T> {
    data: T | null;
    loading: boolean;
    error: Error | null;
    fromCache: boolean;
    refetch: () => Promise<void>;
    isOffline: boolean;
}

/**
 * Hook for fetching data with automatic caching and offline support
 */
export function useCachedData<T>(
    cacheKey: string,
    fetcher: () => Promise<T>,
    options?: {
        cacheDuration?: number;
        enabled?: boolean;
    }
): UseCachedDataResult<T> {
    const [data, setData] = useState<T | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<Error | null>(null);
    const [fromCache, setFromCache] = useState(false);
    const [isOfflineState, setIsOfflineState] = useState(false);

    const { cacheDuration = CACHE_DURATION.MEDIUM, enabled = true } = options || {};

    const fetchData = useCallback(async () => {
        if (!enabled) {
            setLoading(false);
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const online = await isOnline();
            setIsOfflineState(!online);

            const result = await fetchWithCache(cacheKey, fetcher, cacheDuration);
            setData(result.data);
            setFromCache(result.fromCache);
        } catch (err) {
            setError(err instanceof Error ? err : new Error('Unknown error'));
        } finally {
            setLoading(false);
        }
    }, [cacheKey, fetcher, cacheDuration, enabled]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    return {
        data,
        loading,
        error,
        fromCache,
        refetch: fetchData,
        isOffline: isOfflineState,
    };
}

/**
 * Hook for listening to network connectivity changes
 */
export function useNetworkStatus(): {
    isOnline: boolean;
    isLoading: boolean;
} {
    const [online, setOnline] = useState(true);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let isMounted = true;

        const checkConnectivity = async () => {
            const status = await isOnline();
            if (isMounted) {
                setOnline(status);
                setLoading(false);
            }
        };

        checkConnectivity();

        // Check periodically
        const interval = setInterval(checkConnectivity, 10000);

        return () => {
            isMounted = false;
            clearInterval(interval);
        };
    }, []);

    return { isOnline: online, isLoading: loading };
}
