# HudumaLynk — Implementation Plan

A digital marketplace platform connecting service providers and product vendors with customers in Nairobi. Built on Yii2 (PHP), MySQL, and Bootstrap 5 for a clean, working, maintainable system.

---

## User Review Required

> [!IMPORTANT]
> **Yii2 Advanced Template** is recommended over Basic. It cleanly separates the customer-facing app (`frontend/`) from the admin/provider dashboard (`backend/`), sharing models in `common/`. This matches your three user types perfectly.

> [!IMPORTANT]
> **M-Pesa (Daraja API)** is the primary payment method recommended for the Nairobi market. Card payments via a gateway like Flutterwave or Paystack (both support Kenya) will be the secondary method. Please confirm if you have (or plan to get) M-Pesa Daraja API credentials and a preferred card gateway.

> [!WARNING]
> **Provider Dashboard** — The blueprint lists providers as a distinct user type. In Phase 1, the provider dashboard will be a **section inside the backend app** (protected by role), not a separate app. This keeps complexity low and delivery fast. We can spin it off later.

> [!NOTE]
> **Delivery Module** — Per your blueprint, this is a future phase. I'll include the DB table and basic status tracking but will NOT build a full delivery management UI in Phase 1.

---

## Proposed Technology Stack

| Layer | Choice | Why |
|---|---|---|
| Backend Framework | **Yii2 Advanced** | Role-based separation, built-in RBAC, ActiveRecord ORM |
| Database | **MySQL 8** | As specified |
| Frontend UI | **Bootstrap 5 + Vanilla JS** | Clean, fast, no build tools needed |
| Fonts | Google Fonts — **Inter** | Modern, highly readable |
| Payment (Primary) | **M-Pesa Daraja API (STK Push)** | Dominant payment method in Kenya |
| Payment (Secondary) | **Flutterwave** | Cards, bank transfers, multi-currency |
| SMS Notifications | **Africa's Talking SMS** | Kenya-native, affordable, reliable |
| Email | **Yii2 Mailer (SMTP / SendGrid)** | Built-in, straightforward |
| File Storage | **Local (Phase 1)** → S3 (Phase 2) | Simple start |
| Hosting | **VPS (Ubuntu + Nginx + PHP 8.1)** | Cost-effective for Phase 1 |

---

## Proposed Changes

### Phase 1 — Foundation & Core MVP

---

### 1. Project Scaffold

#### [NEW] Yii2 Advanced Application (`c:\PROJECTS\lynk\`)

Initialize using Composer. This gives us:

```
lynk/
├── frontend/          ← Customer-facing site
├── backend/           ← Admin + Provider management
├── common/            ← Shared models, components, helpers
├── console/           ← DB migrations, scheduled tasks
└── environments/      ← dev/prod config switching
```

---

### 2. Database Layer

#### [NEW] `console/migrations/` — SQL Migrations

All tables from the blueprint, plus a few additions:

| Table | Purpose |
|---|---|
| `users` | All user accounts (customer, provider, admin) |
| `providers` | Business profiles linked to user |
| `subscription_plans` | Plan definitions (Basic/Pro/Premium) |
| `subscriptions` | Active provider subscriptions |
| `categories` | Service/product categories |
| `listings` | **Unified table for products + services** (see note below) |
| `listing_images` | Multiple images per listing |
| `orders` | Orders with full/partial/delivery payment types |
| `order_items` | Line items in each order |
| `payments` | Payment transactions (deposit, balance, full, COD) |
| `deliveries` | Delivery tracking (Phase 1 basic) |
| `reviews` | Customer ratings and comments |
| `notifications` | In-app notification log |
| `commissions` | Commission earned per order |
| `featured_listings` | Boost/promote records |

> [!NOTE]
> **Unified `listings` table** — Instead of separate `products` and `services` tables, a single `listings` table with a `type ENUM('product','service')` column avoids code duplication across queries, controllers, and views. The only difference is that services have `availability` and products have `stock_quantity`. Both columns exist in the table, used based on type.

