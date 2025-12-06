# Administrator Guide

## Accessing the Admin Panel

1. Login with an admin account
2. Click **Admin Panel** in the sidebar (red button)
3. Access URL: `https://your-domain.com/admin`

## Dashboard Overview

The admin dashboard displays key metrics:

### Statistics Cards
- **Total Users**: All registered users
- **Pending KYC**: Applications awaiting review
- **Active Leases**: Currently running leases
- **Monthly Revenue**: Current month earnings

### Quick Actions
- View pending KYC applications
- Check unverified subnets
- Process pending payouts

## User Management

### Viewing Users

1. Go to **Admin Panel** > **Users**
2. View list with filters:
   - **Role**: All, User, Admin
   - **Status**: All, Active, Inactive, Suspended
   - **KYC Status**: All, Pending, Approved, Rejected
3. Search by name, email, or company name

### User Actions

**View User Details**
- Click on a user to see:
  - Account information
  - Company profile
  - KYC status
  - Subnets owned
  - Active leases
  - Invoice history

**Edit User**
- Modify user information
- Change role (user/admin)
- Update status
- Reset password

**Suspend User**
- Temporarily disable account
- User cannot login
- All features disabled
- Can be reactivated

**Activate User**
- Reactivate suspended account
- Restore full access

**Verify Email**
- Manually verify user's email
- Useful for support cases

**Impersonate User**
- Login as the user
- See what they see
- Debug issues
- Click "Stop Impersonating" to return

**Delete User**
- Permanently remove account
- Cannot delete last admin
- Cannot delete yourself
- Cascades to related data

### Creating Users

1. Click **Create User**
2. Fill in:
   - Name
   - Email
   - Password
   - Role (User/Admin)
   - Status
   - Verify email option
3. Click **Create User**

## KYC Management

### KYC Review Process

1. Go to **Admin Panel** > **KYC Management**
2. View pending applications
3. Filter by status:
   - **Pending**: Initial state
   - **In Review**: Documents submitted
   - **Approved**: Verified
   - **Rejected**: Denied

### Reviewing a KYC Application

1. Click **Review** on an application
2. Review company information:
   - Company name and legal name
   - Tax ID
   - Address
   - Entity type (individual/company)
3. Check uploaded documents:
   - **Identity Document**: Click "View" to open
   - **Signed KYC Form**: Click "View" to open
4. For companies, verify:
   - Legal representative information
   - Position/title

### KYC Actions

**Approve KYC**
1. Review all documents
2. Verify information matches documents
3. Add optional notes
4. Click **Approve KYC**
5. User is notified by email

**Reject KYC**
1. Identify issues with application
2. Provide rejection reason (required)
3. Click **Reject KYC**
4. User is notified with reason
5. User can resubmit

**Request Information**
1. If documents are incomplete
2. Specify what's needed
3. Click **Request Info**
4. User is notified
5. Status changes to pending

### KYC Verification Checklist

- [ ] Company name matches documents
- [ ] Tax ID is valid format
- [ ] Identity document is clear and readable
- [ ] Document is not expired
- [ ] Signed KYC form has signature
- [ ] For companies: legal representative matches company records
- [ ] Address is complete and verifiable

## Subnet Management

### Viewing Subnets

1. Go to **Admin Panel** > **Subnets**
2. View all subnets with filters:
   - Status (pending, verified, available, leased, suspended)
   - RIR (RIPE, ARIN, APNIC, etc.)
3. Search by network address or company

### Subnet Actions

**Verify Subnet**
- Manually verify ownership
- Bypasses email verification
- Use for support escalations

**Check Reputation**
- Run AbuseIPDB check
- View abuse score
- See recent reports

**Suspend Subnet**
- Remove from marketplace
- Active leases continue
- Cannot be leased
- Owner notified

**Unsuspend Subnet**
- Restore to marketplace
- Make available for leasing

**Bulk Reputation Check**
- Select multiple subnets
- Run batch reputation check
- Review results

### Subnet Details

View subnet page shows:
- Network information (CIDR, IP count)
- Owner company
- Pricing
- WHOIS data
- Reputation score
- Lease history

## Lease Management

### Viewing Leases

1. Go to **Admin Panel** > **Leases**
2. View all leases with filters:
   - Status (pending, active, expired, terminated)
   - Date range
3. Search by subnet or company

### Lease Actions

**View Lease**
- See full lease details
- Holder and lessee information
- LOA records
- Invoice history

**Extend Lease**
- Add additional months
- Updates end date
- Generates new invoice

**Terminate Lease**
- End lease immediately
- Provide reason
- Subnet returns to available
- Prorated refund may apply

### Lease Details

Lease page shows:
- Subnet information
- Holder company
- Lessee company
- Start and end dates
- Monthly and total price
- Assigned ASN
- LOA documents
- Related invoices

## Financial Management

### Invoices

1. Go to **Admin Panel** > **Finance** > **Invoices**
2. View all invoices:
   - Pending
   - Paid
   - Overdue
   - Cancelled
3. Search by invoice number or company

**Invoice Actions**
- View details
- Mark as paid
- Download PDF
- Cancel invoice

### Payouts

1. Go to **Admin Panel** > **Finance** > **Payouts**
2. View all payouts:
   - Pending
   - Processing
   - Completed
   - Failed

**Processing Payouts**

1. Review pending payout
2. Verify holder information
3. Check payout details (bank account, etc.)
4. Click **Process** to start
5. Execute the payment externally
6. Click **Complete** and enter reference
7. Or click **Failed** if issues

### Revenue Report

1. Go to **Admin Panel** > **Finance** > **Revenue**
2. View financial overview:
   - Total revenue
   - Platform fees
   - Holder payouts
   - Net profit
3. Filter by date range
4. Export reports

## Security Best Practices

### Admin Account Security

1. Use strong, unique passwords
2. Don't share admin credentials
3. Review admin actions regularly
4. Remove unnecessary admin accounts

### Monitoring

1. Check Laravel logs daily
2. Review failed login attempts
3. Monitor for unusual activity
4. Set up error alerting

### Data Protection

1. Regular database backups
2. Encrypt sensitive data
3. Limit access to production server
4. Use HTTPS everywhere

## Common Tasks

### Handling Support Requests

**User Can't Login**
1. Check if account exists
2. Verify email is confirmed
3. Check if suspended
4. Reset password if needed

**KYC Taking Too Long**
1. Review pending application
2. Check document quality
3. Process or request info

**Subnet Verification Issues**
1. Check WHOIS data
2. Verify ownership manually
3. Approve if legitimate

**Payment Issues**
1. Check invoice status
2. Verify payment received
3. Mark as paid manually

### Emergency Procedures

**Suspected Abuse**
1. Suspend involved subnets
2. Suspend user account
3. Document the issue
4. Contact user for explanation

**Data Breach**
1. Change all admin passwords
2. Review access logs
3. Notify affected users
4. Report to authorities if required

**Service Outage**
1. Check server status
2. Review error logs
3. Restart services if needed
4. Communicate with users

## Audit Trail

All admin actions are logged:
- User modifications
- KYC decisions
- Subnet status changes
- Payout processing
- Impersonation sessions

Review logs in:
- Laravel logs: `storage/logs/laravel.log`
- Database audit tables (if configured)

## Performance Monitoring

### Key Metrics to Watch

1. **Response Times**: Pages should load < 2 seconds
2. **Error Rate**: Should be < 1%
3. **Database Queries**: Watch for slow queries
4. **Disk Usage**: Monitor storage growth

### Optimization Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database optimization
php artisan optimize
```
