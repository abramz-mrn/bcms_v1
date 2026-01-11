'use client';

import { useAuthStore } from '@/stores/auth-store';
import { Bell, Menu } from 'lucide-react';
import { Button } from '@/components/ui/button';

export function Header() {
  const { user } = useAuthStore();

  return (
    <header className="sticky top-0 z-40 flex h-16 items-center gap-4 border-b bg-white px-4 sm:px-6">
      <Button variant="ghost" size="icon" className="lg:hidden">
        <Menu className="h-5 w-5" />
      </Button>

      <div className="flex-1" />

      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" className="relative">
          <Bell className="h-5 w-5" />
          <span className="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-[10px] font-medium text-white flex items-center justify-center">
            3
          </span>
        </Button>

        <div className="flex items-center gap-3">
          <div className="hidden sm:block text-right">
            <p className="text-sm font-medium">{user?.name}</p>
            <p className="text-xs text-gray-500">{user?.group}</p>
          </div>
          <div className="w-10 h-10 rounded-full bg-maroon-100 flex items-center justify-center">
            <span className="text-sm font-medium text-maroon-600">
              {user?.name?.charAt(0).toUpperCase()}
            </span>
          </div>
        </div>
      </div>
    </header>
  );
}