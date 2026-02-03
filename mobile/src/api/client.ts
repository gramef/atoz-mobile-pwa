import axios from 'axios';
import axiosRetry from 'axios-retry';
import { API_BASE_URL } from '../config';
import { useAuthStore } from '../state/auth';
import AsyncStorage from '@react-native-async-storage/async-storage';

const instance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000, // 30 second timeout
});

// Configure retry logic for network failures
axiosRetry(instance, {
  retries: 2, // Retry failed requests up to 2 times
  retryDelay: axiosRetry.exponentialDelay, // Exponential backoff
  retryCondition: (error) => {
    // Retry on network errors or 5xx server errors
    return axiosRetry.isNetworkOrIdempotentRequestError(error) ||
      (error.response?.status ?? 0) >= 500;
  },
});

function isWeb() {
  return typeof window !== 'undefined' && typeof document !== 'undefined';
}

async function cacheSet(key: string, value: string) {
  try {
    if (isWeb() && window.localStorage) {
      window.localStorage.setItem(key, value);
      return;
    }
    await AsyncStorage.setItem(key, value);
  } catch { }
}

async function cacheGet(key: string) {
  try {
    if (isWeb() && window.localStorage) {
      return window.localStorage.getItem(key);
    }
    return await AsyncStorage.getItem(key);
  } catch {
    return null;
  }
}

function stableStringify(value: any) {
  if (!value || typeof value !== 'object' || Array.isArray(value)) {
    return JSON.stringify(value ?? null);
  }
  const keys = Object.keys(value).sort();
  const out: Record<string, any> = {};
  for (const k of keys) out[k] = value[k];
  return JSON.stringify(out);
}

async function cachedGet<T = any>(
  url: string,
  {
    params,
    ttlMs = 5 * 60_000,
    allowStale = true,
  }: { params?: Record<string, any>; ttlMs?: number; allowStale?: boolean } = {}
) {
  const key = `cache:v1:${url}:${stableStringify(params)}`;
  const now = Date.now();
  try {
    const res = await instance.get<T>(url, { params });
    await cacheSet(key, JSON.stringify({ ts: now, data: res.data }));
    return res;
  } catch (e: any) {
    const raw = await cacheGet(key);
    if (!raw) throw e;
    try {
      const parsed = JSON.parse(raw);
      const ts = Number(parsed?.ts || 0);
      const age = now - ts;
      if (!allowStale && age > ttlMs) {
        throw e;
      }
      return {
        data: parsed?.data,
        status: 200,
        statusText: 'OK',
        headers: {},
        config: { url, params },
        request: null,
      } as any;
    } catch {
      throw e;
    }
  }
}

let hydrationPromise: Promise<void> | null = null;

async function ensureAuthReady() {
  const state = useAuthStore.getState();
  if (state.isReady) return;
  if (!hydrationPromise) {
    hydrationPromise = state
      .hydrate()
      .catch(() => { })
      .finally(() => {
        hydrationPromise = null;
      });
  }
  await hydrationPromise;
}

instance.interceptors.request.use(async (config) => {
  await ensureAuthReady();
  const token = useAuthStore.getState().token;
  if (token) {
    config.headers = config.headers || {};
    (config.headers as any)['Authorization'] = `Bearer ${token}`;
  }
  return config;
});

export function login(email: string, password: string, deviceName?: string) {
  const body = new URLSearchParams({ email, password, device_name: deviceName || 'mobile' });
  return instance.post('/auth/login', body.toString(), {
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', Accept: 'application/json' },
  });
}

export function register(data: {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
  role: 'agent' | 'client';
  phone?: string;
  title?: string;
}) {
  return instance.post('/auth/register', {
    first_name: data.firstName,
    last_name: data.lastName,
    email: data.email,
    password: data.password,
    role: data.role,
    phone: data.phone,
    title: data.title,
  }, {
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
  });
}

export function me(token?: string) {
  const headers: any = { Accept: 'application/json' };
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }
  return instance.get('/auth/me', { headers });
}

export function getInterpreterJobs(params?: Record<string, any>) {
  return cachedGet('/interpreter-jobs', { params, ttlMs: 60_000, allowStale: true });
}

export function getTranslatorJobs(params?: Record<string, any>) {
  return cachedGet('/translator-jobs', { params, ttlMs: 60_000, allowStale: true });
}

export function getInterpreterJob(id: number) {
  return cachedGet(`/interpreter-jobs/${id}`, { ttlMs: 24 * 60 * 60_000, allowStale: true });
}

export function getTranslatorJob(id: number) {
  return cachedGet(`/translator-jobs/${id}`, { ttlMs: 24 * 60 * 60_000, allowStale: true });
}

export function getLanguages() {
  return cachedGet('/languages', { ttlMs: 7 * 24 * 60 * 60_000, allowStale: true });
}

export function getStatuses() {
  return cachedGet('/statuses', { ttlMs: 7 * 24 * 60 * 60_000, allowStale: true });
}

export function getDocumentSignedUrl(id: number) {
  return instance.get(`/documents/${id}`);
}

export function updateInterpreterJob(id: number, data: any) {
  return instance.put(`/interpreter-jobs/${id}`, data);
}

