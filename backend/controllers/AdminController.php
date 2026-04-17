<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\{User, Provider, Order, Listing, Payment, Commission, Subscription, SubscriptionPlan, Review, Category, Notification, Setting};

class AdminController extends Controller
{
    public $layout = 'main';

    public function render($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return parent::renderPartial($view, $params);
        }
        return parent::render($view, $params);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [[
                    'allow'          => true,
                    'roles'          => ['@'],
                    'matchCallback'  => fn() => Yii::$app->user->identity?->isAdmin(),
                ]],
                'denyCallback' => function ($rule, $action) {
                    $user = Yii::$app->user->identity;
                    if ($user && $user->isProvider()) {
                        return Yii::$app->response->redirect(['/provider/dashboard']);
                    }
                    // For others, redirect to login or frontend
                    return Yii::$app->response->redirect(Yii::$app->params['frontendUrl']);
                },
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'approve-provider'    => ['post'],
                    'reject-provider'     => ['post'],
                    'toggle-listing'      => ['post'],
                    'toggle-featured'     => ['post'],
                    'review-toggle'       => ['post'],
                    'review-delete'       => ['post'],
                    'vendor-suspend'      => ['post'],
                    'vendor-delete'       => ['post'],
                    'user-suspend'        => ['post'],
                    'category-delete'     => ['post'],
                    'subscription-renew'  => ['post'],
                    'subscription-cancel' => ['post'],
                ],
            ],
        ];
    }

    // ── Shared helpers ────────────────────────────────────────────────────────

    private function findProvider(int $id): Provider
    {
        $m = Provider::findOne($id);
        if (!$m) throw new NotFoundHttpException('Vendor not found.');
        return $m;
    }

    private function findUser(int $id): User
    {
        $m = User::findOne($id);
        if (!$m) throw new NotFoundHttpException('User not found.');
        return $m;
    }

    private function findCategory(int $id): Category
    {
        $m = Category::findOne($id);
        if (!$m) throw new NotFoundHttpException('Category not found.');
        return $m;
    }

    private function findReview(int $id): Review
    {
        $m = Review::findOne($id);
        if (!$m) throw new NotFoundHttpException('Review not found.');
        return $m;
    }

    private function findSubscription(int $id): Subscription
    {
        $m = Subscription::findOne($id);
        if (!$m) throw new NotFoundHttpException('Subscription not found.');
        return $m;
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function actionDashboard()
    {
        $this->view->title = 'Dashboard Overview';

        $stats = [
            'total_users'     => User::find()->count(),
            'subscriptions'   => Subscription::find()->where(['status' => Subscription::STATUS_ACTIVE])->count(),
            'total_income'    => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->sum('amount') ?? 0,
            'active_listings' => Listing::find()->where(['status' => Listing::STATUS_ACTIVE])->count(),
            'orders_today'    => Order::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00')])->count(),
        ];

        $trends = ['months' => [], 'sales' => [], 'subscribers' => []];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $trends['months'][]      = date('M', strtotime("-$i months"));
            $trends['sales'][]       = (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['like', 'paid_at', $month])->sum('amount') ?? 0;
            $trends['subscribers'][] = (int)   Subscription::find()->andWhere(['like', 'created_at', $month])->count();
        }

        $distribution = [
            'labels' => ['Customers', 'Providers', 'Admins'],
            'data'   => [
                (int) User::find()->where(['role' => User::ROLE_CUSTOMER])->count(),
                (int) User::find()->where(['role' => User::ROLE_PROVIDER])->count(),
                (int) User::find()->where(['role' => User::ROLE_ADMIN])->count(),
            ],
        ];

        $latestUsers  = User::find()->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $topProviders = Provider::find()
            ->with(['user'])
            ->leftJoin('orders', 'orders.provider_id = providers.id')
            ->select(['providers.*', 'COUNT(orders.id) as order_count'])
            ->where(['orders.status' => Order::STATUS_COMPLETED])
            ->groupBy('providers.id')
            ->orderBy(['order_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        return $this->render('dashboard', compact('stats', 'trends', 'distribution', 'latestUsers', 'topProviders'));
    }

    // ── Analytics ─────────────────────────────────────────────────────────────

    public function actionAnalytics()
    {
        $this->view->title = 'Analytics';
        $period = (int) Yii::$app->request->get('period', 30);
        $since  = date('Y-m-d 00:00:00', strtotime("-{$period} days"));

        $totalGmv        = (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['>=', 'paid_at', $since])->sum('amount') ?? 0;
        $totalOrders     = (int)   Order::find()->where(['>=', 'created_at', $since])->count();
        $totalUsers      = (int)   User::find()->count();
        $completedOrders = (int)   Order::find()->where(['status' => Order::STATUS_COMPLETED])->andWhere(['>=', 'created_at', $since])->count();
        $avgOrderValue   = $completedOrders > 0 ? round($totalGmv / $completedOrders) : 0;

        $prevSince      = date('Y-m-d 00:00:00', strtotime("-{$period} days", strtotime($since)));
        $prevGmv        = (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['>=', 'paid_at', $prevSince])->andWhere(['<', 'paid_at', $since])->sum('amount') ?? 0;
        $prevOrders     = (int)   Order::find()->where(['>=', 'created_at', $prevSince])->andWhere(['<', 'created_at', $since])->count();
        $prevUsersCount = (int)   User::find()->where(['<', 'created_at', $since])->count();

        $gmvGrowthPct    = $prevGmv    > 0 ? round(($totalGmv   - $prevGmv)   / $prevGmv   * 100, 1) : 0;
        $ordersGrowthPct = $prevOrders > 0 ? round(($totalOrders - $prevOrders) / $prevOrders * 100, 1) : 0;
        $usersGrowthPct  = $prevUsersCount > 0 ? round(($totalUsers - $prevUsersCount) / $prevUsersCount * 100, 1) : 0;

        $revenueByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $m     = date('Y-m', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            $revenueByMonth[] = [
                'month'  => $label,
                'gmv'    => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['like', 'paid_at', $m])->sum('amount') ?? 0,
                'orders' => (int)   Order::find()->andWhere(['like', 'created_at', $m])->count(),
            ];
        }

        $orderStatuses = [
            'completed' => (int) Order::find()->where(['status' => Order::STATUS_COMPLETED])->andWhere(['>=', 'created_at', $since])->count(),
            'pending'   => (int) Order::find()->where(['status' => Order::STATUS_PENDING])->andWhere(['>=', 'created_at', $since])->count(),
            'cancelled' => (int) Order::find()->where(['status' => Order::STATUS_CANCELLED])->andWhere(['>=', 'created_at', $since])->count(),
        ];

        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $m     = date('Y-m', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            $userGrowth[] = [
                'month'     => $label,
                'new_users' => (int) User::find()->where(['role' => User::ROLE_CUSTOMER])->andWhere(['like', 'created_at', $m])->count(),
                'providers' => (int) User::find()->where(['role' => User::ROLE_PROVIDER])->andWhere(['like', 'created_at', $m])->count(),
            ];
        }

        $categoryBreakdown = Yii::$app->db->createCommand("
            SELECT c.name, COUNT(DISTINCT o.id) AS `count`, COALESCE(SUM(p.amount), 0) AS revenue
            FROM   categories c
            LEFT JOIN listings   l  ON l.category_id = c.id
            LEFT JOIN order_items oi ON oi.listing_id = l.id
            LEFT JOIN orders     o  ON o.id = oi.order_id AND o.status = :status AND o.created_at >= :since
            LEFT JOIN payments   p  ON p.order_id = o.id  AND p.status = :pstatus
            GROUP BY c.id, c.name ORDER BY revenue DESC LIMIT 8
        ", [':status' => Order::STATUS_COMPLETED, ':since' => $since, ':pstatus' => Payment::STATUS_COMPLETED])->queryAll();

        $topVendors = Yii::$app->db->createCommand("
            SELECT pr.id, pr.business_name, COUNT(DISTINCT o.id) AS orders,
                   COALESCE(SUM(p.amount),0) AS revenue, COALESCE(AVG(r.rating),0) AS rating
            FROM   providers pr
            LEFT JOIN orders   o ON o.provider_id = pr.id AND o.status = :status AND o.created_at >= :since
            LEFT JOIN payments p ON p.order_id = o.id AND p.status = :pstatus
            LEFT JOIN reviews  r ON r.provider_id = pr.id
            GROUP BY pr.id, pr.business_name ORDER BY revenue DESC LIMIT 10
        ", [':status' => Order::STATUS_COMPLETED, ':since' => $since, ':pstatus' => Payment::STATUS_COMPLETED])->queryAll();

        $countyStats = Yii::$app->db->createCommand("
            SELECT COALESCE(pr.city,'Unknown') AS county, COUNT(DISTINCT o.id) AS orders,
                   COALESCE(SUM(p.amount),0) AS revenue
            FROM   providers pr
            LEFT JOIN orders   o ON o.provider_id = pr.id AND o.status = :status AND o.created_at >= :since
            LEFT JOIN payments p ON p.order_id = o.id AND p.status = :pstatus
            GROUP BY pr.city ORDER BY revenue DESC LIMIT 10
        ", [':status' => Order::STATUS_COMPLETED, ':since' => $since, ':pstatus' => Payment::STATUS_COMPLETED])->queryAll();

        $funnel = [
            ['stage' => 'Site visits',    'count' => 0,               'color' => '#6C5CE7'],
            ['stage' => 'Listing views',  'count' => 0,               'color' => '#A29BFE'],
            ['stage' => 'Orders placed',  'count' => $totalOrders,    'color' => '#FDCB6E'],
            ['stage' => 'Completed',      'count' => $completedOrders,'color' => '#00B894'],
        ];

        return $this->render('analytics', compact(
            'period', 'totalGmv', 'totalOrders', 'totalUsers', 'avgOrderValue',
            'gmvGrowthPct', 'ordersGrowthPct', 'usersGrowthPct',
            'revenueByMonth', 'orderStatuses', 'userGrowth',
            'categoryBreakdown', 'topVendors', 'countyStats', 'funnel'
        ));
    }

    // ── Map ───────────────────────────────────────────────────────────────────

    public function actionMap()
    {
        $this->view->title = 'Nairobi Map';
        $vendors = Yii::$app->db->createCommand("
            SELECT pr.id, pr.business_name, pr.city AS county,
                   pr.lat, pr.lng,
                   COALESCE(c.name,'General') AS category,
                   COUNT(DISTINCT o.id) AS orders, COALESCE(AVG(r.rating),0) AS rating
            FROM   providers pr
            LEFT JOIN listings   l ON l.provider_id = pr.id AND l.status = 'active'
            LEFT JOIN categories c ON c.id = l.category_id
            LEFT JOIN orders     o ON o.provider_id = pr.id AND o.status = :status
            LEFT JOIN reviews    r ON r.provider_id = pr.id
            WHERE  pr.status = :pstatus AND pr.lat IS NOT NULL AND pr.lng IS NOT NULL
            GROUP BY pr.id, pr.business_name, pr.city, pr.lat, pr.lng, c.name
            ORDER BY orders DESC LIMIT 50
        ", [':status' => Order::STATUS_COMPLETED, ':pstatus' => Provider::STATUS_ACTIVE])->queryAll();

        $categories = Yii::$app->db->createCommand("
            SELECT DISTINCT c.name FROM categories c
            INNER JOIN listings  l  ON l.category_id = c.id
            INNER JOIN providers pr ON pr.id = l.provider_id AND pr.status = :pstatus
            ORDER BY c.name
        ", [':pstatus' => Provider::STATUS_ACTIVE])->queryColumn();

        $counties = Yii::$app->db->createCommand("
            SELECT DISTINCT city AS county FROM providers
            WHERE status = :pstatus AND city IS NOT NULL AND lat IS NOT NULL AND lng IS NOT NULL ORDER BY city
        ", [':pstatus' => Provider::STATUS_ACTIVE])->queryColumn();

        $totalVendors = Yii::$app->db->createCommand("
            SELECT COUNT(*) FROM providers pr
            WHERE pr.status = :pstatus AND pr.lat IS NOT NULL AND pr.lng IS NOT NULL
        ", [':pstatus' => Provider::STATUS_ACTIVE])->queryScalar();
        
        $activeToday  = Yii::$app->db->createCommand("
            SELECT COUNT(DISTINCT p.id) FROM providers p
            INNER JOIN orders o ON o.provider_id = p.id
            WHERE o.created_at >= :today AND o.status IN ('pending', 'completed') 
            AND p.status = :pstatus AND p.lat IS NOT NULL AND p.lng IS NOT NULL
        ", [':today' => date('Y-m-d 00:00:00'), ':pstatus' => Provider::STATUS_ACTIVE])->queryScalar();

        return $this->render('map', compact('vendors', 'categories', 'counties', 'totalVendors', 'activeToday'));
    }

    // ── Vendors ───────────────────────────────────────────────────────────────

    public function actionVendors()
    {
        $this->view->title = 'Vendors';
        $period = (int) Yii::$app->request->get('period', 30);
        $since  = date('Y-m-d 00:00:00', strtotime("-{$period} days"));

        $totalProviders   = Provider::find()->count();
        $pendingProviders = Provider::find()->where(['status' => Provider::STATUS_PENDING])->count();
        $activeProviders  = Provider::find()->where(['status' => Provider::STATUS_ACTIVE])->count();
        $avgRating        = Provider::find()->where(['status' => Provider::STATUS_ACTIVE])->average('rating') ?: 0;
        $newProviders     = Provider::find()->where(['>=', 'created_at', $since])->count();

        $topVendors = Yii::$app->db->createCommand("
            SELECT pr.id, pr.business_name,
                   COUNT(DISTINCT o.id) AS orders, COALESCE(SUM(p.amount),0) AS revenue,
                   COALESCE(AVG(r.rating),0) AS rating
            FROM providers pr
            LEFT JOIN orders   o ON o.provider_id = pr.id AND o.status = :status AND o.created_at >= :since
            LEFT JOIN payments p ON p.order_id = o.id AND p.status = :pstatus
            LEFT JOIN reviews  r ON r.provider_id = pr.id AND r.status = 'visible'
            WHERE pr.status = 'active'
            GROUP BY pr.id, pr.business_name ORDER BY revenue DESC LIMIT 10
        ", [':status' => Order::STATUS_COMPLETED, ':since' => $since, ':pstatus' => Payment::STATUS_COMPLETED])->queryAll();

        $cityStats = Yii::$app->db->createCommand("
            SELECT pr.city, COUNT(*) AS count, COALESCE(AVG(r.rating),0) AS avg_rating
            FROM providers pr LEFT JOIN reviews r ON r.provider_id = pr.id
            WHERE pr.status = 'active' AND pr.city IS NOT NULL
            GROUP BY pr.city ORDER BY count DESC LIMIT 8
        ")->queryAll();

        $statusBreakdown = [
            'pending'   => $pendingProviders,
            'active'    => $activeProviders,
            'rejected'  => Provider::find()->where(['status' => Provider::STATUS_REJECTED])->count(),
            'suspended' => Provider::find()->where(['status' => Provider::STATUS_SUSPENDED])->count(),
        ];

        $statusFilter = Yii::$app->request->get('status');
        $query = Provider::find()->with('user')->orderBy(['created_at' => SORT_DESC]);
        if ($statusFilter && in_array($statusFilter, [Provider::STATUS_PENDING, Provider::STATUS_ACTIVE, Provider::STATUS_SUSPENDED, Provider::STATUS_REJECTED])) {
            $query->andWhere(['status' => $statusFilter]);
        }
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 12]]);

        return $this->render('vendors', compact(
            'totalProviders', 'pendingProviders', 'activeProviders', 'avgRating', 'newProviders',
            'topVendors', 'cityStats', 'statusBreakdown', 'period', 'dataProvider'
        ));
    }

    public function actionVendorView(int $id)
    {
        $this->view->title = 'Vendor Profile';
        $vendor  = $this->findProvider($id);
        $stats = [
            'listings'    => Listing::find()->where(['provider_id' => $id])->count(),
            'orders'      => Order::find()->where(['provider_id' => $id])->count(),
            'revenue'     => (float) Order::find()->where(['provider_id' => $id, 'status' => Order::STATUS_COMPLETED])->sum('total_amount') ?? 0,
            'avg_rating'  => $vendor->rating,
        ];
        $recentOrders = Order::find()->with(['user'])->where(['provider_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $recentReviews = Review::find()->with(['user'])->where(['provider_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $subscription = $vendor->getActiveSubscription();

        return $this->render('vendor-view', compact('vendor', 'stats', 'recentOrders', 'recentReviews', 'subscription'));
    }

    public function actionVendorEdit(int $id)
    {
        $this->view->title = 'Edit Vendor';
        $vendor = $this->findProvider($id);

        if ($vendor->load(Yii::$app->request->post()) && $vendor->save()) {
            Yii::$app->session->setFlash('success', 'Vendor profile updated.');
            return $this->redirect(['/admin/vendor-view', 'id' => $vendor->id]);
        }

        return $this->render('vendor-edit', compact('vendor'));
    }

    public function actionVendorSuspend(int $id)
    {
        $vendor = $this->findProvider($id);
        $vendor->status = $vendor->status === Provider::STATUS_SUSPENDED ? Provider::STATUS_ACTIVE : Provider::STATUS_SUSPENDED;
        $vendor->save(false);
        $action = $vendor->status === Provider::STATUS_SUSPENDED ? 'suspended' : 're-activated';
        Yii::$app->session->setFlash('success', "{$vendor->business_name} has been {$action}.");
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/vendors']);
    }

    public function actionVendorDelete(int $id)
    {
        $vendor = $this->findProvider($id);
        $name   = $vendor->business_name;
        $vendor->status = Provider::STATUS_REJECTED;
        $vendor->save(false);
        Yii::$app->session->setFlash('warning', "{$name} has been deactivated.");
        return $this->redirect(['/admin/vendors']);
    }

    // ── Provider Approval ─────────────────────────────────────────────────────

    public function actionProviders()
    {
        $this->view->title = 'Approval Queue';
        $status    = Yii::$app->request->get('status', 'pending');
        $providers = Provider::find()->with(['user'])->where(['status' => $status])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('providers', compact('providers', 'status'));
    }

    public function actionApproveProvider(int $id)
    {
        $provider = $this->findProvider($id);
        $provider->status = Provider::STATUS_ACTIVE;
        $provider->save(false);
        if ($provider->user) {
            try { Yii::$app->sms->providerApproved($provider->user); } catch (\Exception $e) {}
        }
        Yii::$app->session->setFlash('success', "{$provider->business_name} approved.");
        return $this->redirect(['/admin/providers']);
    }

    public function actionRejectProvider(int $id)
    {
        $provider = $this->findProvider($id);
        $reason   = Yii::$app->request->post('reason', 'Does not meet requirements.');
        $provider->status           = Provider::STATUS_REJECTED;
        $provider->rejection_reason = $reason;
        $provider->save(false);
        if ($provider->user) {
            try { Yii::$app->sms->providerRejected($provider->user, $reason); } catch (\Exception $e) {}
        }
        Yii::$app->session->setFlash('warning', "{$provider->business_name} rejected.");
        return $this->redirect(['/admin/providers']);
    }

    // ── Listings ──────────────────────────────────────────────────────────────

    public function actionListings()
    {
        $this->view->title = 'All Listings';
        $stats = [
            'total'    => Listing::find()->count(),
            'active'   => Listing::find()->where(['status' => Listing::STATUS_ACTIVE])->count(),
            'draft'    => Listing::find()->where(['status' => Listing::STATUS_DRAFT])->count(),
            'new_week' => Listing::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('-7 days'))])->count(),
        ];

        $query  = Listing::find()->with(['provider', 'category'])->orderBy(['created_at' => SORT_DESC]);
        $status = Yii::$app->request->get('status');
        if ($status && in_array($status, [Listing::STATUS_ACTIVE, Listing::STATUS_DRAFT, Listing::STATUS_INACTIVE])) {
            $query->andWhere(['status' => $status]);
        }
        $search = Yii::$app->request->get('q');
        if ($search) {
            $query->andWhere(['like', 'title', $search]);
        }

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        return $this->render('listings', compact('stats', 'dataProvider'));
    }

    public function actionToggleListing(int $id)
    {
        $listing = Listing::findOne($id);
        if ($listing) {
            $listing->status = $listing->status === Listing::STATUS_ACTIVE ? Listing::STATUS_INACTIVE : Listing::STATUS_ACTIVE;
            $listing->save(false);
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/listings']);
    }

    public function actionToggleFeatured(int $id)
    {
        $listing = Listing::findOne($id);
        if ($listing) {
            $listing->is_featured = !$listing->is_featured;
            $listing->save(false);
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/listings']);
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function actionOrders()
    {
        $this->view->title = 'All Orders';
        $today = date('Y-m-d 00:00:00');
        $stats = [
            'total'     => Order::find()->count(),
            'completed' => Order::find()->where(['status' => Order::STATUS_COMPLETED])->count(),
            'pending'   => Order::find()->where(['status' => Order::STATUS_PENDING])->count(),
            'gmv_today' => (float) Order::find()->where(['>=', 'created_at', $today])->sum('total_amount') ?? 0,
        ];

        $query     = Order::find()->with(['user', 'provider'])->orderBy(['created_at' => SORT_DESC]);
        $status    = Yii::$app->request->get('status');
        $validSt   = [Order::STATUS_PENDING, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED];
        if ($status && in_array($status, $validSt)) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        return $this->render('orders', compact('stats', 'dataProvider'));
    }

    public function actionOrderView(int $id)
    {
        $this->view->title = 'Order Detail';
        $order = Order::find()->with(['user', 'provider', 'items', 'items.listing'])->where(['id' => $id])->one();
        if (!$order) throw new NotFoundHttpException('Order not found.');
        $payment = Payment::findOne(['order_id' => $id]);
        return $this->render('order-view', compact('order', 'payment'));
    }

    // ── Categories CRUD ───────────────────────────────────────────────────────

    public function actionCategories()
    {
        $this->view->title = 'Categories';
        $stats = [
            'total'    => Category::find()->count(),
            'active'   => Category::find()->where(['status' => Category::STATUS_ACTIVE])->count(),
            'services' => Category::find()->where(['type' => Category::TYPE_SERVICE])->count(),
            'products' => Category::find()->where(['type' => Category::TYPE_PRODUCT])->count(),
        ];
        $query        = Category::find()->with(['parent'])->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 25]]);
        $parents      = Category::find()->where(['parent_id' => null])->orderBy(['name' => SORT_ASC])->all();

        return $this->render('categories', compact('stats', 'dataProvider', 'parents'));
    }

    public function actionCategoryCreate()
    {
        $this->view->title = 'New Category';
        $category = new Category();

        if ($category->load(Yii::$app->request->post()) && $category->save()) {
            Yii::$app->session->setFlash('success', "Category \"{$category->name}\" created.");
            return $this->redirect(['/admin/categories']);
        }

        $parents = Category::find()->where(['parent_id' => null])->orderBy(['name' => SORT_ASC])->all();
        return $this->render('category-form', compact('category', 'parents'));
    }

    public function actionCategoryEdit(int $id)
    {
        $this->view->title = 'Edit Category';
        $category = $this->findCategory($id);

        if ($category->load(Yii::$app->request->post()) && $category->save()) {
            Yii::$app->session->setFlash('success', "Category \"{$category->name}\" updated.");
            return $this->redirect(['/admin/categories']);
        }

        $parents = Category::find()->where(['parent_id' => null])->andWhere(['!=', 'id', $id])->orderBy(['name' => SORT_ASC])->all();
        return $this->render('category-form', compact('category', 'parents'));
    }

    public function actionCategoryDelete(int $id)
    {
        $category = $this->findCategory($id);
        $name     = $category->name;
        // Move children to no parent before deleting
        Category::updateAll(['parent_id' => null], ['parent_id' => $id]);
        $category->delete();
        Yii::$app->session->setFlash('success', "Category \"{$name}\" deleted.");
        return $this->redirect(['/admin/categories']);
    }

    // ── Reviews ───────────────────────────────────────────────────────────────

    public function actionReviews()
    {
        $this->view->title = 'Reviews';
        $stats = [
            'total'      => Review::find()->count(),
            'avg_rating' => (float) Review::find()->where(['status' => Review::STATUS_VISIBLE])->average('rating') ?: 0,
            'five_star'  => Review::find()->where(['rating' => 5])->count(),
            'hidden'     => Review::find()->where(['status' => Review::STATUS_HIDDEN])->count(),
        ];

        $query  = Review::find()->with(['user', 'provider'])->orderBy(['created_at' => SORT_DESC]);
        $filter = Yii::$app->request->get('filter');
        if ($filter === '5star')   $query->andWhere(['rating' => 5]);
        if ($filter === '4star')   $query->andWhere(['rating' => 4]);
        if ($filter === '3below')  $query->andWhere(['<=', 'rating', 3]);
        if ($filter === 'hidden')  $query->andWhere(['status' => Review::STATUS_HIDDEN]);

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 15]]);
        return $this->render('reviews', compact('stats', 'dataProvider'));
    }

    public function actionReviewToggle(int $id)
    {
        $review = $this->findReview($id);
        $review->status = $review->status === Review::STATUS_VISIBLE ? Review::STATUS_HIDDEN : Review::STATUS_VISIBLE;
        $review->save(false);
        // Recalculate provider rating
        if ($review->provider) $review->provider->recalculateRating();
        Yii::$app->session->setFlash('success', 'Review visibility updated.');
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/reviews']);
    }

    public function actionReviewDelete(int $id)
    {
        $review   = $this->findReview($id);
        $provider = $review->provider;
        $review->delete();
        if ($provider) $provider->recalculateRating();
        Yii::$app->session->setFlash('success', 'Review permanently deleted.');
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/reviews']);
    }

    // ── Earnings & GMV ────────────────────────────────────────────────────────

    public function actionEarnings()
    {
        $this->view->title = 'Earnings & GMV';
        $since30 = date('Y-m-d 00:00:00', strtotime('-30 days'));

        $stats = [
            'total_gmv'        => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->sum('amount') ?? 0,
            'gmv_30d'          => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['>=', 'paid_at', $since30])->sum('amount') ?? 0,
            'total_commission' => (float) Commission::find()->sum('amount') ?? 0,
            'pending_payouts'  => (float) Commission::find()->where(['status' => Commission::STATUS_PENDING])->sum('amount') ?? 0,
        ];

        // Monthly GMV chart (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m     = date('Y-m', strtotime("-$i months"));
            $label = date('M', strtotime("-$i months"));
            $chartData[] = [
                'month'      => $label,
                'gmv'        => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->andWhere(['like', 'paid_at', $m])->sum('amount') ?? 0,
                'commission' => (float) Commission::find()->andWhere(['like', 'created_at', $m])->sum('amount') ?? 0,
            ];
        }

        $query        = Commission::find()->with(['provider', 'order'])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);

        return $this->render('earnings', compact('stats', 'chartData', 'dataProvider'));
    }

    // ── Subscriptions ─────────────────────────────────────────────────────────

    public function actionSubscriptions()
    {
        $this->view->title = 'Subscriptions';
        $next7days = date('Y-m-d H:i:s', strtotime('+7 days'));
        $stats = [
            'active'         => Subscription::find()->where(['status' => Subscription::STATUS_ACTIVE])->count(),
            'total_revenue'  => (float) Subscription::find()->sum('amount_paid') ?? 0,
            'expiring_soon'  => Subscription::find()->where(['status' => Subscription::STATUS_ACTIVE])->andWhere(['<=', 'end_date', $next7days])->andWhere(['>=', 'end_date', date('Y-m-d H:i:s')])->count(),
            'expired'        => Subscription::find()->where(['status' => Subscription::STATUS_EXPIRED])->count(),
        ];

        $query  = Subscription::find()->with(['provider', 'provider.user', 'plan'])->orderBy(['created_at' => SORT_DESC]);
        $status = Yii::$app->request->get('status');
        if ($status && in_array($status, [Subscription::STATUS_ACTIVE, Subscription::STATUS_EXPIRED, Subscription::STATUS_CANCELLED])) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        $plans        = SubscriptionPlan::find()->where(['status' => 'active'])->orderBy(['price_kes' => SORT_ASC])->all();

        return $this->render('subscriptions', compact('stats', 'dataProvider', 'plans'));
    }

    public function actionSubscriptionRenew(int $id)
    {
        $sub = $this->findSubscription($id);
        if ($sub->plan) {
            $sub->end_date = date('Y-m-d H:i:s', strtotime('+' . $sub->plan->duration_days . ' days', strtotime($sub->end_date)));
            $sub->status   = Subscription::STATUS_ACTIVE;
            $sub->save(false);
            Yii::$app->session->setFlash('success', 'Subscription renewed by ' . ($sub->plan->duration_days ?? 30) . ' days.');
        }
        return $this->redirect(['/admin/subscriptions']);
    }

    public function actionSubscriptionCancel(int $id)
    {
        $sub         = $this->findSubscription($id);
        $sub->status = Subscription::STATUS_CANCELLED;
        $sub->save(false);
        Yii::$app->session->setFlash('warning', 'Subscription cancelled.');
        return $this->redirect(['/admin/subscriptions']);
    }

    // ── M-Pesa Payouts ────────────────────────────────────────────────────────

    public function actionPayouts()
    {
        $this->view->title = 'M-Pesa Payouts';
        $stats = [
            'total_paid'    => (float) Commission::find()->where(['status' => Commission::STATUS_PAID])->sum('amount') ?? 0,
            'pending'       => (float) Commission::find()->where(['status' => Commission::STATUS_PENDING])->sum('amount') ?? 0,
            'vendors_owed'  => Commission::find()->where(['status' => Commission::STATUS_PENDING])->select('provider_id')->distinct()->count(),
            'paid_this_month' => (float) Commission::find()->where(['status' => Commission::STATUS_PAID])->andWhere(['like', 'paid_at', date('Y-m')])->sum('amount') ?? 0,
        ];

        $query        = Commission::find()->with(['provider', 'order'])->orderBy(['created_at' => SORT_DESC]);
        $filter       = Yii::$app->request->get('status');
        if ($filter && in_array($filter, [Commission::STATUS_PENDING, Commission::STATUS_PAID])) {
            $query->andWhere(['status' => $filter]);
        }
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);

        return $this->render('payouts', compact('stats', 'dataProvider'));
    }

    // ── Invoices ──────────────────────────────────────────────────────────────

    public function actionInvoices()
    {
        $this->view->title = 'Invoices';
        $stats = [
            'total'       => Payment::find()->count(),
            'completed'   => Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->count(),
            'pending'     => Payment::find()->where(['status' => Payment::STATUS_PENDING])->count(),
            'total_value' => (float) Payment::find()->where(['status' => Payment::STATUS_COMPLETED])->sum('amount') ?? 0,
        ];

        $query  = Payment::find()->with(['order', 'order.user', 'order.provider'])->orderBy(['created_at' => SORT_DESC]);
        $status = Yii::$app->request->get('status');
        if ($status && in_array($status, [Payment::STATUS_PENDING, Payment::STATUS_COMPLETED, Payment::STATUS_FAILED])) {
            $query->andWhere(['status' => $status]);
        }
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);

        return $this->render('invoices', compact('stats', 'dataProvider'));
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    public function actionUsers()
    {
        $this->view->title = 'All Users';
        $stats = [
            'total'     => User::find()->count(),
            'customers' => User::find()->where(['role' => User::ROLE_CUSTOMER])->count(),
            'providers' => User::find()->where(['role' => User::ROLE_PROVIDER])->count(),
            'new_week'  => User::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('-7 days'))])->count(),
        ];

        $query  = User::find()->orderBy(['created_at' => SORT_DESC]);
        $role   = Yii::$app->request->get('role');
        if ($role && in_array($role, [User::ROLE_CUSTOMER, User::ROLE_PROVIDER, User::ROLE_ADMIN])) {
            $query->andWhere(['role' => $role]);
        }
        $search = Yii::$app->request->get('q');
        if ($search) {
            $query->andWhere(['or', ['like', 'email', $search], ['like', 'first_name', $search], ['like', 'last_name', $search], ['like', 'phone', $search]]);
        }

        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        return $this->render('users', compact('stats', 'dataProvider'));
    }

    public function actionUserView(int $id)
    {
        $this->view->title = 'User Profile';
        $user     = $this->findUser($id);
        $orders   = Order::find()->where(['user_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $reviews  = Review::find()->where(['user_id' => $id])->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        $provider = Provider::findOne(['user_id' => $id]);
        return $this->render('user-view', compact('user', 'orders', 'reviews', 'provider'));
    }

    public function actionUserEdit(int $id = 0)
    {
        $this->view->title = $id ? 'Edit User' : 'Add User';
        $user = $id ? $this->findUser($id) : new User(['scenario' => User::SCENARIO_REGISTER]);

        if ($user->load(Yii::$app->request->post())) {
            if (!$id && $user->password) {
                $user->setPassword($user->password);
                $user->generateAuthKey();
            }
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'User saved.');
                return $this->redirect(['/admin/users']);
            }
        }

        return $this->render('user-edit', compact('user'));
    }

    public function actionUserSuspend(int $id)
    {
        $user = $this->findUser($id);
        if ($user->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('danger', 'You cannot suspend yourself.');
            return $this->redirect(['/admin/users']);
        }
        $user->status = $user->status === User::STATUS_SUSPENDED ? User::STATUS_ACTIVE : User::STATUS_SUSPENDED;
        $user->save(false);
        $action = $user->status === User::STATUS_SUSPENDED ? 'suspended' : 're-activated';
        Yii::$app->session->setFlash('success', "{$user->getFullName()} has been {$action}.");
        return $this->redirect(Yii::$app->request->referrer ?: ['/admin/users']);
    }

    // ── Messages ──────────────────────────────────────────────────────────────

    public function actionMessages()
    {
        $this->view->title = 'Messages';
        $stats = [
            'total'  => Notification::find()->count(),
            'unread' => Notification::find()->where(['is_read' => false])->count(),
            'today'  => Notification::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00')])->count(),
        ];
        $query        = Notification::find()->with(['user'])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        return $this->render('messages', compact('stats', 'dataProvider'));
    }

    // ── Notifications ─────────────────────────────────────────────────────────

    public function actionNotifications()
    {
        $this->view->title = 'Notifications';
        $stats = [
            'total'       => Notification::find()->count(),
            'unread'      => Notification::find()->where(['is_read' => false])->count(),
            'sms_today'   => 0, // Placeholder — wire to your SMS log table
        ];

        if (Yii::$app->request->isPost) {
            $title     = Yii::$app->request->post('title', '');
            $message   = Yii::$app->request->post('message', '');
            $recipient = Yii::$app->request->post('recipient', 'all_users');

            if ($title && $message) {
                $query = User::find();
                if ($recipient === 'all_vendors')   $query->where(['role' => User::ROLE_PROVIDER]);
                if ($recipient === 'all_customers') $query->where(['role' => User::ROLE_CUSTOMER]);
                foreach ($query->all() as $u) {
                    $n          = new Notification();
                    $n->user_id = $u->id;
                    $n->title   = $title;
                    $n->message = $message;
                    $n->is_read = false;
                    $n->save(false);
                }
                Yii::$app->session->setFlash('success', 'Notification sent.');
                return $this->refresh();
            }
        }

        $query        = Notification::find()->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 15]]);
        return $this->render('notifications', compact('stats', 'dataProvider'));
    }

    // ── Settings ──────────────────────────────────────────────────────────────

    public function actionSettings()
    {
        $this->view->title = 'Platform Settings';

        if (Yii::$app->request->isPost) {
            foreach (Yii::$app->request->post('settings', []) as $key => $val) {
                Setting::set($key, $val);
            }
            Yii::$app->session->setFlash('success', 'Settings saved successfully.');
            return $this->refresh();
        }

        $settingKeys = [
            'platform_name', 'platform_tagline', 'support_email', 'support_phone',
            'commission_rate', 'mpesa_shortcode', 'mpesa_passkey',
            'mpesa_consumer_key', 'mpesa_consumer_secret', 'mpesa_callback_url',
            'frontend_url', 'maintenance_mode', 'registration_open',
        ];
        $settings = [];
        foreach ($settingKeys as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        return $this->render('settings', compact('settings'));
    }
}