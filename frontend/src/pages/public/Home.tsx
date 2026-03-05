import { useState, useEffect } from "react";
import ProductGrid from "../../components/products/ProductGrid";
import type { Category, Product } from "../../types/models";
import { API_ENDPOINTS } from "../../config/api";
import { apiGet } from "../../lib/api";

export default function Home() {
  const [q, setQ] = useState("");
  const [catId, setCatId] = useState<number | "all">("all");
  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchProducts();
    fetchCategories();
  }, [catId, q]);

  const fetchProducts = async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams();
      if (catId !== "all") params.append("category_id", catId.toString());
      if (q) params.append("search", q);

      const response = await apiGet(`${API_ENDPOINTS.products.list}?${params.toString()}`);
      if (response.success) {
        setProducts(response.data);
      }
    } catch (error) {
      console.error("Failed to fetch products:", error);
    } finally {
      setLoading(false);
    }
  };

  const fetchCategories = async () => {
    try {
      const response = await apiGet(API_ENDPOINTS.categories);
      if (response.success) {
        setCategories(response.data);
      }
    } catch (error) {
      console.error("Failed to fetch categories:", error);
    }
  };

  return (
    <div className="mx-auto max-w-6xl px-4 py-8">
      {/* Hero Section */}
      <div className="mb-12 text-center">
        <div className="inline-block px-6 py-2 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4 shadow-sm">
          Discover Amazing Products
        </div>
        <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
          Welcome to <span className="bg-gradient-to-r from-primary-600 to-accent-500 bg-clip-text text-transparent">MarketPlace</span>
        </h1>
        <p className="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
          Browse thousands of products from trusted sellers. Find everything you need in one place.
        </p>
      </div>

      {/* Search and Filter Section */}
      <div className="bg-white rounded-3xl shadow-soft p-6 mb-10 border border-gray-100">
        <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div className="text-left">
            <h2 className="text-xl font-bold text-gray-900">Browse Products</h2>
            <p className="mt-1 text-sm text-gray-600">
              Find exactly what you're looking for
            </p>
          </div>

          <div className="flex flex-col gap-3 md:flex-row md:items-center">
            <div className="relative">
              <svg className="w-5 h-5 absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input
                className="w-full md:w-96 pl-12 pr-4 py-3 rounded-2xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all duration-300"
                placeholder="Search products..."
                value={q}
                onChange={(e) => setQ(e.target.value)}
              />
            </div>

            <select
              className="px-4 py-3 rounded-2xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all duration-300"
              value={catId}
              onChange={(e) =>
                setCatId(e.target.value === "all" ? "all" : Number(e.target.value))
              }
            >
              <option value="all">All categories</option>
              {categories.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>
          </div>
        </div>
      </div>

      {/* Products Section */}
      <div className="mb-12">
        {loading ? (
          <div className="bg-white rounded-3xl shadow-soft p-12 text-center border border-gray-100">
            <div className="animate-spin rounded-full h-16 w-16 border-b-4 border-primary-600 mx-auto mb-4"></div>
            <p className="text-lg text-gray-600">Loading amazing products...</p>
          </div>
        ) : products.length === 0 ? (
          <div className="bg-white rounded-3xl shadow-soft p-12 text-center border border-gray-100">
            <svg className="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 className="text-xl font-bold text-gray-900 mb-2">No products found</h3>
            <p className="text-gray-600">Try searching for something else or browse all categories</p>
          </div>
        ) : (
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <h2 className="text-2xl font-bold text-gray-900">
                {catId === "all" ? "All Products" : `Category: ${categories.find(c => c.id === catId)?.name}`}
              </h2>
              <span className="text-sm font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                {products.length} products found
              </span>
            </div>
            <ProductGrid products={products} />
          </div>
        )}
      </div>

      {/* Features Section */}
      <div className="grid md:grid-cols-3 gap-6">
        <div className="bg-gradient-to-br from-primary-50 to-blue-50 p-6 rounded-2xl border border-primary-100">
          <div className="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
            <svg className="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 className="font-bold text-lg text-gray-900 mb-2">Secure Payments</h3>
          <p className="text-sm text-gray-600">Shop with confidence with our secure payment system</p>
        </div>

        <div className="bg-gradient-to-br from-accent-50 to-purple-50 p-6 rounded-2xl border border-accent-100">
          <div className="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mb-4">
            <svg className="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 className="font-bold text-lg text-gray-900 mb-2">Fast Delivery</h3>
          <p className="text-sm text-gray-600">Get your products delivered quickly to your doorstep</p>
        </div>

        <div className="bg-gradient-to-br from-success-50 to-green-50 p-6 rounded-2xl border border-success-100">
          <div className="w-12 h-12 bg-success-100 rounded-xl flex items-center justify-center mb-4">
            <svg className="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 className="font-bold text-lg text-gray-900 mb-2">Quality Products</h3>
          <p className="text-sm text-gray-600">Only the best products from verified sellers</p>
        </div>
      </div>
    </div>
  );
}