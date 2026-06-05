import { useEffect, useState } from 'react';

const emptyCustomer = {
  first_name: '',
  last_name: '',
  email: '',
  contact_number: '',
};

export default function CustomerForm({
  customer,
  errors,
  onSubmit,
  onCancel,
  saving,
}) {
  const [form, setForm] = useState(emptyCustomer);

  useEffect(() => {
    setForm(
      customer
        ? {
            first_name: customer.first_name,
            last_name: customer.last_name,
            email: customer.email,
            contact_number: customer.contact_number,
          }
        : emptyCustomer,
    );
  }, [customer]);

  function handleChange(event) {
    const { name, value } = event.target;

    setForm((current) => ({
      ...current,
      [name]: value,
    }));
  }

  function handleSubmit(event) {
    event.preventDefault();
    onSubmit(form);
  }

  function fieldError(field) {
    return errors?.[field]?.[0];
  }

  return (
    <div className="card shadow-sm">
      <div className="card-body">
        <h2 className="h5 mb-3">
          {customer ? 'Edit Customer' : 'Create Customer'}
        </h2>

        <form onSubmit={handleSubmit} noValidate>
          <div className="mb-3">
            <label className="form-label" htmlFor="first_name">
              First Name
            </label>
            <input
              className={`form-control ${fieldError('first_name') ? 'is-invalid' : ''}`}
              id="first_name"
              name="first_name"
              value={form.first_name}
              onChange={handleChange}
              required
            />
            {fieldError('first_name') && (
              <div className="invalid-feedback">{fieldError('first_name')}</div>
            )}
          </div>

          <div className="mb-3">
            <label className="form-label" htmlFor="last_name">
              Last Name
            </label>
            <input
              className={`form-control ${fieldError('last_name') ? 'is-invalid' : ''}`}
              id="last_name"
              name="last_name"
              value={form.last_name}
              onChange={handleChange}
              required
            />
            {fieldError('last_name') && (
              <div className="invalid-feedback">{fieldError('last_name')}</div>
            )}
          </div>

          <div className="mb-3">
            <label className="form-label" htmlFor="email">
              Email Address
            </label>
            <input
              className={`form-control ${fieldError('email') ? 'is-invalid' : ''}`}
              id="email"
              name="email"
              type="email"
              value={form.email}
              onChange={handleChange}
              required
            />
            {fieldError('email') && (
              <div className="invalid-feedback">{fieldError('email')}</div>
            )}
          </div>

          <div className="mb-3">
            <label className="form-label" htmlFor="contact_number">
              Contact Number
            </label>
            <input
              className={`form-control ${fieldError('contact_number') ? 'is-invalid' : ''}`}
              id="contact_number"
              name="contact_number"
              value={form.contact_number}
              onChange={handleChange}
              required
            />
            {fieldError('contact_number') && (
              <div className="invalid-feedback">
                {fieldError('contact_number')}
              </div>
            )}
          </div>

          <div className="d-flex gap-2">
            <button className="btn btn-primary" type="submit" disabled={saving}>
              {saving ? 'Saving...' : customer ? 'Update Customer' : 'Create Customer'}
            </button>

            {customer && (
              <button
                className="btn btn-outline-secondary"
                type="button"
                onClick={onCancel}
                disabled={saving}
              >
                Cancel
              </button>
            )}
          </div>
        </form>
      </div>
    </div>
  );
}
