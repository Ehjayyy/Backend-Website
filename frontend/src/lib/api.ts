const API_BASE = '/api';

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

export const apiFetch = async (
  path: string,
  options: RequestInit = {}
): Promise<ApiResponse> => {
  // Build full URL
  const url = `${API_BASE}${path}`;

  // Get token from localStorage
  const token = localStorage.getItem('token');

  // Default options
  const defaultOptions: RequestInit = {
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    ...options,
  };

  // Handle body for GET requests
  if (defaultOptions.method === 'GET' && defaultOptions.body) {
    console.warn('GET request should not have body');
  }

  try {
    const response = await fetch(url, defaultOptions);

    let data: any;
    try {
      data = await response.json();
    } catch (parseError) {
      throw new Error(`Response parsing failed: ${parseError}`);
    }

    if (!response.ok) {
      throw new Error(data?.error || data?.message || `HTTP ${response.status}`);
    }

    return data;
  } catch (error) {
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Unknown error',
    };
  }
};

// Helper functions for common methods
export const apiGet = (path: string, options?: RequestInit) =>
  apiFetch(path, { method: 'GET', ...options });

export const apiPost = (path: string, body?: any, options?: RequestInit) =>
  apiFetch(path, {
    method: 'POST',
    body: body ? JSON.stringify(body) : undefined,
    ...options,
  });

export const apiPut = (path: string, body?: any, options?: RequestInit) =>
  apiFetch(path, {
    method: 'PUT',
    body: body ? JSON.stringify(body) : undefined,
    ...options,
  });

export const apiPatch = (path: string, body?: any, options?: RequestInit) =>
  apiFetch(path, {
    method: 'PATCH',
    body: body ? JSON.stringify(body) : undefined,
    ...options,
  });

export const apiDelete = (path: string, options?: RequestInit) =>
  apiFetch(path, { method: 'DELETE', ...options });