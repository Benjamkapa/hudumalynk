# Admin UI/UX Specifications for Lynk Marketplace

## Overview
Match existing fixed pages (vendors.php, listings.php, orders.php): Card-based hero stats (4-column grid with labels/values/subs), filter pills row, main content table/grid with custom styling (hl-tbl, hl-card), bottom pagination with styled buttons. Use var(-- variables) for colors (teal, acc, amber, rose, text1-3, bg/bg2, border). Responsive (media queries for <900px, <560px).

**Key Components:**
- **Hero**: `.pg-hero` grid 4 cols, `.pg-hcard` stats (lbl, val, sub).
- **Filters**: `.filter-row` with `.filter-pill` (active state).
- **Table**: `.hl-card > .hl-card-head` (title + button/link), `.hl-tbl` striped hover table.
- **Pagination**: Flex center, 32px round buttons (active acc-pale).
- **SVGs**: Stroke icons 12-13px.
- **Badges**: Status classes (active/teal-pale, pending/amber-pale, etc.).

**DataProvider**: Always use $dataProvider->getModels() loop, with $stats hero, pageSize 12-20.

## Page-by-Page Specs (Vendors downwards)

### Vendors (admin/vendors.php) - FIXED EXAMPLE
- Hero: Total, Active, Pending, New 30d.
- Grid: vendor-card (avatar, name/cat/badge, stats orders/listings/rating, actions view/edit/delete).
- Filter: All/Active/Pending/Suspended.

### Listings (admin/listings.php) - FIXED
- Hero: Total, Active, Draft, New week.
- Table: Listing avatar/title/ID, Vendor, Category, Price, Status badge, Created, actions View/Edit.
- Filter: All/Active/Draft/Inactive.

### Orders (admin/orders.php) - FIXED
- Hero: Total, Completed, Pending, GMV today.
- Table: Ref, Customer avatar/name, Vendor, Amount, Status ord-status, Date, View.
- Filter: All/Pending/Processing/Completed/Cancelled.

### Categories (admin/categories.php)
- Hero: Total categories, Active, Featured, Avg listings per cat.
- Grid/table: Category avatar/color, Name, Listings count, Vendors using, Status, Created.
- Actions: Edit, Delete.
- Filter: All/Active/Draft.
- Button: New category.

### Payouts (admin/payouts.php)
- Hero: Total payouts, Pending, Processed this month, Total paid.
- Table: Payout ID, Provider, Amount, Status (pending/paid), Date requested, Processed date, View.
- Filter: All/Pending/Processing/Paid.
- Button: Process payouts.

### Subscriptions (admin/subscriptions.php)
- Hero: Active subs, Total revenue, Expiring soon, Free trials.
- Table: Provider, Plan name, Status (active/expired), Start/End date, Amount, Actions renew/cancel.
- Filter: Active/Expired/Trial.
- Button: Create plan.

### Reviews (admin/reviews.php)
- Hero: Total, Avg rating ★, 5-star, Flagged.
- List: review-card (user avatar/name/date/stars, comment, vendor/listing foot, actions View/Remove).
- Filter: All/5star/4star/3below/Flagged.

### Users (admin/users.php) - FIXED
- Hero: Total, Customers, Providers, New week.
- Table: User avatar/name/email, Role badge, Phone, Joined, Orders count, Status, View/Edit.

### Earnings (admin/earnings.php)
- Hero: Total platform earnings, This month, Providers paid, Commissions %.
- Charts: Revenue over time (line/bar), Breakdown (pie: commissions/platform fee).
- Table: Provider, Earnings this month, Paid, Pending payout, Balance.
- Filter: Period (30d, 90d, year).

### Invoices (admin/invoices.php)
- Hero: Generated, Paid, Pending, Revenue.
- Table: Invoice #, Provider/Customer, Amount, Status, Date, Download PDF.
- Filter: All/Paid/Pending.
- Button: Bulk send.

### Map (admin/map.php) - FIXED?
- Hero/map with markers (providers, listings density).
- Filter: Categories, Counties.
- Sidebar list vendors with orders/rating.

### Messages (admin/messages.php)
- Hero: Unread msgs, Active chats, Total conversations.
- Chat list/sidebar, message view.
- Filter: Unread/Providers/Customers.

### Notifications (admin/notifications.php)
- Hero: Sent, Unread, SMS today.
- List: notif-item (icon, title/body/time, unread dot).
- Form: Send notif (recipient/channel/title/message).
- Filter: All/Unread.

### Analytics (admin/analytics.php) - FIXED
- Charts/tables for GMV, growth, funnel.

### Settings (admin/settings.php) - FIXED
- Form groups, notification log.

## Developer Instructions
1. Copy fixed pages as templates (vendors/listings/orders).
2. Controller: Add $stats queries, ActiveDataProvider with relations (with(['user', 'provider'])) , pageSize 20.
3. View: PHPDoc $dataProvider, hero pg-hero, table hl-tbl, loop $dataProvider->getModels(), pagination code.
4. Use model relations (e.g. $order->user->getFullName(), $listing->provider->business_name).
5. Add CRUD actions (view/update/delete) linking to new controllers/views.
6. Make responsive, use existing CSS vars/SVGs.

Models exist for all (Order, Listing, Review, User, Provider, Subscription, Notification, etc.).

Build in order: Categories → Payouts → Subscriptions → Reviews → Earnings → Invoices → Messages. Dashboard/analytics/map/settings good.

Deployment ready after these.
