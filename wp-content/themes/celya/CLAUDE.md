# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Celya** is a custom WordPress WooCommerce theme for an artisanal French food products e-commerce site. It is built with Tailwind CSS 3.x and uses extensive WooCommerce template overrides.

The working directory for this theme is `wp-content/themes/celya/`. All commands below should be run from that directory.

## Build Commands

```bash
# Development — watch and recompile Tailwind CSS on file changes
npm run dev

# Production — minified CSS output
npm run build
```

**CSS pipeline — multi-entry, loaded per page:**

- **Global:** `assets/css/input.css` → `output.css`. Carries the Tailwind layers (`@tailwind base/components/utilities`) plus the partials shared across the whole site (fonts, gutenberg, navigation, forms, product-card, message, and `_celya-buttons.css`). Enqueued on every page in `functions.php` under the handle `celya-tailwind`.
- **Page-specific:** five separate entries compiled to their own output, each enqueued only on the relevant page by `inc/woocommerce-setup.php` (`celya_enqueue_woocommerce_styles`), with a dependency on `celya-tailwind`:
  - `input-product.css` → `output-product.css` — `is_product()`
  - `input-category.css` → `output-category.css` — `is_shop() || is_product_taxonomy()`
  - `input-cart.css` → `output-cart.css` — `is_cart()`
  - `input-checkout.css` → `output-checkout.css` — `is_checkout()`
  - `input-account.css` → `output-account.css` — `is_account_page()`

Page-specific entries contain **no** `@tailwind` directives — the utility classes they reference are already emitted in the global `output.css` (Tailwind scans `content` globally). They only carry the resolved `@apply` component CSS, so there is no utility duplication. Custom component classes used via `@apply` (e.g. `btn-celya`) live in the shared `_celya-buttons.css`, imported by both the global entry and any page entry that needs them (currently `input-account.css`).

`npm run build` compiles all six entries; `npm run dev` runs all six watchers in parallel.

**JS:** No bundler — vanilla JS files are enqueued directly by `functions.php`.

## Architecture

### PHP Structure

- **`functions.php`** — Bootstraps the theme: loads the class autoloader, includes all `/inc/` files, registers nav menus, image sizes, Customizer options, and enqueues assets.
- **`inc/`** — All feature modules live here:
  - `woocommerce-setup.php` — Grid layout (3 cols), products per page, custom add-to-cart text/styles, category page hooks.
  - `woocommerce-custom-fields.php` — Custom product spec system: ingredients, nutritional tables, allergens, conservation, tasting notes. Data is stored as JSON in product meta with a legacy plain-text fallback.
  - `woocommerce-setup-single-product.php` — Related products count, weight formatting in variation JS data.
  - `woocommerce-setup-single-product-tabs.php` — Custom WooCommerce product tabs (description, ingredients, nutrition, conservation, tasting, allergens).
  - `woocommerce-setup-breadcrumb.php` — Breadcrumb customization.
  - `class-loader.php` — PSR-4 style autoloader for any classes in `/inc/classes/`.

### WooCommerce Template Overrides

Custom templates in `woocommerce/` override WooCommerce defaults. Key overrides:

- `archive-product.php` / `content-product.php` — Shop/category listing layout and product cards.
- `content-single-product.php` — Full single product page layout.
- `single-product/` — Individual product component templates (title, price, tabs, add-to-cart variants).

### JavaScript Modules

- `assets/js/app.js` — Mobile menu toggle (burger animation). Loaded on all pages.
- `assets/js/modules/product-page.js` — Product gallery thumbnail switching, zoom, and review form interactions. Conditionally enqueued only on single product pages.

### Design System

Colors and typography are defined in **both** `tailwind.config.js` and `theme.json` (Gutenberg v2). They must stay in sync for the block editor to match the frontend. Primary: deep artisanal brown `#59332A`; secondary: beige `#F2D0A7`.

Fonts (Montserrat + Arima) are self-hosted as `.ttf` files and loaded via `@font-face` in `assets/css/input.css`. Both fonts are also registered in `theme.json` for Gutenberg.

### Asset Cache-Busting

All `wp_enqueue_style` / `wp_enqueue_script` calls in `functions.php` use `filemtime()` as the version parameter, so browsers automatically fetch updated files on change.

### Customizer Settings

Contact info (email, phone, address, social links) is managed through the WordPress Theme Customizer (`customize_register` hook in `functions.php`), not hardcoded in templates.