**Enhanced `listings` schema suggestion:**
```sql
CREATE TABLE listings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    provider_id BIGINT NOT NULL,
    category_id INT NOT NULL,
    type ENUM('product','service') NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT NULL,       -- products only
    availability VARCHAR(100) DEFAULT NULL, -- services only (e.g. "Mon-Fri 8am-5pm")
    location VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active','inactive','draft') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

---

### 3. Authentication & RBAC Module

#### [MODIFY] `common/config/` & `common/models/`

- Yii2 built-in `yii\web\IdentityInterface` on `User` model
- **Three roles**: `admin`, `provider`, `customer`
- Role assigned at registration
- Login via email or phone number
- Password reset via email OTP
- Phone verification via Africa's Talking SMS

**Auth flows:**
- `/site/login` — unified login for all roles, redirect to role-specific dashboard
- `/site/register` — customer registration
- `/provider/register` — provider registration with document upload
- Admins created via console command (no public registration)

---

### 4. Customer-Facing Frontend (`frontend/`)

#### Pages to Build:

| Page | Route | Description |
|---|---|---|
| Home | `/` | Hero, categories, featured listings, how it works |
| Browse | `/browse` | Search + filter listings (type, category, location, price, rating) |
| Listing Detail | `/listing/view/{id}` | Full listing with provider info, reviews, order button |
| Provider Profile | `/provider/profile/{id}` | All listings + ratings for a provider |
| Order Flow | `/order/create` | Cart → payment type selection → payment → confirmation |
| My Orders | `/order/index` | Customer order history with status tracking |
| My Account | `/account/index` | Profile management |
| Register | `/site/register` | Customer signup |
| Login | `/site/login` | Unified login |

---

### 5. Provider Portal (`backend/` — Provider Section)

#### Pages to Build:

| Page | Route | Description |
|---|---|---|
| Provider Dashboard | `/provider/dashboard` | Overview: orders, earnings, subscription status |
| Manage Listings | `/provider/listing/index` | CRUD for products and services |
| Manage Orders | `/provider/order/index` | Incoming orders, accept/reject, update status |
| Subscription | `/provider/subscription/index` | Current plan, upgrade, billing history |
| Earnings | `/provider/earnings/index` | Transaction log, commission breakdown |
| Profile | `/provider/profile/update` | Edit business info |

---

### 6. Admin Dashboard (`backend/` — Admin Section)

#### Pages to Build:

| Page | Route | Description |
|---|---|---|
| Admin Dashboard | `/admin/dashboard` | Platform-wide stats: users, orders, revenue |
| Manage Users | `/admin/user/index` | View/suspend/activate users |
| Manage Providers | `/admin/provider/index` | Approve/suspend providers, view docs |
| Manage Plans | `/admin/plan/index` | CRUD subscription plans |
| Manage Orders | `/admin/order/index` | View all orders, resolve disputes |
| Manage Payments | `/admin/payment/index` | Transaction log, manual verification |
| Commission Config | `/admin/commission/index` | Set rates globally or per category |
| Featured Listings | `/admin/featured/index` | Approve, configure boosts |
| Reports | `/admin/report/index` | Revenue, orders, users — filterable by date |
| Notifications | `/admin/notification/index` | Broadcast messages to users/providers |
| Categories | `/admin/category/index` | Manage listing categories |

---

### 7. Order & Payment Module

#### `common/models/Order.php`, `Payment.php`, `OrderItem.php`

**Payment type resolution logic:**

```
Order value < 2,000 KES     → Allow Payment on Delivery (if provider verified)
Order value 2,000–10,000    → Require Partial Payment (30% deposit default)
Order value > 10,000        → Require Full Payment upfront
```

> [!NOTE]
> These thresholds will be **configurable by the admin** in the settings panel, not hardcoded.

**Order Status Flow:**
```
pending → awaiting_payment / awaiting_deposit → paid / deposit_paid
       → processing → out_for_delivery → completed
       or → cancelled / failed
