'use client';

import { useEffect, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboardApi } from '@/lib/api';
import { Users, UserCheck, UserX, FileText, AlertCircle, DollarSign, Ticket, Activity } from 'lucide-react';

interface DashboardSummary {
  total_customers:  number;
  active_customers:  number;
  suspended_subscriptions: number;
  unpaid_invoices: number;
  unpaid_amount: number;
  open_tickets: number;
  monthly_revenue: number;
}

interface RecentActivity {
  id: number;
  user_name: string;
  action: string;
  resource_type: string;
  description: string;
  created_at: string;
}

interface RecentTicket {
  id:  number;
  ticket_number: string;
  customer_name: string;
  subject: string;
  category: string;
  priority: string;
  status: string;
  sla_breached: boolean;
  created_at: string;
}

export default function DashboardPage() {
  const [summary, setSummary] = useState<DashboardSummary | null>(null);
  const [activities, setActivities] = useState<RecentActivity[]>([]);
  const [tickets, setTickets] = useState<RecentTicket[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [summaryRes, activitiesRes, ticketsRes] = await Promise.all([
          dashboardApi.getSummary(),
          dashboardApi. getRecentActivities(),
          dashboardApi. getRecentTickets(),
        ]);
        setSummary(summaryRes.data);
        setActivities(activitiesRes.data);
        setTickets(ticketsRes. data);
      } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount);
  };

  const stats = summary ? [
    {
      title: 'Total Customers',
      value: summary.total_customers,
      icon: Users,
      color: 'text-blue-600',
      bgColor: 'bg-blue-100',
    },
    {
      title: 'Active Customers',
      value: summary.active_customers,
      icon: UserCheck,
      color: 'text-green-600',
      bgColor: 'bg-green-100',
    },
    {
      title: 'Suspended',
      value: summary.suspended_subscriptions,
      icon: UserX,
      color: 'text-red-600',
      bgColor: 'bg-red-100',
    },
    {
      title: 'Unpaid Invoices',
      value: summary.unpaid_invoices,
      icon: FileText,
      color: 'text-orange-600',
      bgColor:  'bg-orange-100',
    },
    {
      title: 'Unpaid Amount',
      value: formatCurrency(summary.unpaid_amount),
      icon: AlertCircle,
      color: 'text-yellow-600',
      bgColor:  'bg-yellow-100',
      isString: true,
    },
    {
      title: 'Open Tickets',
      value: summary.open_tickets,
      icon: Ticket,
      color:  'text-purple-600',
      bgColor: 'bg-purple-100',
    },
    {
      title: 'Monthly Revenue',
      value:  formatCurrency(summary.monthly_revenue),
      icon: DollarSign,
      color: 'text-emerald-600',
      bgColor: 'bg-emerald-100',
      isString: true,
    },
  ] :  [];

  const getPriorityColor = (priority:  string) => {
    switch (priority) {
      case 'critical': return 'bg-red-100 text-red-800';
      case 'high': return 'bg-orange-100 text-orange-800';
      case 'medium': return 'bg-yellow-100 text-yellow-800';
      case 'low': return 'bg-green-100 text-green-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'open': return 'bg-blue-100 text-blue-800';
      case 'assigned': return 'bg-purple-100 text-purple-800';
      case 'in_progress': return 'bg-yellow-100 text-yellow-800';
      case 'resolved':  return 'bg-green-100 text-green-800';
      case 'closed': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-maroon-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-600">Welcome to BCMS - Billing & Customer Management System</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
        {stats.map((stat, index) => (
          <Card key={index}>
            <CardContent className="p-4">
              <div className="flex items-center space-x-3">
                <div className={`p-2 rounded-lg ${stat.bgColor}`}>
                  <stat.icon className={`h-5 w-5 ${stat.color}`} />
                </div>
                <div>
                  <p className="text-xs text-gray-500">{stat.title}</p>
                  <p className="text-lg font-semibold text-gray-900">
                    {stat.isString ? stat.value : stat.value.toLocaleString()}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Tickets */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Ticket className="h-5 w-5" />
              Recent Tickets
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {tickets.length === 0 ? (
                <p className="text-gray-500 text-center py-4">No open tickets</p>
              ) : (
                tickets.slice(0, 5).map((ticket) => (
                  <div
                    key={ticket.id}
                    className={`p-3 rounded-lg border ${ticket.sla_breached ? 'border-red-300 bg-red-50' :  'border-gray-200'}`}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <span className="font-medium text-sm">{ticket.ticket_number}</span>
                          {ticket.sla_breached && (
                            <span className="text-xs bg-red-600 text-white px-1. 5 py-0.5 rounded">
                              SLA Breached
                            </span>
                          )}
                        </div>
                        <p className="text-sm text-gray-600 mt-1">{ticket.subject}</p>
                        <p className="text-xs text-gray-500">{ticket.customer_name}</p>
                      </div>
                      <div className="flex flex-col items-end gap-1">
                        <span className={`text-xs px-2 py-0.5 rounded-full ${getPriorityColor(ticket.priority)}`}>
                          {ticket.priority}
                        </span>
                        <span className={`text-xs px-2 py-0.5 rounded-full ${getStatusColor(ticket.status)}`}>
                          {ticket.status. replace('_', ' ')}
                        </span>
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>
          </CardContent>
        </Card>

        {/* Recent Activities */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Activity className="h-5 w-5" />
              Recent Activities
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {activities.length === 0 ? (
                <p className="text-gray-500 text-center py-4">No recent activities</p>
              ) : (
                activities.slice(0, 8).map((activity) => (
                  <div key={activity.id} className="flex items-start gap-3 p-2 hover:bg-gray-50 rounded-lg">
                    <div className="w-8 h-8 rounded-full bg-maroon-100 flex items-center justify-center flex-shrink-0">
                      <span className="text-xs font-medium text-maroon-600">
                        {activity.user_name. charAt(0).toUpperCase()}
                      </span>
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm text-gray-900">
                        <span className="font-medium">{activity.user_name}</span>
                        {' '}
                        <span className="text-gray-600">{activity.description || `${activity.action} ${activity. resource_type}`}</span>
                      </p>
                      <p className="text-xs text-gray-500">{activity.created_at}</p>
                    </div>
                  </div>
                ))
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}