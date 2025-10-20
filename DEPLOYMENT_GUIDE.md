# DigitalOcean Deployment Guide for My Friend Hazel
## Domain: app.myfriendhazel.com

### Overview
This guide will help you deploy your Laravel + Vue.js application to DigitalOcean App Platform using GitHub integration. This is the **easiest and most optimized** approach for your setup.

---

## Prerequisites Checklist
- [ ] GitHub account with repository access
- [ ] DigitalOcean account
- [ ] Domain `myfriendhazel.com` managed in DigitalOcean DNS
- [ ] Supabase project created
- [ ] DigitalOcean Spaces bucket created

---

## Step 1: Prepare Your GitHub Repository

### 1.1 Create GitHub Repository
```bash
# In your project directory
git init
git add .
git commit -m "Initial commit - My Friend Hazel v2.0"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/my-friend-hazel.git
git push -u origin main
```

### 1.2 Add .gitignore (if not present)
Create `.gitignore` in your project root:
```gitignore
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode
```

### 1.3 Create Production Environment Template
Create `.env.production` (DO NOT commit this):
```env
APP_NAME="My Friend Hazel"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://app.myfriendhazel.com

LOG_CHANNEL=stack
LOG_LEVEL=info

SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Database (SQLite for simplicity)
DB_CONNECTION=sqlite
DB_DATABASE=/workspace/database/database.sqlite

# Supabase Configuration
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
SUPABASE_ANON_KEY=your_anon_key_here
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key_here

# DigitalOcean Spaces
DIGITALOCEAN_SPACES_KEY=your_spaces_key_here
DIGITALOCEAN_SPACES_SECRET=your_spaces_secret_here
DIGITALOCEAN_SPACES_REGION=nyc3
DIGITALOCEAN_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DIGITALOCEAN_SPACES_BUCKET=hazel-audio-clips
DIGITALOCEAN_SPACES_BASE_PATH=livekit/audio_transcripts

# Mail Configuration (for magic links)
MAIL_MAILER=smtp
MAIL_HOST=smtp.postmarkapp.com
MAIL_PORT=587
MAIL_USERNAME=your_postmark_token
MAIL_PASSWORD=your_postmark_token
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@myfriendhazel.com
MAIL_FROM_NAME="${APP_NAME}"

# Vite Configuration
VITE_APP_URL=https://app.myfriendhazel.com
```

---

## Step 2: Configure DigitalOcean DNS

### 2.1 Add Subdomain Record
1. Go to DigitalOcean → Networking → Domains
2. Select `myfriendhazel.com`
3. Add new record:
   - **Type**: A
   - **Name**: app
   - **Value**: Will be set after App Platform deployment
   - **TTL**: 3600

### 2.2 Note the DNS Record
- You'll update the A record value after creating the App Platform app

---

## Step 3: Create DigitalOcean Spaces

### 3.1 Create Spaces Bucket
1. Go to DigitalOcean → Spaces
2. Create new Space:
   - **Name**: `hazel-audio-clips`
   - **Region**: New York (nyc3)
   - **File Listing**: Restricted (recommended)
3. Note down the **Access Key** and **Secret Key**

### 3.2 Configure CORS
1. Go to your Space → Settings → CORS
2. Add CORS rule:
```json
{
  "AllowedOrigins": ["https://app.myfriendhazel.com"],
  "AllowedMethods": ["GET", "HEAD"],
  "AllowedHeaders": ["*"],
  "MaxAgeSeconds": 3600
}
```

### 3.3 Create Folder Structure
Create these folders in your Space:
```
livekit/
└── audio_transcripts/
    ├── 6/          # Account ID 6 (mtaimoorhas1@gmail.com)
    │   ├── -1/     # Main profile
    │   └── 15/     # Elderly profile
    └── 7/          # Account ID 7 (microassetsmain@gmail.com)
        └── -1/     # Main profile
```

---

## Step 4: Configure Supabase

### 4.1 Update Authentication Settings
1. Go to Supabase → Authentication → URL Configuration
2. Set:
   - **Site URL**: `https://app.myfriendhazel.com`
   - **Redirect URLs**: 
     - `https://app.myfriendhazel.com/login`
     - `https://app.myfriendhazel.com/confirm`
     - `https://app.myfriendhazel.com/auth/callback`

### 4.2 Get API Keys
1. Go to Supabase → Settings → API
2. Copy:
   - **Project URL** (SUPABASE_URL)
   - **anon public** key (SUPABASE_ANON_KEY)
   - **service_role** key (SUPABASE_SERVICE_ROLE_KEY)

---

## Step 5: Deploy to DigitalOcean App Platform

### 5.1 Create New App
1. Go to DigitalOcean → Apps → Create App
2. Choose **GitHub** as source
3. Select your repository: `my-friend-hazel`
4. Choose branch: `main`
5. Click **Next**

### 5.2 Configure Build Settings
**Build Command**:
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Run Command**: Leave default (App Platform handles this)

### 5.3 Set Environment Variables
In the App Platform interface, add these environment variables (mark as **Encrypted**):

**Core App**:
```
APP_NAME=My Friend Hazel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.myfriendhazel.com
```

**Supabase**:
```
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
SUPABASE_ANON_KEY=your_anon_key_here
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key_here
```

**DigitalOcean Spaces**:
```
DIGITALOCEAN_SPACES_KEY=your_spaces_key_here
DIGITALOCEAN_SPACES_SECRET=your_spaces_secret_here
DIGITALOCEAN_SPACES_REGION=nyc3
DIGITALOCEAN_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DIGITALOCEAN_SPACES_BUCKET=hazel-audio-clips
DIGITALOCEAN_SPACES_BASE_PATH=livekit/audio_transcripts
```

