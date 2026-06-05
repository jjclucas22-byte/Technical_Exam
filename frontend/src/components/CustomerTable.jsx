export default function CustomerTable({
  customers,
  loading,
  deletingId,
  onView,
  onEdit,
  onDelete,
}) {
  if (loading) {
    return <p className="text-secondary mb-0">Loading customers...</p>;
  }

  if (customers.length === 0) {
    return <p className="text-secondary mb-0">No customers found.</p>;
  }

  return (
    <div className="table-responsive">
      <table className="table table-striped table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email Address</th>
            <th>Contact Number</th>
            <th className="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          {customers.map((customer) => (
            <tr key={customer.id}>
              <td>
                {customer.first_name} {customer.last_name}
              </td>
              <td>{customer.email}</td>
              <td>{customer.contact_number}</td>
              <td className="text-end">
                <div className="btn-group btn-group-sm" role="group">
                  <button
                    className="btn btn-outline-info"
                    type="button"
                    onClick={() => onView(customer.id)}
                  >
                    View
                  </button>
                  <button
                    className="btn btn-outline-primary"
                    type="button"
                    onClick={() => onEdit(customer)}
                  >
                    Edit
                  </button>
                  <button
                    className="btn btn-outline-danger"
                    type="button"
                    onClick={() => onDelete(customer)}
                    disabled={deletingId === customer.id}
                  >
                    {deletingId === customer.id ? 'Deleting...' : 'Delete'}
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
