/* eslint-disable react-refresh/only-export-components */
import React, { createContext, useContext, useMemo, useState, useEffect } from "react";
import { useAuth } from "./authStore";
import { API_ENDPOINTS } from "../config/api";
import { apiGet, apiPost } from "../lib/api";

export type OrderStatus = "PENDING" | "PAID" | "SHIPPED" | "COMPLETED" | "CANCELLED";

export type Order = {
  id: number;
  user_id: number; // buyer
  order_date: string;
  status: OrderStatus;
  total_amount: number;
  items?: OrderItem[];
};

export type OrderItem = {
  id: number;
  order_id: number;
  product_id: number;
  quantity: number;
  price: number; // snapshot price
  product?: {
    id: number;
    name: string;
  };
};

type OrderContextValue = {
  listMyOrders: Order[];
  listItemsByOrder: (orderId: number) => OrderItem[];
  createOrder: (payload: {
    total_amount: number;
    items: { product_id: number; quantity: number; price: number }[];
  }) => Promise<Order>;
  loading: boolean;
  reload: () => Promise<void>;
};

const OrderContext = createContext<OrderContextValue | null>(null);

export function OrderProvider({ children }: { children: React.ReactNode }) {
  const { user, token, ready } = useAuth();
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(false);

  const reload = async () => {
    if (!ready || !user || !token) {
      setOrders([]);
      return;
    }

    setLoading(true);
    try {
      const response = await apiGet(API_ENDPOINTS.orders.list);

      if (response.success) {
        setOrders(response.data);
      } else {
        setOrders([]);
      }
    } catch (error) {
      console.error("Failed to load orders:", error);
      setOrders([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    reload();
  }, [user]);

  const listMyOrders = orders;

  const listItemsByOrder = (orderId: number) => {
    const order = orders.find((o) => o.id === orderId);
    if (order && order.items) {
      return order.items;
    }
    return [];
  };

  const createOrder: OrderContextValue["createOrder"] = async ({ total_amount, items }) => {
    if (!ready || !user || !token) throw new Error("You must be logged in.");
    const response = await apiPost(API_ENDPOINTS.orders.create, {
      total_amount,
      items,
    });

    if (!response.success) {
      throw new Error(response.error || "Failed to create order");
    }

    const data = response.data;
    await reload();
    return data;
  };

  const value = useMemo(
    () => ({
      listMyOrders,
      listItemsByOrder,
      createOrder,
      loading,
      reload,
    }),
    [orders, loading]
  );

  return <OrderContext.Provider value={value}>{children}</OrderContext.Provider>;
}

export function useOrders() {
  const ctx = useContext(OrderContext);
  if (!ctx) throw new Error("useOrders must be used within OrderProvider");
  return ctx;
}