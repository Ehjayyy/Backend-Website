import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "../../store/authStore";
import { useShop } from "../../store/shopStore";
import { useToast } from "../../store/toastStore";
import { useProducts, type Product } from "../../store/productStore";

export default function SellerProducts() {
  const { user, ready } = useAuth();
  const { myShop } = useShop();
  const { showToast } = useToast();
  const { listByShop, addProduct, updateProduct, deleteProduct } = useProducts();

  const [editing, setEditing] = useState<Product | null>(null);
  const [categories, setCategories] = useState<{ id: number; name: string }[]>([]);

  const [name, setName] = useState("");
  const [categoryId, setCategoryId] = useState<number>(1);
  const [price, setPrice] = useState<number>(0);
  const [stock, setStock] = useState<number>(0);
  const [description, setDescription] = useState("");

  const products = listByShop;

  useEffect(() => {
    fetchCategories();
  }, []);

  useEffect(() => {
    if (editing) {
      setName(editing.name);
      setCategoryId(editing.category_id);
      setPrice(editing.price);
      setStock(editing.stock);
      setDescription(editing.description ?? "");
    } else {
      setName("");
      setCategoryId(1);
      setPrice(0);
      setStock(0);
      setDescription("");
    }
  }, [editing]);

  const fetchCategories = async () => {
    try {
      const response = await fetch("https://marketplace-backend-t6d6.onrender.com/api/categories");
      if (response.ok) {
        const data = await response.json();
        setCategories(data);
      }
    } catch (error) {
      console.error("Failed to fetch categories:", error);
    }
  };

  if (!ready) return <Shell title="Manage Products" subtitle="Loading..." />;

  if (!user) return <Shell title="Manage Products" subtitle="Please login first." />;

  if (user.role !== "SELLER") {
    return <Shell title="Manage Products" subtitle="Only sellers can access this page." />;
  }

  if (!myShop) {
    return (
      <Shell title="Manage Products" subtitle="Create your shop first.">
        <Link className="underline underline-offset-4 text-sm" to="/seller/create-shop">
          Go to Create Shop
        </Link>
      </Shell>
    );
  }

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      if (!name.trim()) throw new Error("Name is required");
      if (price < 0) throw new Error("Price must be >= 0");
      if (stock < 0) throw new Error("Stock must be >= 0");

      if (editing) {
        await updateProduct(editing.id, {
          name: name.trim(),
          category_id: categoryId,
          price,
          stock,
          description: description.trim() || null,
        });
        showToast("✅ Product updated");
        setEditing(null);
      } else {
        await addProduct({
          category_id: categoryId,
          name: name.trim(),
          price,
          stock,
          description: description.trim() || null,
        });
        showToast("✅ Product added");
      }
    } catch (err) {
      showToast(`❌ ${(err as Error).message ?? "Failed"}`);
    }
  };

  return (
    <Shell title="Manage Products" subtitle={`Shop: ${myShop.shop_name}`}>
      <div className="mt-6 grid gap-6 lg:grid-cols-2">
        {/* Form */}
        <div className="rounded-2xl border bg-white p-5">
          <div className="font-semibold">{editing ? "Edit Product" : "Add Product"}</div>

          <form className="mt-4 grid gap-3" onSubmit={submit}>
            <input
              className="rounded-xl border px-3 py-2"
              placeholder="Product name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
            />

            <select
              className="rounded-xl border px-3 py-2"
              value={categoryId}
              onChange={(e) => setCategoryId(Number(e.target.value))}
            >
              {categories.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>

            <input
              className="rounded-xl border px-3 py-2"
              type="number"
              step="0.01"
              min={0}
              placeholder="Price"
              value={price}
              onChange={(e) => setPrice(Number(e.target.value))}
              required
            />

            <input
              className="rounded-xl border px-3 py-2"
              type="number"
              min={0}
              placeholder="Stock"
              value={stock}
              onChange={(e) => setStock(Number(e.target.value))}
              required
            />

            <textarea
              className="rounded-xl border px-3 py-2 min-h-[110px]"
              placeholder="Description (optional)"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            />

            <div className="flex gap-3">
              <button className="rounded-xl border px-3 py-2 hover:bg-gray-50">
                {editing ? "Save changes" : "Add product"}
              </button>

              {editing && (
                <button
                  type="button"
                  className="rounded-xl border px-3 py-2 hover:bg-gray-50"
                  onClick={() => setEditing(null)}
                >
                  Cancel
                </button>
              )}
            </div>
          </form>
        </div>

        {/* List */}
        <div className="rounded-2xl border bg-white">
          <div className="border-b p-4 font-semibold">Your Products</div>

          {products.length === 0 ? (
            <div className="p-5 text-sm text-gray-600">No products yet.</div>
          ) : (
            <div className="divide-y">
              {products.map((p) => (
                <div key={p.id} className="p-4 flex items-start justify-between gap-4">
                  <div>
                    <div className="font-semibold">{p.name}</div>
                    <div className="text-sm text-gray-600">
                      ₱{Number(p.price).toFixed(2)} • Stock: {p.stock}
                    </div>
                    <div className="text-xs text-gray-500">
                      Category: {categories.find((c) => c.id === p.category_id)?.name ?? "Unknown"}
                    </div>
                  </div>

                  <div className="flex gap-2">
                    <button
                      className="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50"
                      onClick={() => setEditing(p)}
                    >
                      Edit
                    </button>
                    <button
                      className="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50"
                      onClick={async () => {
                        try {
                          await deleteProduct(p.id);
                          showToast(`🗑 Deleted ${p.name}`);
                          if (editing?.id === p.id) setEditing(null);
                        } catch (err) {
                          showToast(`❌ ${(err as Error).message ?? "Failed to delete"}`);
                        }
                      }}
                    >
                      Delete
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}

          <div className="p-4">
            <Link className="underline underline-offset-4 text-sm" to="/profile">
              Back to Profile
            </Link>
          </div>
        </div>
      </div>
    </Shell>
  );
}

function Shell({
  title,
  subtitle,
  children,
}: {
  title: string;
  subtitle: string;
  children?: React.ReactNode;
}) {
  return (
    <div className="mx-auto max-w-6xl px-4 py-8">
      <h1 className="text-2xl font-semibold">{title}</h1>
      <p className="mt-1 text-sm text-gray-600">{subtitle}</p>
      {children}
    </div>
  );
}