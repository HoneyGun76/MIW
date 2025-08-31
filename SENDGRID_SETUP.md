# SendGrid Email Service Setup for Railway

## 🚀 SendGrid Configuration

To complete the email service setup, you need to:

### 1. Create SendGrid Account
1. Go to https://sendgrid.com/
2. Sign up for a free account (100 emails/day free tier)
3. Verify your account

### 2. Create API Key
1. Login to SendGrid Console
2. Go to Settings → API Keys
3. Click "Create API Key"
4. Choose "Restricted Access"
5. Give it a name: "MIW-Railway-Production"
6. Grant permissions:
   - Mail Send: FULL ACCESS
   - Mail Settings: READ ACCESS
7. Copy the generated API key

### 3. Set Railway Environment Variable
Run this command with your actual SendGrid API key:

```bash
railway variables --set "SMTP_PASSWORD=your-sendgrid-api-key-here"
```

Replace `your-sendgrid-api-key-here` with the actual API key from SendGrid.

### 4. Verify Domain (Recommended)
1. In SendGrid Console, go to Settings → Sender Authentication
2. Verify your domain (miw.id) or at minimum verify a single sender email
3. This improves deliverability rates

### 5. Update FROM Email Address
Once domain is verified, update the environment variable:

```bash
railway variables --set "SMTP_USERNAME=noreply@miw.id"
```

Or keep using your Gmail address if you prefer sender email to be from Gmail.

## 📋 Current Configuration Status

✅ SMTP_HOST: smtp.sendgrid.net
✅ SMTP_USERNAME: apikey  
❌ SMTP_PASSWORD: **NEEDS TO BE SET**
✅ SMTP_PORT: 587
✅ SMTP_ENCRYPTION: tls

## 🔧 Configuration Applied

Updated `config.php` to use SendGrid as default for Railway environment:
- Fallback from Gmail to SendGrid for better Railway compatibility
- Maintains Gmail for local development
- Enhanced error handling and debugging

## 🧪 Testing

After setting the API key, test the email service:
1. Deploy the changes: `railway up`
2. Visit: https://miw.id/test_email.php (if you upload the test file)
3. Check Railway logs: `railway logs -s web`

## 📞 Support

SendGrid provides excellent documentation and support for integration issues.
Railway also has specific guides for SendGrid integration.
