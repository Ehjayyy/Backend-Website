/* eslint-disable react-refresh/only-export-components */
import React, { createContext, useCallback, useContext, useMemo, useState } from "react";
import { API_ENDPOINTS } from "../config/api";
import { apiPost } from "../lib/api";

export type Role = "BUYER" | "SELLER" | "ADMIN";

export type User = {
  id: number;
  name: string;
  email: string;
  role: Role;
  created_at: string;
};

export type AuthUser = Omit<User, "password">;

type AuthContextValue = {
  user: AuthUser | null;
  token: string | null;
  ready: boolean;
  isLoggedIn: boolean;

  register: (payload: { name: string; email: string; password: string; role: Role }) => Promise<void>;
  login: (payload: { email: string; password: string }) => Promise<void>;
  logout: () => void;
};

const AuthContext = createContext<AuthContextValue | null>(null);

const TOKEN_KEY = "mp_token";
const USER_KEY = "mp_user";

function safeJsonParse<T>(raw: string | null): T | null {
  if (!raw) return null;
  try {
    return JSON.parse(raw) as T;
  } catch {
    return null;
  }
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  // Simple state initialization
  const [token, setToken] = useState<string | null>(() => localStorage.getItem(TOKEN_KEY));
  const [user, setUser] = useState<AuthUser | null>(() => safeJsonParse<AuthUser>(localStorage.getItem(USER_KEY)));
  const ready = true; // Always ready

  const register = useCallback<AuthContextValue["register"]>(async ({ name, email, password, role }) => {
    const response = await apiPost(API_ENDPOINTS.auth.register, {
      name,
      email,
      password,
      role,
    });

    if (!response.success) {
      throw new Error(response.error || "Registration failed");
    }

    const { user, token } = response.data as { user: AuthUser; token: string };
    setUser(user);
    setToken(token);
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
  }, []);

  const login = useCallback<AuthContextValue["login"]>(async ({ email, password }) => {
    const response = await apiPost(API_ENDPOINTS.auth.login, {
      email,
      password,
    });

    if (!response.success) {
      throw new Error(response.error || "Login failed");
    }

    const { user, token } = response.data as { user: AuthUser; token: string };
    setUser(user);
    setToken(token);
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
  }, []);

  const logout = useCallback(() => {
    setUser(null);
    setToken(null);
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      ready,
      isLoggedIn: !!token && !!user,
      register,
      login,
      logout,
    }),
    [user, token, ready, register, login, logout]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used within AuthProvider");
  return ctx;
}