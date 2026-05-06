import { Routes } from '@angular/router';
import { authGuard, adminGuard } from './core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () => import('./pages/home/home.component').then(m => m.HomeComponent),
  },
  {
    path: 'product/:id',
    loadComponent: () => import('./pages/product-details/product-details.component').then(m => m.ProductDetailsComponent),
  },
  {
    path: 'seller/:id',
    loadComponent: () => import('./pages/seller-profile/seller-profile.component').then(m => m.SellerProfileComponent),
  },
  {
    path: 'login',
    loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent),
  },
  {
    path: 'register',
    loadComponent: () => import('./pages/register/register.component').then(m => m.RegisterComponent),
  },


  {
    path: 'create',
    loadComponent: () => import('./pages/create-product/create-product.component').then(m => m.CreateProductComponent),
    canActivate: [authGuard],
  },
  {
    path: 'edit/:id',
    loadComponent: () => import('./pages/edit-product/edit-product.component').then(m => m.EditProductComponent),
    canActivate: [authGuard],
  },
  {
    path: 'profile',
    loadComponent: () => import('./pages/profile/profile.component').then(m => m.ProfileComponent),
    canActivate: [authGuard],
  },
  {
    path: 'favorites',
    loadComponent: () => import('./pages/favorites/favorites.component').then(m => m.FavoritesComponent),
    canActivate: [authGuard],
  },
  {
    path: 'my-products',
    loadComponent: () => import('./pages/my-products/my-products.component').then(m => m.MyProductsComponent),
    canActivate: [authGuard],
  },
  {
    path: 'orders-buyer',
    loadComponent: () => import('./pages/orders-buyer/orders-buyer.component').then(m => m.OrdersBuyerComponent),
    canActivate: [authGuard],
  },
  {
    path: 'orders-seller',
    loadComponent: () => import('./pages/orders-seller/orders-seller.component').then(m => m.OrdersSellerComponent),
    canActivate: [authGuard],
  },
  {
    path: 'chats',
    loadComponent: () => import('./pages/chats/chats.component').then(m => m.ChatsComponent),
    canActivate: [authGuard],
  },
  {
    path: 'chat/:id',
    loadComponent: () => import('./pages/chat-room/chat-room.component').then(m => m.ChatRoomComponent),
    canActivate: [authGuard],
  },


  {
    path: 'admin',
    loadComponent: () => import('./pages/admin/admin.component').then(m => m.AdminComponent),
    canActivate: [adminGuard],
  },

  { path: '**', redirectTo: '' },
];