**Database**:
```
DB_CONNECTION=sqlite
DB_DATABASE=/workspace/database/database.sqlite
```

**Session & Cache**:
```
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

**Mail** (if using Postmark):
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.postmarkapp.com
MAIL_PORT=587
MAIL_USERNAME=your_postmark_token
MAIL_PASSWORD=your_postmark_token
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@myfriendhazel.com
MAIL_FROM_NAME=My Friend Hazel
```

**Vite**:
```
VITE_APP_URL=https://app.myfriendhazel.com
```

### 5.4 Configure Resources
**Web Service**:
- **Instance Size**: Basic ($5/month) to start
- **Instance Count**: 1
- **HTTP Port**: 8080 (default)
- **Health Check**: `/` (expects 200)

**Optional Worker** (for queues):
- **Command**: `php artisan queue:work --sleep=3 --tries=3 --no-interaction`
- **Instance Size**: Basic ($5/month)
- **Instance Count**: 1

### 5.5 Set Custom Domain
1. In App Platform → Settings → Domains
2. Add custom domain: `app.myfriendhazel.com`
3. DigitalOcean will provide DNS records to add

### 5.6 Update DNS Record
1. Go back to DigitalOcean → Networking → Domains
2. Update the A record for `app.myfriendhazel.com` with the IP provided by App Platform
3. Wait for DNS propagation (5-15 minutes)

---

## Step 6: Deploy and Test

### 6.1 Deploy
1. Click **Create Resources** in App Platform
2. Wait for build to complete (5-10 minutes)
3. Check build logs for any errors

### 6.2 Test Deployment
1. Visit `https://app.myfriendhazel.com`
2. Test login with Supabase magic link
3. Verify dashboard loads with health analytics
4. Test API endpoints:
   - `https://app.myfriendhazel.com/api/test-supabase-connection`
   - `https://app.myfriendhazel.com/api/realtime-sync/profile-data`

### 6.3 Verify Data Flow
1. Check that health metrics display correctly
2. Verify "Add Elderly Profile" button appears for caregivers
3. Test that each user sees their own data (Account ID 6 vs 7)

---

## Step 7: Post-Deployment Configuration

### 7.1 Enable Auto-Deployments
1. In App Platform → Settings → GitHub
2. Enable **Auto Deploy** on push to `main` branch

### 7.2 Set Up Monitoring
1. Go to App Platform → Monitoring
2. Set up alerts for:
   - High CPU usage
   - High memory usage
   - Failed deployments

### 7.3 Configure Logs
1. App Platform automatically collects logs
2. Access via App Platform → Runtime Logs
3. For persistent logs, consider upgrading to a Droplet

---

## Step 8: Production Optimizations

### 8.1 Performance
- **CDN**: App Platform includes CDN automatically
- **Caching**: Laravel config/route/view caching is enabled
- **Assets**: Vite build optimizes frontend assets

### 8.2 Security
- **HTTPS**: Automatically enabled by App Platform
- **Environment Variables**: Marked as encrypted
- **Supabase**: Service role key kept secure

### 8.3 Scaling
- **Horizontal**: Increase instance count in App Platform
- **Vertical**: Upgrade instance size
- **Database**: Consider managed database for high traffic

---

## Troubleshooting

### Common Issues

**Build Fails**:
- Check build logs in App Platform
- Ensure all dependencies are in `composer.json` and `package.json`
- Verify environment variables are set correctly

**App Won't Start**:
- Check runtime logs
- Verify `APP_KEY` is generated
- Ensure database file exists and is writable

**Supabase Connection Issues**:
- Verify `SUPABASE_URL` and keys are correct
- Check redirect URLs in Supabase dashboard
- Ensure CORS is configured properly

**DigitalOcean Spaces Issues**:
- Verify Spaces credentials
- Check CORS configuration
- Ensure bucket and folder structure exists

**DNS Issues**:
- Wait for DNS propagation (up to 24 hours)
- Use `dig app.myfriendhazel.com` to check DNS
- Verify A record points to correct IP

### Getting Help
1. Check App Platform logs first
2. Verify all environment variables
3. Test API endpoints individually
4. Check Supabase and Spaces configurations

---

## Cost Estimation

**Monthly Costs**:
- **App Platform Basic**: $5/month (web service)
- **App Platform Basic**: $5/month (worker, optional)
- **Spaces**: $5/month (250GB storage)
- **Total**: ~$10-15/month

**Scaling Costs**:
- **Professional**: $12/month per service
- **Pro**: $24/month per service
- **Managed Database**: $15/month (if needed)

---

## Maintenance

### Regular Tasks
1. **Monitor logs** for errors
2. **Update dependencies** monthly
3. **Backup database** (if using managed DB)
4. **Review costs** and optimize resources

### Updates
1. Push changes to `main` branch
2. App Platform auto-deploys
3. Monitor deployment logs
4. Test functionality after deployment

---

## Success Checklist

- [ ] GitHub repository created and code pushed
- [ ] DigitalOcean DNS configured for `app.myfriendhazel.com`
- [ ] Supabase project configured with correct URLs
- [ ] DigitalOcean Spaces bucket created with CORS
- [ ] App Platform app created with all environment variables
- [ ] Custom domain added and DNS updated
- [ ] Application deployed successfully
- [ ] Login and dashboard functionality tested
- [ ] Health analytics displaying correctly
- [ ] User data isolation verified
- [ ] Auto-deployments enabled

**Your application is now live at: https://app.myfriendhazel.com** 🎉

---

## Support

If you encounter issues:
1. Check this guide first
2. Review App Platform logs
3. Verify all configurations match this guide
4. Test each component individually

**Remember**: This setup provides automatic deployments, SSL certificates, and scaling capabilities with minimal maintenance required.
