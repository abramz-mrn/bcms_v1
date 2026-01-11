import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  photo: string | null;
  group: string;
  permissions: Record<string, string[]>;
}

interface AuthState {
  user: User | null;
  token: string | null;
  setUser: (user: User) => void;
  setToken: (token: string) => void;
  logout: () => void;
  hasPermission: (resource: string, action: string) => boolean;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      setUser: (user) => set({ user }),
      setToken: (token) => {
        set({ token });
        if (typeof window !== 'undefined') {
          localStorage.setItem('bcms_token', token);
        }
      },
      logout: () => {
        set({ user: null, token: null });
        if (typeof window !== 'undefined') {
          localStorage.removeItem('bcms_token');
          localStorage.removeItem('bcms_user');
        }
      },
      hasPermission: (resource, action) => {
        const { user } = get();
        if (!user?. permissions) return false;
        return user.permissions[resource]?.includes(action) ??  false;
      },
    }),
    {
      name: 'bcms_auth',
      partialize: (state) => ({ user: state.user, token: state.token }),
    }
  )
);