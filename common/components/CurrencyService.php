<?php

namespace common\components;

use Yii;
use yii\base\Component;

/**
 * CurrencyService — KES ↔ USD conversion with live rate caching
 *
 * Usage:
 *   Yii::$app->currency->toUsd(5000)         // "38.50"
 *   Yii::$app->currency->format(5000, 'USD') // "USD 38.50"
 *   Yii::$app->currency->format(5000, 'KES') // "KES 5,000.00"
 */
class CurrencyService extends Component
{
    public string $baseCurrency    = 'KES';
    public float  $kesToUsdRate    = 0.0077;   // fallback static rate
    public string $liveApiKey      = '';        // optional: currencylayer / openexchangerates
    public int    $cacheTtlSeconds = 3600;     // refresh rate every hour

    public function init(): void
    {
        parent::init();
        $this->kesToUsdRate = (float)($_ENV['KES_TO_USD_RATE'] ?? $this->kesToUsdRate);
        $this->liveApiKey   = $_ENV['CURRENCY_API_KEY'] ?? '';
    }

    // ── Preference handling ───────────────────────────────────────────────────

    /**
     * Get the current user's preferred display currency (KES or USD)
     */
    public function getDisplayCurrency(): string
    {
        return Yii::$app->request->cookies->getValue('hl_currency') 
            ?? Yii::$app->session->get('hl_currency') 
            ?? $this->baseCurrency;
    }

    /**
     * Set the user's preferred display currency
     */
    public function setDisplayCurrency(string $code): void
    {
        $code = strtoupper($code);
        if (in_array($code, ['KES', 'USD'])) {
            Yii::$app->session->set('hl_currency', $code);
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name'   => 'hl_currency',
                'value'  => $code,
                'expire' => time() + 3600 * 24 * 30, // 30 days
            ]));
        }
    }

    // ── Rate resolution ───────────────────────────────────────────────────────

    public function getRate(): float
    {
        $cached = Yii::$app->cache->get('currency_kes_usd_rate');
        if ($cached !== false) {
            return (float)$cached;
        }

        $rate = $this->fetchLiveRate();
        Yii::$app->cache->set('currency_kes_usd_rate', $rate, $this->cacheTtlSeconds);
        return $rate;
    }

    private function fetchLiveRate(): float
    {
        if (empty($this->liveApiKey)) {
            return $this->kesToUsdRate;
        }

        try {
            // Using ExchangeRate-API (free tier) if key is supplied
            $url = "https://v6.exchangerate-api.com/v6/{$this->liveApiKey}/pair/KES/USD";
            $ch  = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
            ]);
            $json = curl_exec($ch);
            $data = json_decode($json, true);
            if (isset($data['conversion_rate'])) {
                return (float)$data['conversion_rate'];
            }
        } catch (\Exception $e) {
            Yii::warning('[Currency] Live rate fetch failed: ' . $e->getMessage(), 'currency');
        }

        return $this->kesToUsdRate;
    }

    // ── Conversion helpers ────────────────────────────────────────────────────

    public function toUsd(float $amountKes): float
    {
        return round($amountKes * $this->getRate(), 2);
    }

    public function toKes(float $amountUsd): float
    {
        $rate = $this->getRate();
        return $rate > 0 ? round($amountUsd / $rate, 2) : 0;
    }

    // ── Formatting ────────────────────────────────────────────────────────────

    /**
     * Format an amount (always stored as KES) in the chosen display currency.
     *
     * @param float  $amountKes  Amount in KES (as stored in DB)
     * @param string $currency   'KES' or 'USD'
     * @param bool   $symbol     Prepend currency symbol
     */
    public function format(float $amountKes, ?string $currency = null, bool $symbol = true): string
    {
        $currency = $currency ?? $this->getDisplayCurrency();
        if ($currency === 'USD') {
            $amount = $this->toUsd($amountKes);
            return ($symbol ? 'USD ' : '') . number_format($amount, 2);
        }
        return ($symbol ? 'KES ' : '') . number_format($amountKes, 2);
    }

    /**
     * Returns both formatted strings for the KES/USD toggle UI.
     *
     * @param float $amountKes
     * @return array ['KES' => 'KES 5,000.00', 'USD' => 'USD 38.50', 'rate' => 0.0077]
     */
    public function both(float $amountKes): array
    {
        return [
            'KES'  => $this->format($amountKes, 'KES'),
            'USD'  => $this->format($amountKes, 'USD'),
            'rate' => $this->getRate(),
        ];
    }
}
