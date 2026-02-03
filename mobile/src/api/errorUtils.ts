// Helper function to extract user-friendly error messages
export function getErrorMessage(error: any): string {
    if (error.response) {
        // Server responded with error status
        const data = error.response.data;

        // Laravel validation errors
        if (data?.errors) {
            const firstError = Object.values(data.errors)[0];
            if (Array.isArray(firstError) && firstError.length > 0) {
                return firstError[0] as string;
            }
        }

        // General message
        if (data?.message) {
            return data.message;
        }

        // HTTP status text
        if (error.response.statusText) {
            return error.response.statusText;
        }

        return `Request failed with status ${error.response.status}`;
    }

    if (error.request) {
        // Request made but no response
        if (error.code === 'ECONNABORTED') {
            return 'Request timeout - please check your connection';
        }
        if (error.message?.includes('Network Error')) {
            return 'Network error - please check your internet connection';
        }
        return 'No response from server - please try again';
    }

    // Something else happened
    return error.message || 'An unexpected error occurred';
}

// Retry configuration
const MAX_RETRIES = 2;
const RETRY_DELAY = 1000; // 1 second

// Helper to check if error is retryable
function isRetryableError(error: any): boolean {
    if (error.code === 'ECONNABORTED') return true; // Timeout
    if (error.response?.status === 408) return true; // Request Timeout
    if (error.response?.status === 429) return true; // Too Many Requests
    if (error.response?.status === 503) return true; // Service Unavailable
    if (error.response?.status === 504) return true; // Gateway Timeout
    if (error.message?.includes('Network Error')) return true;
    return false;
}

// Helper to wait for retry delay
function sleep(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export { sleep };
