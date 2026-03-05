/* eslint-disable react-refresh/only-export-components */
import React, { createContext, useContext, useMemo, useState, useCallback } from "react";
import { useAuth } from "./authStore";
import { API_ENDPOINTS } from "../config/api";

export type DashboardStats = {
  users: number;
  shops: number;
  products: number;
  orders: number;
  reports: number;
};

export type User = {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
  shops: Array<{
    id: number;
    shop_name: string;
  }>;
};

export type Shop = {
  id: number;
  user_id: number;
  shop_name: string;
  description: string;
  created_at: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
  product_count: number;
};

export type Product = {
  id: number;
  name: string;
  price: number;
  stock: number;
  description: string;
  created_at: string;
  shop: {
    id: number;
    shop_name: string;
  };
  category: {
    id: number;
    name: string;
  };
};

export type Report = {
  id: number;
  user_id: number;
  target_type: string;
  target_id: number;
  reason: string;
  status: string;
  created_at: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
};

export type Order = {
  id: number;
  user_id: number;
  order_date: string;
  status: string;
  total_amount: number;
  user: {
    id: number;
    name: string;
    email: string;
  };
  items: Array<{
    id: number;
    product_id: number;
    quantity: number;
    price: number;
  }>;
  payment: {
    id: number;
    order_id: number;
    payment_method: string;
    payment_status: string;
    payment_date: string;
  } | null;
};

export type Category = {
  id: number;
  name: string;
};

type AdminContextValue = {
  // Dashboard
  stats: DashboardStats | null;
  statsLoading: boolean;
  fetchStats: () => Promise<void>;

  // Users
  users: User[];
  usersLoading: boolean;
  fetchUsers: () => Promise<void>;
  deleteUser: (id: number) => Promise<void>;

  // Shops
  shops: Shop[];
  shopsLoading: boolean;
  fetchShops: () => Promise<void>;
  deleteShop: (id: number) => Promise<void>;

  // Products
  products: Product[];
  productsLoading: boolean;
  fetchProducts: () => Promise<void>;
  deleteProduct: (id: number) => Promise<void>;

  // Reports
  reports: Report[];
  reportsLoading: boolean;
  fetchReports: () => Promise<void>;
  deleteReport: (id: number) => Promise<void>;
  updateReportStatus: (id: number, status: string) => Promise<void>;

  // Orders
  orders: Order[];
  ordersLoading: boolean;
  fetchOrders: () => Promise<void>;

  // Categories
  categories: Category[];
  categoriesLoading: boolean;
  fetchCategories: () => Promise<void>;
  createCategory: (name: string) => Promise<void>;
  updateCategory: (id: number, name: string) => Promise<void>;
  deleteCategory: (id: number) => Promise<void>;
};

const AdminContext = createContext<AdminContextValue | null>(null);