export function acceptInterpreterJob(id: number) {
  return instance.post(`/interpreter-jobs/${id}/accept`);
}

export function declineInterpreterJob(id: number, reason?: string) {
  return instance.post(`/interpreter-jobs/${id}/decline`, { reason });
}

export function completeInterpreterJob(id: number) {
  return instance.post(`/interpreter-jobs/${id}/complete`);
}

export function dnaInterpreterJob(id: number) {
  return instance.post(`/interpreter-jobs/${id}/dna`);
}

export function returnInterpreterJob(id: number) {
  return instance.post(`/interpreter-jobs/${id}/return`);
}

export function acceptTranslatorJob(id: number) {
  return instance.post(`/translator-jobs/${id}/accept`);
}

export function declineTranslatorJob(id: number, reason?: string) {
  return instance.post(`/translator-jobs/${id}/decline`, { reason });
}

export function updateProfile(data: {
  title?: string;
  firstName: string;
  lastName: string;
  email: string;
  phone?: string;
}) {
  return instance.put('/auth/profile', {
    title: data.title,
    first_name: data.firstName,
    last_name: data.lastName,
    email: data.email,
    phone: data.phone,
  });
}

export function getDocuments() {
  return instance.get('/documents');
}

export function getDocument(id: number) {
  return instance.get(`/documents/${id}`);
}

export function downloadDocument(id: number) {
  return instance.get(`/documents/${id}/download`, { responseType: 'blob' });
}

export function uploadDocument(file: FormData, onUploadProgress?: (progressEvent: any) => void) {
  return instance.post('/documents', file, {
    headers: { 'Content-Type': 'multipart/form-data' },
    onUploadProgress,
  });
}

export function deleteDocument(id: number) {
  return instance.delete(`/documents/${id}`);
}

export function getConversations() {
  return instance.get('/conversations');
}

export function getConversation(id: number) {
  return instance.get(`/conversations/${id}`);
}

export function createConversation(userId: number) {
  return instance.post('/conversations', { user_id: userId });
}

export function sendMessage(conversationId: number, body: string, type: string = 'text') {
  return instance.post(`/conversations/${conversationId}/messages`, { body, type });
}

export function markMessageAsRead(messageId: number) {
  return instance.patch(`/messages/${messageId}/read`);
}

// Timesheets
export function getTimesheets(params?: {
  status?: string;
  from_date?: string;
  to_date?: string;
  page?: number;
}) {
  return instance.get('/timesheets', { params });
}

export function getTimesheet(id: number) {
  return instance.get(`/timesheets/${id}`);
}

export function createTimesheet(data: {
  job_id: number;
  agent_start_time: string;
  agent_end_time: string;
  notes?: string;
}) {
  return instance.post('/timesheets', data);
}

export function updateTimesheet(id: number, data: {
  agent_start_time?: string;
  agent_end_time?: string;
  notes?: string;
}) {
  return instance.put(`/timesheets/${id}`, data);
}

export function deleteTimesheet(id: number) {
  return instance.delete(`/timesheets/${id}`);
}

export function addTimesheetExpense(timesheetId: number, expense: {
  type: string;
  amount: number;
}) {
  return instance.post(`/timesheets/${timesheetId}/expenses`, expense);
}

export function removeTimesheetExpense(timesheetId: number, expenseId: number) {
  return instance.delete(`/timesheets/${timesheetId}/expenses/${expenseId}`);
}

export function signTimesheet(timesheetId: number, data: {
  agent_signature: string;
  client_signature: string;
  client_name: string;
  client_phone?: string;
  client_designation?: string;
}) {
  return instance.post(`/timesheets/${timesheetId}/sign`, data);
}


// ============================================
// PHASE 2: Client Invoices & Remittances API
// ============================================

// Client Invoices
export function getClientInvoices(params?: {
  status?: string;
  from_date?: string;
  to_date?: string;
  page?: number;
}) {
  return instance.get('/client-invoices', { params });
}

export function getClientInvoice(id: number) {
  return instance.get(`/client-invoices/${id}`);
}

export function downloadClientInvoicePdf(id: number) {
  return instance.get(`/client-invoices/${id}/pdf`, {
    responseType: 'blob'
  });
}

// Remittances
export function getRemittances(params?: {
  status?: string;
  page?: number;
}) {
  return instance.get('/remittances', { params });
}

export function createRemittance(data: FormData) {
  return instance.post('/remittances', data, {
    headers: { 'Content-Type': 'multipart/form-data' }
  });
}

export function getRemittance(id: number) {
  return instance.get(`/remittances/${id}`);
}

// Agent Invoices (Payslips)
export function getAgentInvoices(params?: {
  status?: string;
  from_date?: string;
  to_date?: string;
  page?: number;
}) {
  return instance.get('/agent-invoices', { params });
}

export function getAgentInvoice(id: number) {
  return instance.get(`/agent-invoices/${id}`);
}

export function downloadAgentInvoicePdf(id: number) {
  return instance.get(`/agent-invoices/${id}/pdf`, {
    responseType: 'blob'
  });
}