```

**M-Pesa STK Push Flow:**
1. Customer places order
2. System sends STK Push request to Daraja API
3. Customer confirms on phone
4. M-Pesa callback received → payment verified → order status updates
5. Provider notified via SMS + in-app notification

---

### 8. Subscription Module

#### `common/models/Subscription.php`, `SubscriptionPlan.php`

- Admin creates plans in panel
- Provider purchases plan via M-Pesa or card
- Console cron job checks for expiring subscriptions daily
- Grace period of 3 days before listings go inactive
- Email + SMS warning at 7 days and 1 day before expiry
- Expired providers: listings hidden, cannot receive new orders

**Default Plans (configurable):**

| Plan | Price (KES/mo) | Listings Limit | Featured Slots | Verified Badge |
|---|---|---|---|---|
| Basic | 1,000 | 5 products + 3 services | 0 | No |
| Professional | 2,500 | 20 products + 10 services | 1 | Yes |
| Premium | 5,000 | Unlimited | 3 | Yes + Priority Support |

---

### 9. Notification Module

#### `common/components/NotificationService.php`

Handles:
- **SMS** via Africa's Talking
- **Email** via Yii2 Mailer (SMTP/SendGrid)
- **In-app** stored in `notifications` table, shown in header bell icon

Trigger points:
- Customer: order placed, payment confirmed, order status changes, review requested
- Provider: new order received, subscription expiring, payment settled
- Admin: new provider registration awaiting approval

---

### 10. Review & Rating Module

#### `common/models/Review.php`

- Only customers who completed an order can review that provider
- Rating: 1–5 stars
- Optional text comment
- Provider average rating auto-calculated and cached on `providers.rating`
- Admin can flag/remove reviews

---

## Build Sequence (Execution Order)

| Step | Task |
|---|---|
| 1 | Scaffold Yii2 Advanced app |
| 2 | Configure database + run all migrations |
| 3 | Set up RBAC + Authentication |
| 4 | Build common models (User, Provider, Listing, Order, Payment) |
| 5 | Build Provider Portal (listing CRUD + order management) |
| 6 | Build Customer Frontend (browse, listing detail, order flow) |
| 7 | Integrate M-Pesa STK Push |
| 8 | Build Admin Dashboard |
| 9 | Build Subscription Module |
| 10 | Integrate SMS + Email notifications |
| 11 | Build Review & Rating system |
| 12 | Polish UI + testing |

---

## Open Questions

> [!IMPORTANT]
> **1. M-Pesa & Payment Gateway** — Do you already have Daraja API credentials (Consumer Key, Consumer Secret, Shortcode)? Are you using sandbox first or jumping to production? For card payments, do you prefer Flutterwave, Paystack, or another?

> [!IMPORTANT]
> **2. SMS Provider** — Africa's Talking is recommended. Do you already have an account, or should I design the notification module to be easily swappable (e.g., use an interface so you can plug in any SMS provider later)?

> [!IMPORTANT]
> **3. Hosting** — Do you have a server ready (VPS/shared hosting)? Yii2 requires PHP 7.4+ and Composer. This matters for whether we configure nginx/apache configs now or later.

> [!NOTE]
> **4. Provider Self-Registration** — Should providers register themselves and wait for admin approval (blueprint says yes), or should Phase 1 be admin-only onboarding (admin adds providers manually)? Self-registration is more scalable; manual is simpler to start.

> [!NOTE]
> **5. KES Currency Only?** — Should the system be locked to Kenya Shillings (KES) for Phase 1, or should we build in multi-currency support from the start (adds complexity)?

> [!NOTE]
> **6. Domain / Branding Assets** — Do you have a logo for HudumaLynk? If not, I can generate one. This affects the header and email templates.

---

## Verification Plan

### Automated
- Yii2 built-in test suite (unit + functional) for models and controllers
- Manual API testing via browser for all user flows

### Manual Verification Steps
1. Register as customer → search listing → place order → simulate M-Pesa payment → verify status update
2. Register as provider → purchase subscription → create listing → receive order → fulfill
3. Login as admin → approve provider → view reports → configure commission
4. Test subscription expiry: expire a subscription manually, verify listings become inactive
5. Test all three payment types: full, deposit, COD

