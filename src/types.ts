// src/types.ts
export interface Product {
  id: number;
  name: string;
  price: number;
  image?: string;          // main image for grid
  description?: string;
  stock?: number;
  images?: string[];       // full array for modal
  category_name?: string;  // optional category
}
