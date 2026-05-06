export interface User {
  id: number;
  name: string;
  email: string;
  role: 'user' | 'admin';
  avatar?: string;
  location?: string;
  bio?: string;
  joined_at?: string;
}

export interface Product {
  id: number;
  title: string;
  price: number;
  location: string;
  product_condition: 'new' | 'used';
  category_id?: number;
  category_name?: string;
  seller_id?: number;
  seller_name?: string;
  user_id?: number;
  thumbnail?: string;
  images?: ProductImage[];
  description?: string;
  created_at?: string;
}

export interface ProductImage {
  id: number;
  image_path: string;
}

export interface Order {
  id: number;
  product_id: number;
  buyer_id: number;
  seller_id: number;
  status: 'pending' | 'accepted' | 'rejected' | 'cancelled';
  created_at: string;
  title?: string;
  price?: number;
  location?: string;
  thumbnail?: string;
  seller_name?: string;
  buyer_name?: string;
  chat_id?: number;
}

export interface Chat {
  chat_id: number;
  product_id: number;
  other_user_id: number;
  other_user_name: string;
  other_user_avatar?: string;
  title?: string;
  price?: number;
  last_message?: string;
  last_message_at?: string;
  created_at?: string; 
  unread_count?: number;
}

export interface Message {
  id: number;
  chat_id?: number;
  sender_id: number;
  sender_name?: string;
  sender_avatar?: string;
  message_text: string;
  file_path?: string;
  file_type?: 'image' | 'file';
  file_name?: string;
  is_read?: number;
  created_at: string;
}

export interface Comment {
  id: number;
  comment_text: string;
  created_at: string;
  user_id: number;
  user_name: string;
  user_avatar?: string;
}

export interface Category {
  id: number;
  name: string;
  products_count?: number;
}
