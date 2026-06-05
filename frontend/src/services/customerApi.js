const CUSTOMER_API_URL = '/api/customers';

async function request(url, options = {}) {
  const response = await fetch(url, {
    ...options,
    headers: {
      Accept: 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...options.headers,
    },
  });

  if (response.status === 204) {
    return null;
  }

  const payload = await response.json().catch(() => ({}));

  if (!response.ok) {
    const error = new Error(payload.message || 'The request could not be completed.');
    error.status = response.status;
    error.errors = payload.errors || {};
    throw error;
  }

  return payload;
}

export const customerApi = {
  async list(search = '') {
    const query = search.trim()
      ? `?search=${encodeURIComponent(search.trim())}`
      : '';

    const payload = await request(`${CUSTOMER_API_URL}${query}`);
    return payload.data;
  },

  async get(id) {
    const payload = await request(`${CUSTOMER_API_URL}/${id}`);
    return payload.data;
  },

  async create(customer) {
    const payload = await request(CUSTOMER_API_URL, {
      method: 'POST',
      body: JSON.stringify(customer),
    });

    return payload.data;
  },

  async update(id, customer) {
    const payload = await request(`${CUSTOMER_API_URL}/${id}`, {
      method: 'PUT',
      body: JSON.stringify(customer),
    });

    return payload.data;
  },

  async remove(id) {
    await request(`${CUSTOMER_API_URL}/${id}`, {
      method: 'DELETE',
    });
  },
};
