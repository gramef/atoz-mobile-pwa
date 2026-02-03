/**
 * Web Maps Service
 * Provides cross-platform maps functionality for PWA using native browser geolocation
 * and links to external map applications
 */

// Types
export interface Coordinates {
    latitude: number;
    longitude: number;
}

export interface LocationResult {
    coords: Coordinates;
    accuracy: number;
    timestamp: number;
}

export interface MapLink {
    url: string;
    label: string;
}

/**
 * Check if geolocation is available
 */
export function isGeolocationAvailable(): boolean {
    return typeof navigator !== 'undefined' && 'geolocation' in navigator;
}

/**
 * Get current location using browser Geolocation API
 */
export async function getCurrentLocation(
    options?: PositionOptions
): Promise<LocationResult> {
    if (!isGeolocationAvailable()) {
        throw new Error('Geolocation is not supported by this browser');
    }

    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    coords: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                    },
                    accuracy: position.coords.accuracy,
                    timestamp: position.timestamp,
                });
            },
            (error) => {
                let message = 'Failed to get location';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Location permission denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Location information unavailable';
                        break;
                    case error.TIMEOUT:
                        message = 'Location request timed out';
                        break;
                }
                reject(new Error(message));
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 60000,
                ...options,
            }
        );
    });
}

/**
 * Watch location changes
 */
export function watchLocation(
    callback: (location: LocationResult) => void,
    errorCallback?: (error: GeolocationPositionError) => void,
    options?: PositionOptions
): number | null {
    if (!isGeolocationAvailable()) {
        console.error('Geolocation is not supported');
        return null;
    }

    return navigator.geolocation.watchPosition(
        (position) => {
            callback({
                coords: {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                },
                accuracy: position.coords.accuracy,
                timestamp: position.timestamp,
            });
        },
        errorCallback,
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 30000,
            ...options,
        }
    );
}

/**
 * Stop watching location
 */
export function clearLocationWatch(watchId: number): void {
    if (isGeolocationAvailable() && watchId) {
        navigator.geolocation.clearWatch(watchId);
    }
}

/**
 * Calculate distance between two coordinates (in meters)
 */
export function calculateDistance(from: Coordinates, to: Coordinates): number {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = (from.latitude * Math.PI) / 180;
    const φ2 = (to.latitude * Math.PI) / 180;
    const Δφ = ((to.latitude - from.latitude) * Math.PI) / 180;
    const Δλ = ((to.longitude - from.longitude) * Math.PI) / 180;

    const a =
        Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}

/**
 * Format distance for display
 */
export function formatDistance(meters: number): string {
    if (meters < 1000) {
        return `${Math.round(meters)}m`;
    }
    return `${(meters / 1000).toFixed(1)}km`;
}

/**
 * Generate map links for opening in external apps
 */
export function getMapLinks(coords: Coordinates, label?: string): MapLink[] {
    const { latitude, longitude } = coords;
    const encodedLabel = encodeURIComponent(label || 'Location');

    const links: MapLink[] = [];

    // Detect platform
    const userAgent = navigator.userAgent.toLowerCase();
    const isIOS = /iphone|ipad|ipod/.test(userAgent);
    const isAndroid = /android/.test(userAgent);

    if (isIOS) {
        // Apple Maps (default on iOS)
        links.push({
            url: `maps://maps.apple.com/?q=${encodedLabel}&ll=${latitude},${longitude}`,
            label: 'Apple Maps',
        });
    }

    // Google Maps (works on all platforms)
    links.push({
        url: `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`,
        label: 'Google Maps',
    });

    // Google Maps directions
    links.push({
        url: `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`,
        label: 'Get Directions (Google)',
    });

    if (isAndroid) {
        // Waze (popular on Android)
        links.push({
            url: `https://waze.com/ul?ll=${latitude},${longitude}&navigate=yes`,
            label: 'Waze',
        });
    }

    return links;
}

/**
 * Open location in default maps app
 */
export function openInMaps(
    coords: Coordinates,
    label?: string,
    preferredApp?: 'google' | 'apple' | 'waze'
): void {
    const { latitude, longitude } = coords;
    const encodedLabel = encodeURIComponent(label || 'Location');

    let url: string;

    const userAgent = navigator.userAgent.toLowerCase();
    const isIOS = /iphone|ipad|ipod/.test(userAgent);

    if (preferredApp === 'google') {
        url = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
    } else if (preferredApp === 'apple' && isIOS) {
        url = `maps://maps.apple.com/?q=${encodedLabel}&ll=${latitude},${longitude}`;
    } else if (preferredApp === 'waze') {
        url = `https://waze.com/ul?ll=${latitude},${longitude}&navigate=yes`;
    } else {
        // Default based on platform
        if (isIOS) {
            url = `maps://maps.apple.com/?q=${encodedLabel}&ll=${latitude},${longitude}`;
        } else {
            url = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
        }
    }

    window.open(url, '_blank');
}

/**
 * Get directions to a location
 */
export function getDirections(
    destination: Coordinates,
    origin?: Coordinates
): void {
    let url = `https://www.google.com/maps/dir/?api=1&destination=${destination.latitude},${destination.longitude}`;

    if (origin) {
        url += `&origin=${origin.latitude},${origin.longitude}`;
    }

    window.open(url, '_blank');
}

/**
 * Generate a static map image URL (using OpenStreetMap)
 */
export function getStaticMapUrl(
    coords: Coordinates,
    zoom: number = 15,
    width: number = 400,
    height: number = 300
): string {
    // Using OpenStreetMap static map service
    const { latitude, longitude } = coords;
    return `https://staticmap.openstreetmap.de/staticmap.php?center=${latitude},${longitude}&zoom=${zoom}&size=${width}x${height}&maptype=mapnik&markers=${latitude},${longitude},red-pushpin`;
}

/**
 * Embed map component URL for iframe (using OpenStreetMap)
 */
export function getEmbedMapUrl(coords: Coordinates, zoom: number = 15): string {
    const { latitude, longitude } = coords;
    // OpenStreetMap embed URL
    return `https://www.openstreetmap.org/export/embed.html?bbox=${longitude - 0.01},${latitude - 0.01},${longitude + 0.01},${latitude + 0.01}&layer=mapnik&marker=${latitude},${longitude}`;
}
