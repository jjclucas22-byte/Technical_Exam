export default function CustomerDetails({ customer, onClose }) {
  if (!customer) {
    return null;
  }

  return (
    <div className="card shadow-sm mb-4">
      <div className="card-body">
        <div className="d-flex justify-content-between align-items-start gap-3">
          <div>
            <h2 className="h5 mb-3">Customer Details</h2>
            <dl className="row mb-0">
              <dt className="col-sm-4">Name</dt>
              <dd className="col-sm-8">
                {customer.first_name} {customer.last_name}
              </dd>

              <dt className="col-sm-4">Email</dt>
              <dd className="col-sm-8">{customer.email}</dd>

              <dt className="col-sm-4">Contact Number</dt>
              <dd className="col-sm-8">{customer.contact_number}</dd>
            </dl>
          </div>

          <button
            className="btn btn-sm btn-outline-secondary"
            type="button"
            onClick={onClose}
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
}
