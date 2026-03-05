/* eslint-disable react-refresh/only-export-components */
import React, { createContext, useContext, useMemo } from "react";
import { useAuth } from "./authStore";
import { API_ENDPOINTS } from "../config/api";
import { apiPost } from "../lib/api";

export type PaymentStatus = "PENDING" | "PAID" | "FAILED";

export type Payment = {
  id: number;
  order_id: number;
  payment_method: string;
  payment_status: PaymentStatus;
  payment_date: string;
};

type PaymentContextValue = {
  createPayment: (payload: Omit<Payment, "id" | "payment_date">) => Promise<Payment>;
  getPaymentByOrderId: (orderId: number) => Promise<Payment | null>;
};

const PaymentContext = createContext<PaymentContextValue | null>(null);

export function PaymentProvider({ children }: { children: React.ReactNode }) {
  const { user, token, ready } = useAuth();

  const createPayment: PaymentContextValue["createPayment"] = async ({
    order_id,
    payment_method,
    payment_status,
  }) => {
    if (!ready || !user || !token) throw new Error("You must be logged in.");
    const response = await apiPost(API_ENDPOINTS.payments.create, {
      order_id,
      payment_method,
      payment_status,
    });

    if (!response.success) {
      throw new Error(response.error || "Failed to create payment");
    }

    return response.data;
  };

  const getPaymentByOrderId: PaymentContextValue["getPaymentByOrderId"] = async (orderId: number) => {
    if (!ready || !user || !token) return null;
    const response = await fetch(API_ENDPOINTS.payments.order(orderId), {
      headers: {
        "Authorization": `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      return null;
    }

    return await response.json();
  };

  const value = useMemo(
    () => ({
      createPayment,
      getPaymentByOrderId,
    }),
    [createPayment, getPaymentByOrderId]
  );

  return <PaymentContext.Provider value={value}>{children}</PaymentContext.Provider>;
}

export function usePayments() {
  const ctx = useContext(PaymentContext);
  if (!ctx) throw new Error("usePayments must be used within PaymentProvider");
  return ctx;
}
