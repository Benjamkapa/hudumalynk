<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\models\{Commission, Provider, Listing, Order, Review, Subscription, SubscriptionPlan};

class ProviderController extends Controller
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
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => fn() => Yii::$app->user->identity?->isProvider(),
                ]],
                'denyCallback' => function ($rule, $action) {
                    $user = Yii::$app->user->identity;
                    if ($user && $user->isAdmin()) {
                        return Yii::$app->response->redirect(['/admin/dashboard']);
                    }
                    // For others, redirect to frontend
                    return Yii::$app->response->redirect(Yii::$app->params['frontendUrl']);
                },
            ],
        ];
    }

    private function getProvider(): Provider
    {
        $p = Provider::findOne(['user_id' => Yii::$app->user->id]);
        if (!$p) throw new NotFoundHttpException('Provider profile not found.');
        return $p;
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function actionDashboard()
    {
        $this->view->title = 'Provider Dashboard';
        $provider = $this->getProvider();

        if ($provider->status === Provider::STATUS_PENDING) {
            return $this->render('pending');
        }

        // ── 1. Top Level Stats ───────────────────────────────────────────────
        $stats = [
            'balance'        => (float)Order::find()->where(['provider_id' => $provider->id, 'payment_status' => Order::PAYMENT_STATUS_PAID])->sum('total_amount') ?? 0,
            'active_listings'=> Listing::find()->where(['provider_id' => $provider->id, 'status' => Listing::STATUS_ACTIVE])->count(),
            'total_orders'   => Order::find()->where(['provider_id' => $provider->id])->count(),
            'avg_rating'     => $provider->rating,
        ];

        // ── 2. Earnings Trend (Last 6 Months) ────────────────────────────────
        $trends = [
            'months'   => [],
            'earnings' => [],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $label = date('M',   strtotime("-$i months"));
            $trends['months'][] = $label;

            $earn = (float)Order::find()
                ->where(['provider_id' => $provider->id, 'payment_status' => Order::PAYMENT_STATUS_PAID])
                ->andWhere(['like', 'created_at', $month])
                ->sum('total_amount') ?? 0;
            $trends['earnings'][] = $earn;
        }

        // ── 3. Recent Orders ─────────────────────────────────────────────────
        $recentOrders = Order::find()
            ->with(['user'])
            ->where(['provider_id' => $provider->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // ── 4. Recent Reviews ────────────────────────────────────────────────
        $recentReviews = Review::find()
            ->with(['user'])
            ->where(['provider_id' => $provider->id, 'status' => Review::STATUS_VISIBLE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(4)
            ->all();

        return $this->render('dashboard', compact(
            'provider', 
            'stats', 
            'trends', 
            'recentOrders', 
            'recentReviews'
        ));
    }

    // ── Listings CRUD ─────────────────────────────────────────────────────────

    public function actionListings()
    {
        $this->view->title = 'My Listings';
        $provider = $this->getProvider();
        $listings = Listing::find()->with(['category'])
            ->where(['provider_id' => $provider->id])
            ->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('listings', compact('provider', 'listings'));
    }

    public function actionCreateListing()
    {
        $this->view->title = 'Add New Listing';
        $provider = $this->getProvider();

        if (!$provider->canPublishListings()) {
            Yii::$app->session->setFlash('warning', 'Your account is not active or your subscription has expired. Please subscribe to a plan.');
            return $this->redirect(['/provider/subscription']);
        }

        $listing = new Listing(['provider_id' => $provider->id]);
        if ($listing->load(Yii::$app->request->post())) {
            $listing->provider_id = $provider->id;
            $listing->status      = Listing::STATUS_ACTIVE;

            if ($listing->save(false)) {
                // Handle multiple image uploads
                $imageFiles = UploadedFile::getInstances($listing, 'imageFiles');
                if ($imageFiles && count($imageFiles) > 0 && count($imageFiles) <= 5) {
                    $dir = Yii::getAlias('@uploads') . '/listings/' . $provider->id . '/';
                    @mkdir($dir, 0755, true);
                    foreach ($imageFiles as $i => $file) {
                        if ($file->error === UPLOAD_ERR_OK) {
                            $filename = 'img-' . time() . '-' . rand(1000,9999) . '-' . $i . '.' . $file->extension;
                            $file->saveAs($dir . $filename);
                            $imagePath = 'listings/' . $provider->id . '/' . $filename;
                            
                            $listingImage = new \common\models\ListingImage([
                                'listing_id' => $listing->id,
                                'image_path' => $imagePath,
                                'sort_order' => $i,
                            ]);
                            if ($i === 0) {
                                $listingImage->is_primary = true;
                                $listing->primary_image = $imagePath;
                            }
                            $listingImage->save(false);
                        }
                    }
                    $listing->save(false);
                    Yii::$app->session->setFlash('success', 'Listing "' . $listing->name . '" published!');
                    return $this->redirect(['/provider/listings']);
                } else {
                    // No images, but listing saved
                    Yii::$app->session->setFlash('success', 'Listing "' . $listing->name . '" published!');
                    return $this->redirect(['/provider/listings']);
                }
            }
        }

        $categories = \common\models\Category::flatList();
        return $this->render('listing-form', compact('listing', 'categories'));
    }

    public function actionEditListing(int $id)
    {
        $this->view->title = 'Edit Listing';
        $provider = $this->getProvider();
        $listing  = Listing::findOne(['id' => $id, 'provider_id' => $provider->id]);
        if (!$listing) throw new NotFoundHttpException();

        if ($listing->load(Yii::$app->request->post())) {
            // Delete old images on update
            \common\models\ListingImage::deleteAll(['listing_id' => $listing->id]);
            
            // Handle multiple image uploads (replace all)
            $imageFiles = UploadedFile::getInstances($listing, 'imageFiles');
            if ($imageFiles && count($imageFiles) > 0 && count($imageFiles) <= 5) {
                $dir = Yii::getAlias('@uploads') . '/listings/' . $provider->id . '/';
                @mkdir($dir, 0755, true);
                foreach ($imageFiles as $i => $file) {
                    if ($file->error === UPLOAD_ERR_OK) {
                        $filename = 'img-' . time() . '-' . rand(1000,9999) . '-' . $i . '.' . $file->extension;
                        $file->saveAs($dir . $filename);
                        $imagePath = 'listings/' . $provider->id . '/' . $filename;
                        
                        $listingImage = new \common\models\ListingImage([
                            'listing_id' => $listing->id,
                            'image_path' => $imagePath,
                            'sort_order' => $i,
                        ]);
                        if ($i === 0) {
                            $listingImage->is_primary = true;
                            $listing->primary_image = $imagePath;
                        }
                        $listingImage->save(false);
                    }
                }
            }
            if ($listing->save()) {
                Yii::$app->session->setFlash('success', 'Listing updated.');
                return $this->redirect(['/provider/listings']);
            }
        }

        $categories = \common\models\Category::flatList();
        return $this->render('listing-form', compact('listing', 'categories'));
    }

    public function actionDeleteListing(int $id)
    {
        $provider = $this->getProvider();
        $listing  = Listing::findOne(['id' => $id, 'provider_id' => $provider->id]);
        if ($listing) {
            $listing->status = Listing::STATUS_INACTIVE;
            $listing->save(false);
            Yii::$app->session->setFlash('success', 'Listing removed.');
        }
        return $this->redirect(['/provider/listings']);
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function actionOrders()
    {
        $this->view->title = 'My Orders';
        $provider = $this->getProvider();
        $orders   = Order::find()->with(['user', 'items'])
            ->where(['provider_id' => $provider->id])
            ->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('orders', compact('provider', 'orders'));
    }

    public function actionOrderView(int $id)
    {
        $this->view->title = 'Order Detail';
        $provider = $this->getProvider();
        $order = Order::find()->with(['user', 'provider', 'items', 'items.listing'])->where(['id' => $id, 'provider_id' => $provider->id])->one();
        if (!$order) throw new NotFoundHttpException('Order not found.');
        $payment = Payment::findOne(['order_id' => $id]);
        return $this->render('order-view', compact('order', 'payment', 'provider'));
    }

    public function actionAcceptOrder(int $id)
    {
        $provider = $this->getProvider();
        $order    = Order::findOne(['id' => $id, 'provider_id' => $provider->id]);
        if ($order && $order->status === Order::STATUS_AWAITING_PAYMENT) {
            $order->status = Order::STATUS_PROCESSING;
            $order->save(false);
            Yii::$app->sms->orderAccepted($order->user, $order);
            Yii::$app->session->setFlash('success', 'Order accepted.');
        }
        return $this->redirect(['/provider/orders']);
    }

    public function actionCompleteOrder(int $id)
    {
        $provider = $this->getProvider();
        $order    = Order::findOne(['id' => $id, 'provider_id' => $provider->id]);
        if ($order && in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_OUT_FOR_DELIVERY])) {
            $order->status = Order::STATUS_COMPLETED;
            $order->save(false);
            // Record commission
            $commission = \common\models\Commission::createFromOrder($order);
            $commission->save(false);
            Yii::$app->sms->orderCompleted($order->user, $order);
            Yii::$app->session->setFlash('success', 'Order marked complete!');
        }
        return $this->redirect(['/provider/orders']);
    }

    // ── Subscription ──────────────────────────────────────────────────────────

    public function actionSubscription()
    {
        $this->view->title = 'My Subscription';
        $provider = $this->getProvider();
        $plans    = SubscriptionPlan::activePlans();
        $current  = Subscription::find()->with(['plan'])
            ->where(['provider_id' => $provider->id, 'status' => Subscription::STATUS_ACTIVE])
            ->orderBy(['end_date' => SORT_DESC])->one();
        return $this->render('subscription', compact('provider', 'plans', 'current'));
    }

    public function actionSubscribePlan(int $planId)
    {
        $this->view->title = 'Subscribe to Plan';
        $provider = $this->getProvider();
        $plan     = SubscriptionPlan::findOne($planId);
        if (!$plan) throw new NotFoundHttpException('Plan not found.');

        if (Yii::$app->request->isPost) {
            $phone = Yii::$app->request->post('phone', Yii::$app->user->identity->phone);

            // Create a placeholder order reference for subscription payment
            $order           = new \common\models\Order();
            $order->user_id  = Yii::$app->user->id;
            $order->provider_id = $provider->id;
            $order->total_amount = $plan->price_kes;
            $order->payment_type = Order::PAYMENT_FULL;
            $order->status       = Order::STATUS_AWAITING_PAYMENT;
            $order->payment_status = Order::PAYMENT_STATUS_UNPAID;
            $order->notes    = 'Subscription: ' . $plan->name;
            $order->save(false);

            $result = Yii::$app->mpesa->stkPush($order, $phone, $plan->price_kes, \common\models\Payment::STAGE_FULL);
            if ($result['success']) {
                Yii::$app->session->set('sub_plan_id', $planId);
                Yii::$app->session->set('sub_order_id', $order->id);
                Yii::$app->session->setFlash('success', 'M-Pesa prompt sent! Enter your PIN to subscribe.');
            } else {
                Yii::$app->session->setFlash('danger', $result['message']);
            }
            return $this->redirect(['/provider/subscription']);
        }

        return $this->render('subscribe-plan', compact('provider', 'plan'));
    }

    // ── Profile ───────────────────────────────────────────────────────────────

    public function actionEarnings()
    {
        $this->view->title = 'Earnings & Commissions';
        $provider = $this->getProvider();
        $commissions = Commission::find()
            ->with(['order'])
            ->where(['provider_id' => $provider->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('earnings', compact('provider', 'commissions'));
    }

    public function actionProfile()
    {
        $this->view->title = 'My Business Profile';
        $provider = $this->getProvider();

        if ($provider->load(Yii::$app->request->post())) {
            $logo = UploadedFile::getInstance($provider, 'logoFile');
            if ($logo) {
                $dir = Yii::getAlias('@uploads') . '/logos/';
                @mkdir($dir, 0755, true);
                $fn = 'logo-' . $provider->id . '.' . $logo->extension;
                $logo->saveAs($dir . $fn);
                $provider->logo = 'logos/' . $fn;
            }
            if ($provider->save()) {
                Yii::$app->session->setFlash('success', 'Profile updated!');
                return $this->refresh();
            }
        }

        return $this->render('profile', compact('provider'));
    }

    // ── Reviews ───────────────────────────────────────────────────────────────

    public function actionReviews()
    {
        $this->view->title = 'My Reviews';
        $provider = $this->getProvider();
        $reviews  = Review::find()->with(['user', 'order'])
            ->where(['provider_id' => $provider->id, 'status' => Review::STATUS_VISIBLE])
            ->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('reviews', compact('provider', 'reviews'));
    }

    // ── Missing Modules ───────────────────────────────────────────────────────

    public function actionSettings()
    {
        $this->view->title = 'Settings';
        return $this->render('settings');
    }

    public function actionMessages()
    {
        $this->view->title = 'Messages';
        return $this->render('messages');
    }

    public function actionPayouts()
    {
        $this->view->title = 'My Payouts & Earnings';
        $provider = $this->getProvider();
        
        $stats = [
            'total_earned'    => (float)Commission::find()->where(['provider_id' => $provider->id])->sum('amount') ?? 0,
            'pending_payouts' => (float)Commission::find()->where(['provider_id' => $provider->id, 'status' => Commission::STATUS_PENDING])->sum('amount') ?? 0,
            'paid_out'        => (float)Commission::find()->where(['provider_id' => $provider->id, 'status' => Commission::STATUS_PAID])->sum('amount') ?? 0,
        ];
        
        $query = Commission::find()->with(['provider', 'order'])->where(['provider_id' => $provider->id])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 20]]);
        
        return $this->render('payouts', compact('stats', 'dataProvider'));
    }

    public function actionAnalytics()
    {
        $this->view->title = 'Analytics & Insights';
        return $this->render('analytics');
    }

    // ── Notifications ─────────────────────────────────────────────────────────

    public function actionNotifications()
    {
        $this->view->title = 'Notifications';
        $provider = $this->getProvider();
        $user = $provider->user;
        
        $stats = [
            'total'       => Notification::find()->where(['user_id' => $user->id])->count(),
            'unread'      => Notification::find()->where(['user_id' => $user->id, 'is_read' => false])->count(),
        ];
        
        $query = Notification::find()->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => ['pageSize' => 15]]);
        
        return $this->render('notifications', compact('stats', 'dataProvider'));
    }
    
    public function actionMarkAllRead()
    {
        $provider = $this->getProvider();
        Notification::updateAll(['is_read' => true], ['user_id' => $provider->user->id, 'is_read' => false]);
        return $this->redirect(['/provider/notifications']);
    }
}
