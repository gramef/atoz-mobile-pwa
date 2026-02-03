import { create } from 'zustand';
import * as SecureStore from 'expo-secure-store';

type User = {
  id: number;
  name: string;
  email: string;
  roles: string[];
  first_name?: string;
  last_name?: string;
  role?: string;
  title?: string;
  phone?: string;
};

type State = {
  token: string | null;
  user: User | null;
  isReady: boolean;
  setAuth: (token: string, user: User) => Promise<void>;
  clear: () => Promise<void>;
  logout: () => Promise<void>;
  hydrate: () => Promise<void>;
};


function isWeb() {
  return typeof window !== 'undefined' && typeof document !== 'undefined';
}

async function safeSetItem(key: string, value: string) {
  try {
    if (isWeb() && window.localStorage) {
      window.localStorage.setItem(key, value);
    } else {
      await SecureStore.setItemAsync(key, value);
    }
  } catch (e) {
    console.error('AuthStore: safeSetItem error', key, e);
  }
}

async function safeGetItem(key: string) {
  try {
    if (isWeb() && window.localStorage) {
      return window.localStorage.getItem(key);
    }
    return await SecureStore.getItemAsync(key);
  } catch (e) {
    console.error('AuthStore: safeGetItem error', key, e);
    return null;
  }
}

async function safeDeleteItem(key: string) {
  try {
    if (isWeb() && window.localStorage) {
      window.localStorage.removeItem(key);
    } else {
      await SecureStore.deleteItemAsync(key);
    }
  } catch (e) {
    console.error('AuthStore: safeDeleteItem error', key, e);
  }
}

export const useAuthStore = create<State>((set: any) => ({
  token: null,
  user: null,
  isReady: false,
  setAuth: async (token: string, user: User) => {
    console.log('AuthStore: setAuth calling...', token?.substring(0, 10));
    await safeSetItem('token', token);
    await safeSetItem('user', JSON.stringify(user));
    console.log('AuthStore: setAuth finished writing to storage');
    set({ token, user, isReady: true });
  },
  clear: async () => {
    await safeDeleteItem('token');
    await safeDeleteItem('user');
    set({ token: null, user: null, isReady: true });
  },
  logout: async () => {
    await safeDeleteItem('token');
    await safeDeleteItem('user');
    set({ token: null, user: null, isReady: true });
  },
  hydrate: async () => {
    console.log('AuthStore: hydrate calling...');
    const t = await safeGetItem('token');
    const u = await safeGetItem('user');
    console.log('AuthStore: hydrate got from storage', t ? 'FOUND_TOKEN' : 'NO_TOKEN', u);
    let parsedUser: User | null = null;
    if (u) {
      try {
        parsedUser = JSON.parse(u);
      } catch {
        parsedUser = null;
        await safeDeleteItem('user');
      }
    }
    set({ token: t || null, user: parsedUser, isReady: true });
  },
}));
