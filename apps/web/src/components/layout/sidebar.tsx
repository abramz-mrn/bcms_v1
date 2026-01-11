'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { cn } from '@/lib/utils';
import { useAuthStore } from '@/stores/auth-store';
import {
  LayoutDashboard,
  Users,
  UserCog,
  Package,
  FileText,
  CreditCard,
  Ticket,
  Router,
  Settings,
  LogOut,
  Wifi,
  Building,
  Tag,
  FileBarChart,
  Bell,
  ScrollText,
} from 'lucide-react';

const menuItems = [
  { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard, permission: 'dashboard. view' },
  { name: 'Customers', href: '/customers', icon: Users, permission: 'customers.view' },
  { name: 'Subscriptions', href: '/subscriptions', icon: Package, permission: 'subscriptions.view' },
  { name: 'Invoices', href: '/invoices', icon: FileText, permission: 'invoices.view' },
  { name: 'Payments', href: '/payments', icon:  CreditCard, permission: 'payments.view' },
  { name: 'Tickets', href: '/tickets', icon: Ticket, permission: 'tickets.view' },
  { name: 'Products', href: '/products', icon:  Tag, permission: 'products.view' },
  { name: 'Routers', href: '/routers', icon: Router, permission: 'routers.view' },
  { name: 'Users', href: '/users', icon:  UserCog, permission: 'users.view' },
  { name: 'Templates', href: '/templates', icon:  Bell, permission: 'templates.view' },
  { name: 'Reports', href: '/reports', icon:  FileBarChart, permission: 'reports.view' },
  { name: 'Audit Logs', href: '/audit-logs', icon: ScrollText, permission: 'audit_logs.view' },
  { name: 'Settings', href:  '/settings', icon: Settings, permission: 'settings.view' },
];

export function Sidebar() {
  const pathname = usePathname();
  const { user, logout } = useAuthStore();

  const hasPermission = (permission: string) => {
    if (!user?. permissions) return false;
    const [resource, action] = permission.split('.');
    return user.permissions[resource]?.includes(action);
  };

  const handleLogout = () => {
    logout();
    window.location.href = '/login';
  };

  return (
    <aside className="fixed inset-y-0 left-0 z-50 hidden w-64 bg-white border-r lg:block">
      <div className="flex flex-col h-full">
        {/* Logo */}
        <div className="flex items-center gap-3 px-6 py-5 border-b">
          <div className="p-2 bg-maroon-600 rounded-lg">
            <Wifi className="h-6 w-6 text-white" />
          </div>
          <div>
            <h1 className="font-bold text-lg text-maroon-800">BCMS</h1>
            <p className="text-xs text-gray-500">Maroon-NET</p>
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
          {menuItems.map((item) => {
            if (! hasPermission(item.permission)) return null;

            const isActive = pathname === item.href || pathname.startsWith(item.href + '/');

            return (
              <Link
                key={item.href}
                href={item.href}
                className={cn(
                  'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                  isActive
                    ? 'bg-maroon-50 text-maroon-700'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                )}
              >
                <item.icon className="h-5 w-5" />
                {item.name}
              </Link>
            );
          })}
        </nav>

        {/* User & Logout */}
        <div className="p-4 border-t">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 rounded-full bg-maroon-100 flex items-center justify-center">
              <span className="text-sm font-medium text-maroon-600">
                {user?.name?. charAt(0).toUpperCase()}
              </span>
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-gray-900 truncate">{user?.name}</p>
              <p className="text-xs text-gray-500 truncate">{user?.group}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
          >
            <LogOut className="h-4 w-4" />
            Logout
          </button>
        </div>
      </div>
    </aside>
  );
}