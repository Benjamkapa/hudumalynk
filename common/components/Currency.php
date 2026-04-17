<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\Cookie;

/**
 * Currency component to handle switching and formatting
 */
class Currency extends Component
{
    private $_current;

    /**
     * Get current currency code (KES or USD)
     */
    public function getCurrent(): string
    {
        if ($this->_current === null) {
            $this->_current = Yii::$app->request->cookies->getValue('hl_currency') 
                           ?? Yii::$app->session->get('hl_currency') 
                           ?? Yii::$app->params['defaultCurrency'] 
                           ?? 'KES';
        }
        return $this->_current;
    }

    /**
     * Set current currency
     */
    public function set(string $code): void
    {
        $code = strtoupper($code);
        if (in_array($code, Yii::$app->params['supportedCurrencies'])) {
            $this->_current = $code;
            Yii::$app->session->set('hl_currency', $code);
            Yii::$app->response->cookies->add(new Cookie([
                'name' => 'hl_currency',
                'value' => $code,
                'expire' => time() + 3600 * 24 * 30, // 30 days
            ]));
        }
    }

    /**
     * Convert KES amount to current currency
     */
    public function convert(float $amountInKes): float
    {
        if ($this->getCurrent() === 'KES') {
            return $amountInKes;
        }
        $rate = Yii::$app->params['kesToUsdRate'] ?? 0.0077;
        return $amountInKes * $rate;
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amountInKes): string
    {
        $converted = $this->convert($amountInKes);
        $code = $this->getCurrent();
        
        if ($code === 'USD') {
            return '$' . number_format($converted, 2);
        }
        
        return 'KES ' . number_format($converted, 0);
    }

    /**
     * Get both formatted strings (e.g. for tooltips or details)
     */
    public function both(float $amountInKes): array
    {
        $rate = Yii::$app->params['kesToUsdRate'] ?? 0.0077;
        return [
            'KES' => 'KES ' . number_format($amountInKes, 0),
            'USD' => '$' . number_format($amountInKes * $rate, 2),
        ];
    }
}
