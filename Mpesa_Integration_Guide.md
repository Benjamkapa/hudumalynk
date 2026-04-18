## M-Pesa Setup Process

### Step 1: Get M-Pesa Daraja API Credentials

**You need to register for M-Pesa Daraja API access:**

1. **Visit the Safaricom Developer Portal**: https://developer.safaricom.co.ke/
2. **Create an account** and verify your business details
3. **Create a new app** in the developer portal
4. **Get your credentials**:
   - **Consumer Key** (App Consumer Key)
   - **Consumer Secret** (App Consumer Secret)
   - **Shortcode** (your PayBill/Till number)
   - **Passkey** (for STK Push - provided in the portal)

**For sandbox testing** (recommended first):
- Use the default sandbox credentials initially
- Shortcode: `174379`
- Passkey: `bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919`

### Step 2: Configure Environment Variables

**Update your .env file** with the M-Pesa credentials:

```bash
# For Sandbox Testing (start here)
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_sandbox_consumer_key
MPESA_CONSUMER_SECRET=your_sandbox_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
MPESA_CALLBACK_URL=https://yourdomain.com/order/mpesa-callback
MPESA_ACCOUNT_REF=HudumaLynk

# For Production (after testing)
MPESA_ENV=production
MPESA_CONSUMER_KEY=your_production_consumer_key
MPESA_CONSUMER_SECRET=your_production_consumer_secret
MPESA_SHORTCODE=your_actual_paybill_number
MPESA_PASSKEY=your_production_passkey
MPESA_CALLBACK_URL=https://yourdomain.com/order/mpesa-callback
```

### Step 3: Set Up Public Callback URL

**M-Pesa requires a public HTTPS endpoint for callbacks.** The system expects the callback at `/order/mpesa-callback`.

**For local development:**
- Use ngrok: `ngrok http 8080` (your frontend port)
- Update `MPESA_CALLBACK_URL` to: `https://your-ngrok-url.ngrok-free.app/order/mpesa-callback`

**For production:**
- Your domain must have SSL/TLS certificate
- Callback URL: `https://yourdomain.com/order/mpesa-callback`

### Step 4: Configure Admin Settings (Optional Override)

**The system allows runtime configuration via the admin panel:**

1. **Login to admin dashboard** at `http://localhost:8081`
2. **Go to Settings** (`/admin/settings`)
3. **Add custom settings** for M-Pesa (these override .env values):
   - `mpesa_shortcode`: Your PayBill number
   - `mpesa_passkey`: STK Push passkey
   - `mpesa_consumer_key`: API consumer key
   - `mpesa_consumer_secret`: API consumer secret
   - `mpesa_callback_url`: Public callback URL

### Step 5: Test the Integration

**Start your servers:**
```bash
# Frontend (customer payments)
php yii serve --docroot=frontend/web --port=8080

# Backend (admin/provider)
php yii serve --docroot=backend/web --port=8081
```

**Test payment flow:**
1. **Create an order** as a customer
2. **Go to payment page** (`/order/pay?id=X`)
3. **Select M-Pesa** tab
4. **Enter phone number** (format: 254XXXXXXXXX)
5. **Click "Send M-Pesa Prompt"**
6. **Check your phone** for STK Push notification
7. **Enter M-Pesa PIN** to complete payment

### Step 6: Verify Payment Processing

**Check payment logs:**
- **Admin dashboard**: View orders and payment status
- **Database**: Check `payments` table for transaction records
- **Logs**: Check app.log for M-Pesa API calls

**Expected flow:**
1. **STK Push sent** → Payment status: `pending`
2. **User enters PIN** → M-Pesa processes payment
3. **Callback received** → Payment status: `completed`
4. **Order status updates** → `awaiting_payment` → `processing`

### Step 7: Go Live (Production Setup)

**When ready for production:**

1. **Update .env**:
   ```bash
   MPESA_ENV=production
   MPESA_CONSUMER_KEY=your_production_key
   MPESA_CONSUMER_SECRET=your_production_secret
   MPESA_SHORTCODE=your_paybill_number
   ```

2. **Ensure HTTPS** is properly configured on your domain

3. **Test with small amounts** before full deployment

4. **Monitor callback logs** for any issues

### Troubleshooting Common Issues

**"Could not connect to M-Pesa"**:
- Check internet connection
- Verify API credentials
- Ensure correct environment (sandbox vs production)

**"Callback not received"**:
- Verify callback URL is publicly accessible
- Check HTTPS certificate validity
- Confirm URL format: `https://domain.com/order/mpesa-callback`

**"Invalid phone number"**:
- Must be in format: `254XXXXXXXXX` (no + or spaces)
- Use Kenyan mobile numbers only

**Payment stuck on "pending"**:
- User didn't enter PIN within time limit
- Network issues during transaction
- Check M-Pesa account balance

### Additional Configuration Options

**Payment Limits:**
- M-Pesa has transaction limits (KES 150,000 max per transaction)
- Daily limits apply based on your account type

**Commission Settings:**
- Configure platform commission rate in admin settings
- Default: 10% (configurable via `commission_rate` setting)

**SMS Notifications:**
- Configure Africa's Talking credentials for payment confirmations
- Update .env with your AT API key

The system is fully integrated with M-Pesa and handles the complete payment lifecycle from initiation to completion. Start with sandbox testing to ensure everything works before going live!