export function AdminProvider({ children }: { children: React.ReactNode }) {
  const { token } = useAuth();

  // Dashboard
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [statsLoading, setStatsLoading] = useState(false);

  // Users
  const [users, setUsers] = useState<User[]>([]);
  const [usersLoading, setUsersLoading] = useState(false);

  // Shops
  const [shops, setShops] = useState<Shop[]>([]);
  const [shopsLoading, setShopsLoading] = useState(false);

  // Products
  const [products, setProducts] = useState<Product[]>([]);
  const [productsLoading, setProductsLoading] = useState(false);

  // Reports
  const [reports, setReports] = useState<Report[]>([]);
  const [reportsLoading, setReportsLoading] = useState(false);

  // Orders
  const [orders, setOrders] = useState<Order[]>([]);
  const [ordersLoading, setOrdersLoading] = useState(false);

  // Categories
  const [categories, setCategories] = useState<Category[]>([]);
  const [categoriesLoading, setCategoriesLoading] = useState(false);

  const fetchStats = useCallback(async () => {
    if (!token) return;
    setStatsLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.dashboard, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setStats(data);
      }
    } catch (error) {
      console.error("Failed to fetch dashboard stats:", error);
    }
    setStatsLoading(false);
  }, [token]);

  const fetchUsers = useCallback(async () => {
    if (!token) return;
    setUsersLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.users, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setUsers(data);
      }
    } catch (error) {
      console.error("Failed to fetch users:", error);
    }
    setUsersLoading(false);
  }, [token]);

  const fetchShops = useCallback(async () => {
    if (!token) return;
    setShopsLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.shops, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setShops(data);
      }
    } catch (error) {
      console.error("Failed to fetch shops:", error);
    }
    setShopsLoading(false);
  }, [token]);

  const fetchProducts = useCallback(async () => {
    if (!token) return;
    setProductsLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.products, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setProducts(data);
      }
    } catch (error) {
      console.error("Failed to fetch products:", error);
    }
    setProductsLoading(false);
  }, [token]);

  const fetchReports = useCallback(async () => {
    if (!token) return;
    setReportsLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.reports, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setReports(data);
      }
    } catch (error) {
      console.error("Failed to fetch reports:", error);
    }
    setReportsLoading(false);
  }, [token]);

  const fetchOrders = useCallback(async () => {
    if (!token) return;
    setOrdersLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.orders, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setOrders(data);
      }
    } catch (error) {
      console.error("Failed to fetch orders:", error);
    }
    setOrdersLoading(false);
  }, [token]);

  const fetchCategories = useCallback(async () => {
    if (!token) return;
    setCategoriesLoading(true);
    try {
      const response = await fetch(API_ENDPOINTS.admin.categories, {
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        const data = await response.json();
        setCategories(data);
      }
    } catch (error) {
      console.error("Failed to fetch categories:", error);
    }
    setCategoriesLoading(false);
  }, [token]);

  const deleteUser = useCallback(async (id: number) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.user(id), {
        method: "DELETE",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        setUsers((prev) => prev.filter((user) => user.id !== id));
      }
    } catch (error) {
      console.error("Failed to delete user:", error);
    }
  }, [token]);

  const deleteShop = useCallback(async (id: number) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.shop(id), {
        method: "DELETE",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        setShops((prev) => prev.filter((shop) => shop.id !== id));
      }
    } catch (error) {
      console.error("Failed to delete shop:", error);
    }
  }, [token]);

  const deleteProduct = useCallback(async (id: number) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.product(id), {
        method: "DELETE",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        setProducts((prev) => prev.filter((product) => product.id !== id));
      }
    } catch (error) {
      console.error("Failed to delete product:", error);
    }
  }, [token]);

  const deleteReport = useCallback(async (id: number) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.report(id), {
        method: "DELETE",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        setReports((prev) => prev.filter((report) => report.id !== id));
      }
    } catch (error) {
      console.error("Failed to delete report:", error);
    }
  }, [token]);

  const updateReportStatus = useCallback(async (id: number, status: string) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.report(id), {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`,
        },
        body: JSON.stringify({ status }),
      });
      if (response.ok) {
        const updatedReport = await response.json();
        setReports((prev) => prev.map((report) => report.id === id ? updatedReport : report));
      }
    } catch (error) {
      console.error("Failed to update report status:", error);
    }
  }, [token]);

  const createCategory = useCallback(async (name: string) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.categories, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`,
        },
        body: JSON.stringify({ name }),
      });
      if (response.ok) {
        const newCategory = await response.json();
        setCategories((prev) => [...prev, newCategory]);
      }
    } catch (error) {
      console.error("Failed to create category:", error);
    }
  }, [token]);

  const updateCategory = useCallback(async (id: number, name: string) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.category(id), {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`,
        },
        body: JSON.stringify({ name }),
      });
      if (response.ok) {
        const updatedCategory = await response.json();
        setCategories((prev) => prev.map((category) => category.id === id ? updatedCategory : category));
      }
    } catch (error) {
      console.error("Failed to update category:", error);
    }
  }, [token]);

  const deleteCategory = useCallback(async (id: number) => {
    if (!token) return;
    try {
      const response = await fetch(API_ENDPOINTS.admin.category(id), {
        method: "DELETE",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
      });
      if (response.ok) {
        setCategories((prev) => prev.filter((category) => category.id !== id));
      }
    } catch (error) {
      console.error("Failed to delete category:", error);
    }
  }, [token]);

  const value = useMemo<AdminContextValue>(
    () => ({
      // Dashboard
      stats,
      statsLoading,
      fetchStats,

      // Users
      users,
      usersLoading,
      fetchUsers,
      deleteUser,

      // Shops
      shops,
      shopsLoading,
      fetchShops,
      deleteShop,

      // Products
      products,
      productsLoading,
      fetchProducts,
      deleteProduct,

      // Reports
      reports,
      reportsLoading,
      fetchReports,
      deleteReport,
      updateReportStatus,

      // Orders
      orders,
      ordersLoading,
      fetchOrders,

      // Categories
      categories,
      categoriesLoading,
      fetchCategories,
      createCategory,
      updateCategory,
      deleteCategory,
    }),
    [
      stats,
      statsLoading,
      users,
      usersLoading,
      shops,
      shopsLoading,
      products,
      productsLoading,
      reports,
      reportsLoading,
      orders,
      ordersLoading,
      categories,
      categoriesLoading,
      fetchStats,
      fetchUsers,
      deleteUser,
      fetchShops,
      deleteShop,
      fetchProducts,
      deleteProduct,
      fetchReports,
      deleteReport,
      updateReportStatus,
      fetchOrders,
      fetchCategories,
      createCategory,
      updateCategory,
      deleteCategory,
    ]
  );

  return <AdminContext.Provider value={value}>{children}</AdminContext.Provider>;
}

export function useAdmin() {
  const ctx = useContext(AdminContext);
  if (!ctx) throw new Error("useAdmin must be used within AdminProvider");
  return ctx;
}
