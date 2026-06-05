import { useEffect, useState } from 'react';
import CustomerDetails from './components/CustomerDetails';
import CustomerForm from './components/CustomerForm';
import CustomerTable from './components/CustomerTable';
import { customerApi } from './services/customerApi';

export default function App() {
  const [customers, setCustomers] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [editingCustomer, setEditingCustomer] = useState(null);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [validationErrors, setValidationErrors] = useState({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [deletingId, setDeletingId] = useState(null);
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    loadCustomers();
  }, []);

  async function loadCustomers(search = '') {
    setLoading(true);
    setError('');

    try {
      const data = await customerApi.list(search);
      setCustomers(data);
    } catch (requestError) {
      setError(requestError.message);
    } finally {
      setLoading(false);
    }
  }

  async function handleSearch(event) {
    event.preventDefault();
    await loadCustomers(searchTerm);
  }

  async function handleClearSearch() {
    setSearchTerm('');
    await loadCustomers('');
  }

  async function handleSave(form) {
    setSaving(true);
    setMessage('');
    setError('');
    setValidationErrors({});

    try {
      if (editingCustomer) {
        await customerApi.update(editingCustomer.id, form);
        setMessage('Customer updated successfully.');
      } else {
        await customerApi.create(form);
        setMessage('Customer created successfully.');
      }

      setEditingCustomer(null);
      setSelectedCustomer(null);
      await loadCustomers(searchTerm);
    } catch (requestError) {
      if (requestError.status === 422) {
        setValidationErrors(requestError.errors);
      } else {
        setError(requestError.message);
      }
    } finally {
      setSaving(false);
    }
  }

  async function handleView(id) {
    setError('');

    try {
      const customer = await customerApi.get(id);
      setSelectedCustomer(customer);
    } catch (requestError) {
      setError(requestError.message);
    }
  }

  function handleEdit(customer) {
    setEditingCustomer(customer);
    setSelectedCustomer(null);
    setValidationErrors({});
    setMessage('');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  async function handleDelete(customer) {
    const confirmed = window.confirm(
      `Delete ${customer.first_name} ${customer.last_name}?`,
    );

    if (!confirmed) {
      return;
    }

    setDeletingId(customer.id);
    setMessage('');
    setError('');

    try {
      await customerApi.remove(customer.id);
      setMessage('Customer deleted successfully.');

      if (selectedCustomer?.id === customer.id) {
        setSelectedCustomer(null);
      }

      if (editingCustomer?.id === customer.id) {
        setEditingCustomer(null);
      }

      await loadCustomers(searchTerm);
    } catch (requestError) {
      setError(requestError.message);
    } finally {
      setDeletingId(null);
    }
  }

  function handleCancelEdit() {
    setEditingCustomer(null);
    setValidationErrors({});
  }

  return (
    <main className="container py-5">
      <div className="mb-4">
        <h1 className="display-6 mb-1">Customer Management</h1>
        <p className="text-secondary mb-0">
          Create, view, update, delete, and search customer records.
        </p>
      </div>

      {message && (
        <div className="alert alert-success" role="alert">
          {message}
        </div>
      )}

      {error && (
        <div className="alert alert-danger" role="alert">
          {error}
        </div>
      )}

      <div className="row g-4">
        <div className="col-lg-4">
          <CustomerForm
            customer={editingCustomer}
            errors={validationErrors}
            onSubmit={handleSave}
            onCancel={handleCancelEdit}
            saving={saving}
          />
        </div>

        <div className="col-lg-8">
          <CustomerDetails
            customer={selectedCustomer}
            onClose={() => setSelectedCustomer(null)}
          />

          <div className="card shadow-sm">
            <div className="card-body">
              <div className="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                <h2 className="h5 mb-0">Customers</h2>

                <form
                  className="d-flex gap-2"
                  onSubmit={handleSearch}
                  role="search"
                >
                  <input
                    className="form-control"
                    type="search"
                    placeholder="Search name or email"
                    value={searchTerm}
                    onChange={(event) => setSearchTerm(event.target.value)}
                  />
                  <button className="btn btn-primary" type="submit">
                    Search
                  </button>
                  <button
                    className="btn btn-outline-secondary"
                    type="button"
                    onClick={handleClearSearch}
                  >
                    Clear
                  </button>
                </form>
              </div>

              <CustomerTable
                customers={customers}
                loading={loading}
                deletingId={deletingId}
                onView={handleView}
                onEdit={handleEdit}
                onDelete={handleDelete}
              />
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
