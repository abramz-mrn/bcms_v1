'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import { customerApi } from '@/lib/api';
import { Plus, Search, Eye, Edit, Trash2 } from 'lucide-react';
import { useDebounce } from '@/hooks/use-debounce';

interface Customer {
  id: number;
  code: string;
  name: string;
  phone: string;
  email: string;
  group_area: string;
  created_at: string;
  subscriptions: Array<{
    id: number;
    status: string;
    product: { name: string };
  }>;
}

export default function CustomersPage() {
  const router = useRouter();
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const debouncedSearch = useDebounce(search, 500);

  useEffect(() => {
    fetchCustomers();
  }, [page, debouncedSearch]);

  const fetchCustomers = async () => {
    setLoading(true);
    try {
      const response = await customerApi.getAll({
        page,
        search: debouncedSearch,
        per_page: 15,
      });
      setCustomers(response.data);
      setTotalPages(response.meta.last_page);
    } catch (error) {
      console.error('Failed to fetch customers:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (! confirm('Are you sure you want to delete this customer?')) return;

    try {
      await customerApi.delete(id);
      fetchCustomers();
    } catch (error) {
      console.error('Failed to delete customer:', error);
    }
  };

  const getStatusBadge = (status: string) => {
    const colors:  Record<string, string> = {
      Active: 'bg-green-100 text-green-800',
      'Soft-Limit': 'bg-yellow-100 text-yellow-800',
      Suspend: 'bg-red-100 text-red-800',
      Registered: 'bg-blue-100 text-blue-800',
      Terminated: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  const columns = [
    { key: 'code', label: 'Code' },
    { key: 'name', label: 'Name' },
    { key: 'phone', label: 'Phone' },
    { key: 'email', label: 'Email' },
    { key: 'group_area', label: 'Area' },
    {
      key: 'subscriptions',
      label: 'Status',
      render: (customer:  Customer) => (
        <div className="flex flex-wrap gap-1">
          {customer.subscriptions.map((sub) => (
            <span
              key={sub.id}
              className={`px-2 py-0.5 rounded-full text-xs ${getStatusBadge(sub.status)}`}
            >
              {sub.status}
            </span>
          ))}
        </div>
      ),
    },
    {
      key: 'actions',
      label: 'Actions',
      render: (customer: Customer) => (
        <div className="flex items-center gap-2">
          <Button
            variant="ghost"
            size="sm"
            onClick={() => router.push(`/customers/${customer.id}`)}
          >
            <Eye className="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            onClick={() => router.push(`/customers/${customer.id}/edit`)}
          >
            <Edit className="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="sm"
            onClick={() => handleDelete(customer.id)}
            className="text-red-600 hover:text-red-700"
          >
            <Trash2 className="h-4 w-4" />
          </Button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Customers</h1>
          <p className="text-gray-600">Manage customer data</p>
        </div>
        <Button
          onClick={() => router.push('/customers/create')}
          className="bg-maroon-600 hover:bg-maroon-700"
        >
          <Plus className="h-4 w-4 mr-2" />
          Add Customer
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center gap-4">
            <div className="relative flex-1 max-w-sm">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
              <Input
                placeholder="Search customers..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <DataTable
            columns={columns}
            data={customers}
            loading={loading}
            page={page}
            totalPages={totalPages}
            onPageChange={setPage}
          />
        </CardContent>
      </Card>
    </div>
  );
}