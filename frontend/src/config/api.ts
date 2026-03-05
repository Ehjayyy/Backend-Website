export const API_BASE = '/api';

export const API_ENDPOINTS = {
  auth: {
    login: `${API_BASE}/auth/login.php`,
    register: `${API_BASE}/auth/register.php`,
    me: `${API_BASE}/auth/me.php`,
  },
  products: {
    list: `${API_BASE}/products/index.php`,
    create: `${API_BASE}/products/create.php`,
    update: `${API_BASE}/products/update.php`,
    delete: `${API_BASE}/products/delete.php`,
    product: (id: number) => `${API_BASE}/products/[id].php?id=${id}`,
    shop: `${API_BASE}/products/shop.php`,
  },
  categories: `${API_BASE}/categories/index.php`,
  orders: {
    list: `${API_BASE}/orders/index.php`,
    create: `${API_BASE}/orders/create.php`,
    order: (id: number) => `${API_BASE}/orders/[id].php?id=${id}`,
  },
  shops: {
    list: `${API_BASE}/shops/index.php`,
    create: `${API_BASE}/shops/create.php`,
    me: `${API_BASE}/shops/me.php`,
    shop: (id: number) => `${API_BASE}/shops/[id].php?id=${id}`,
  },
  reports: {
    list: `${API_BASE}/reports/index.php`,
    create: `${API_BASE}/reports/create.php`,
    me: `${API_BASE}/reports/me.php`,
    report: (id: number) => `${API_BASE}/reports/[id].php?id=${id}`,
  },
  payments: {
    list: `${API_BASE}/payments/index.php`,
    create: `${API_BASE}/payments/create.php`,
    order: (orderId: number) => `${API_BASE}/payments/order/[orderId].php?orderId=${orderId}`,
  },
  admin: {
    dashboard: `${API_BASE}/admin/dashboard/stats.php`,
    users: `${API_BASE}/admin/users.php`,
    user: (id: number) => `${API_BASE}/admin/users/[id].php?id=${id}`,
    shops: `${API_BASE}/admin/shops.php`,
    shop: (id: number) => `${API_BASE}/admin/shops/[id].php?id=${id}`,
    products: `${API_BASE}/admin/products.php`,
    product: (id: number) => `${API_BASE}/admin/products/[id].php?id=${id}`,
    reports: `${API_BASE}/admin/reports.php`,
    report: (id: number) => `${API_BASE}/admin/reports/[id].php?id=${id}`,
    orders: `${API_BASE}/admin/orders.php`,
    order: (id: number) => `${API_BASE}/admin/orders/[id].php?id=${id}`,
    categories: `${API_BASE}/admin/categories.php`,
    category: (id: number) => `${API_BASE}/admin/categories/[id].php?id=${id}`,
  },
};
