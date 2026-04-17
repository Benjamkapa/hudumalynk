<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\Listing;
use common\models\Category;

class BrowseController extends Controller
{
    public $layout = 'main';

    public function actionIndex()
    {
        $this->view->title = 'Browse Services & Products';
        $request = Yii::$app->request;

        $query = Listing::find()
            ->with(['provider', 'category', 'images'])
            ->where(['status' => Listing::STATUS_ACTIVE]);

        // Search
        if ($q = $request->get('q')) {
            $query->andWhere(['or',
                ['like', 'listings.name', $q],
                ['like', 'listings.description', $q],
            ]);
        }
        // Type filter
        if ($type = $request->get('type')) {
            $query->andWhere(['listings.type' => $type]);
        }
        // Category filter
        if ($cat = $request->get('category')) {
            $catModel = Category::findOne(['slug' => $cat]);
            if ($catModel) {
                $query->andWhere(['listings.category_id' => $catModel->id]);
            }
        }
        // Price filter
        if ($minPrice = $request->get('min_price')) {
            $query->andWhere(['>=', 'listings.price', (float)$minPrice]);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->andWhere(['<=', 'listings.price', (float)$maxPrice]);
        }
        // Sort
        switch ($request->get('sort')) {
            case 'price_asc':  $query->orderBy(['listings.price' => SORT_ASC]); break;
            case 'price_desc': $query->orderBy(['listings.price' => SORT_DESC]); break;
            case 'featured':   $query->orderBy(['listings.is_featured' => SORT_DESC, 'listings.created_at' => SORT_DESC]); break;
            default:           $query->orderBy(['listings.is_featured' => SORT_DESC, 'listings.created_at' => SORT_DESC]);
        }

        $provider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 16],
        ]);

        $categories = Category::topLevel();

        return $this->render('index', [
            'dataProvider' => $provider,
            'categories'   => $categories,
            'currentType'  => $request->get('type'),
            'currentQ'     => $request->get('q'),
        ]);
    }

    public function actionCategory(string $slug)
    {
        $cat = Category::findOne(['slug' => $slug, 'status' => Category::STATUS_ACTIVE]);
        if (!$cat) throw new NotFoundHttpException('Category not found.');
        return $this->redirect(['/browse', 'category' => $slug]);
    }
}
