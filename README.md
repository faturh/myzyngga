# Zyngga - Maternal Care Platform

Homepage built from Figma design using Laravel and Tailwind CSS.

## Design Tokens (from Figma)

**Colors:**
- Primary R500: `#9C3650`
- Primary R300: `#FF5983`
- Primary R100: `#FF9FB7`
- Monochrome Black: `#333333`
- Monochrome Light: `#F0F0F0`

**Typography:**
- Font: Outfit (Google Fonts)
- XSmall: 14px, Medium (500)
- Small: 16px, Medium (500)
- Medium: 20px, Medium (500)
- Semi-Large: 25px, Medium (500)

## Setup

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Install Node dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Build assets:**
   ```bash
   npm run build
   ```
   Or for development with hot reload:
   ```bash
   npm run dev
   ```

5. **Run the server:**
   ```bash
   php artisan serve
   ```

Visit http://localhost:8000 to view the homepage.
