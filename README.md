# Zyngga Laundry – Modern Laundry Platform

A premium laundry management platform built with Laravel, Tailwind CSS, and Alpine.js, following a pixel-perfect design system from Figma.

## 🎨 Design System

### Colors
- **Primary Blue**: `#1660C1` (Main), `#0E3B77` (Dark), `#E8EFF9` (Light)
- **Accent Yellow**: `#F7931E` (Main), `#FEF4E9` (Light)
- **Neutral**: `#0F0F0F` (Dark), `#808080` (Gray), `#F4F4F4` (Light)

### Typography
- **Font Family**: `DM Sans` (Google Fonts)
- **Sizes**:
  - `XS`: 12px
  - `SM`: 14px
  - `Base`: 16px
  - `LG`: 18px
  - `XL`: 20px

## 🧩 Reusable Blade Components

The project uses a custom set of Atomic Design components for consistency:

- **`<x-zyngga-button>`**: Versatile button supporting variants (`primary`, `secondary`, `tertiary`, `neutral`), sizes (`s`, `m`, `l`), and icons.
- **`<x-zyngga-text>`**: Standardized typography component with pre-defined styles.
- **`<x-zyngga-status>`**: Dynamic status badges for orders and payments.
- **`<x-zyngga-service-icon>`**: Standardized iconography for laundry services (Regular, Quick, Express, Kilat, Satuan).
- **`<x-zyngga-footer>`**: Modern rounded footer with brand information.

## 🚀 Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/username/Zyngga.git
   cd myzyngga
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies:**
   ```bash
   npm install
   ```

4. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run Database Migrations (if applicable):**
   ```bash
   php artisan migrate
   ```

6. **Build assets:**
   ```bash
   npm run dev
   ```

7. **Run the server:**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to view the application.

## 📱 Features
- Responsive Mobile-First Design
- Dynamic Order Tracking Progres
- Premium UI/UX with Glassmorphism and Smooth Animations
- Localized Pickup & Delivery Scheduling
