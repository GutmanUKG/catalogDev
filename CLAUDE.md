# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

B2B e-commerce cabinet for ak-cent.kz built on **Bitrix CMS** (PHP backend) with **React 17** interactive components. The site is a wholesale B2B portal with multi-price support, regional pricing, Excel import/export, order templates, and draft orders. Currency is KZT. Interface language is Russian.

## Build Commands

React components each have their own independent webpack builds. Navigate to the component's template directory before running:

```bash
# Development mode with file watching
npm start     # runs: npx webpack --mode development --watch

# Production build
npm build     # runs: npx webpack --mode production
```

Webpack-enabled component locations:
- `local/components/sotbit/basket.upselling/templates/b2bcabinet/`
- `local/components/sotbit/multibasket.multibasket/src/`
- `local/templates/b2bcabinet_v2.0/components/sotbit/basket.upselling/b2bcabinet/`
- `local/templates/b2bcabinet_v2.0/components/sotbit/multibasket.multibasket/b2bcabinet/src/`
- `local/templates/.default/components/sotbit/basket.upselling/b2bcabinet_slider/`

Each builds `script.js` in its own directory from `src/index.jsx` entry point.

## Linting

ESLint is configured per-component (`.eslintrc.json`): `eslint:recommended` + `plugin:react/recommended`, parser `babel-eslint`, ES2021, JSX enabled. `react/prop-types` rule is disabled. ESLint runs automatically during webpack builds via `eslint-webpack-plugin`.

## Architecture

### Directory Structure

- **`local/php_interface/`** — Application bootstrap and configuration
  - `init.php` — Main entry point: module loading, namespace registration, event handlers, custom pricing logic
  - `handlers.php` — Additional Bitrix event handlers (user registration emails)
  - `constants.php` — Global constants (e.g., `IBLOCK_CATALOG_ID = 7`)
  - `include/sale_discount/` — Custom sale discount conditions

- **`local/classes/onelab/`** — Custom PHP business logic (namespace `Onelab\`)
  - `catalog/product/` — Product and partner logic
  - `catalog_sale/` — Catalog sales: basket, import/export, Excel operations, events, AJAX handlers
  - `sale/order/` — Order processing
  - `brands/` — Brand management
  - `main/` — Search functionality

- **`local/components/`** — Bitrix components (modular UI+logic blocks)
  - `sotbit/` — Main B2B Cabinet components (basket, catalog filter, drafts, Excel, orders, search, etc.)
  - `onelab/` — Custom components (basket, catalog filter, pre-order, order, search, user table)
  - `bitrix/` — Overridden standard Bitrix components

- **`local/templates/b2bcabinet_v2.0/`** — Primary site template (v2.0)
  - `components/` — Template-specific component overrides (can override component templates)
  - `assets/` — CSS, JS, icons

### Bitrix Component Conventions

Each component follows this structure:
- `class.php` — Component logic (PHP class extending `CBitrixComponent`)
- `.description.php` — Component metadata
- `.parameters.php` — Parameter schema
- `templates/` — Template variations (each can have its own webpack build)
- `lang/` — Localization files (Russian)

### React Components (Frontend)

The `basket.upselling` component is the primary React app. Architecture:
- **State management**: Custom Context API + `useThunkReducer` hook (Redux-like thunk pattern)
- **Store**: `store/state.jsx` (context provider), `store/actions.js` (action creators), `store/reduser.js` (reducer)
- **Custom hooks** (`src/castomHooks/`): `useThunkReducer`, `useDebounce`, `useQuatityViewer`, `useSyncWithBasket`
- **API layer** (`src/bitrix_api.js`): Uses `BX.ajax.runComponentAction()` for server communication
- **BX global**: Declared as webpack external — Bitrix JS framework is always available on page

API call pattern:
```javascript
BX.ajax.runComponentAction('sotbit:basket.upselling', 'actionName', {
    mode: 'class',
    data: { /* params */ }
})
```

### Key Business Logic

- **Pricing**: Two-tier system — offline price (type ID 12) and dealer price (type ID 7). User field `UF_APPLY_PRICE` determines which price applies. Handled via `OnGetOptimalPrice` event handler in `init.php`.
- **Iblock IDs**: Catalog = 7 (`IBLOCK_CATALOG_ID`/`CATALOG_IBLOCK_ID`), Brands = 20, Managers = 16
- **Brand auto-sync**: `OnAfterIBlockElementAdd` handler automatically creates brand entries in iblock 20 from product attribute `BREND_ATTR_S`
- **Order emails**: Custom `OnOrderNewSendEmail` handler enriches order notification with company name, location, delivery, weight, coupons
- **Store quantity agent**: `AgentUpdateStoreQuantity()` syncs store 51 quantities to product property `STORE_51`
- **Custom facet search**: `MyFacet` class extends `Bitrix\Iblock\PropertyIndex\Facet` for modified smart filter behavior with `$searchFilter` global

### Namespace Registration

The `Onelab\` namespace is registered to `local/classes/onelab/` via Bitrix autoloader in `init.php`.

## Codebase Quirks

Known typos in filenames/identifiers that should be preserved for consistency (changing them would break imports):
- `reduser.js` (not "reducer")
- `castomHooks/` (not "customHooks")
- `useQuatityViewer` (not "useQuantityViewer")
- `tooltibp.jsx` (not "tooltip")
- `getProdeucts` in API (not "getProducts")
