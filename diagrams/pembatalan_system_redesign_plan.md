# PEMBATALAN SYSTEM REDESIGN: IMPLEMENTATION PLAN

## Overview

This document outlines the implementation plan for transitioning from the current client-initiated cancellation system to a new admin-controlled pembatalan (cancellation) process for MIW Travel Management System. The redesign aims to remove direct client access to cancellation functionality, requiring clients to contact MIW Travel manually for cancellation requests.

## Table of Contents

1. [Current System Analysis](#current-system-analysis)
2. [New System Design](#new-system-design)
3. [Implementation Phases](#implementation-phases)
4. [Code Changes](#code-changes)
5. [Database Modifications](#database-modifications)
6. [User Interface Requirements](#user-interface-requirements)
7. [Business Logic Implementation](#business-logic-implementation)
8. [Testing Procedure](#testing-procedure)
9. [Deployment Strategy](#deployment-strategy)

## Current System Analysis

### Files Involved in Current System

- `form_pembatalan.php` - Public-facing cancellation form
- `submit_pembatalan.php` - Form submission handler
- `admin_pembatalan.php` - Admin panel for reviewing cancellations
- `verify_cancellation.php` - Verification/approval process
- `get_pembatalan_details.php` - Retrieves cancellation details
- `delete_pembatalan.php` - Deletes cancellation records

### Current Process Flow

1. Client accesses cancellation form (`form_pembatalan.php`)
2. Client submits form with personal details and reason (`submit_pembatalan.php`)
3. Admin reviews submissions in dashboard (`admin_pembatalan.php`)
4. Admin approves cancellation (`verify_cancellation.php`)
5. System sends confirmation email and deletes records

### Current Limitations

- No explicit penalty calculation
- No financial transaction tracking
- Immediate data deletion without comprehensive record-keeping
- No opportunity for customer retention attempts
- Limited approval workflow

## New System Design

### New Process Flow

1. Client contacts MIW Travel manually (phone, email, in-person)
2. Admin searches for client and reviews details
3. Admin initiates cancellation process
4. System calculates penalty amount
5. System sends email with payment instructions
6. Client makes payment and submits proof
7. Admin verifies payment
8. System completes cancellation process and sends receipt

### Key Features

- Admin-initiated workflow
- Structured penalty calculation based on business rules
- Payment tracking and verification system
- Multi-stage email notifications
- Comprehensive record-keeping
- Integration with financial systems

## Implementation Phases

### Phase 1: Preparation (Week 1)

- [ ] Deactivate existing public cancellation form
- [ ] Create database backup
- [ ] Document existing functionality fully
- [ ] Set up development environment for new system

### Phase 2: Database Enhancements (Week 1-2)

- [ ] Create new tables for penalty tracking
- [ ] Modify existing tables with new fields
- [ ] Create database migration scripts
- [ ] Set up audit logging

### Phase 3: Admin Interface Development (Week 2-3)

- [ ] Develop client search functionality
- [ ] Create cancellation initiation interface
- [ ] Build penalty calculation module
- [ ] Design payment verification interface

### Phase 4: Business Logic Implementation (Week 3-4)

- [ ] Implement penalty calculation algorithms
- [ ] Create email template system
- [ ] Develop payment verification logic
- [ ] Build record-keeping functions

### Phase 5: Testing and Refinement (Week 4-5)

- [ ] Conduct unit testing for all components
- [ ] Perform integration testing
- [ ] Test business rule scenarios
- [ ] Usability testing with admin staff

### Phase 6: Deployment (Week 5)

- [ ] Deploy database changes
- [ ] Deploy new admin interface
- [ ] Update documentation
- [ ] Train staff on new system

## Code Changes

### Files to Remove

- `form_pembatalan.php`
- `submit_pembatalan.php`
- Public-facing references to cancellation form

### Files to Create

- `admin_pembatalan_initiate.php` - Interface for initiating cancellations
- `pembatalan_penalty_calculator.php` - Penalty calculation module
- `pembatalan_payment_verification.php` - Payment verification interface
- `pembatalan_email_manager.php` - Email generation and tracking
- `pembatalan_receipt_generator.php` - Receipt generation module

### Files to Modify

- `admin_pembatalan.php` - Enhance with new functionality
- `admin_nav.php` - Update navigation with new options
- `verify_cancellation.php` - Modify for new workflow
- `admin_dashboard.php` - Add cancellation metrics

## Database Modifications

### New Tables

```sql
-- Track admin-initiated cancellation requests
CREATE TABLE IF NOT EXISTS pembatalan_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) NOT NULL,
    initiated_by VARCHAR(50) NOT NULL,
    initiated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reason_code VARCHAR(20) NOT NULL,
    reason_details TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'INITIATED',
    special_circumstances TEXT,
    admin_notes TEXT,
    FOREIGN KEY (nik) REFERENCES data_jamaah(nik)
);

-- Track penalty payments
CREATE TABLE IF NOT EXISTS pembatalan_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    penalty_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'IDR',
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_account_name VARCHAR(100),
    payment_date DATETIME,
    payment_proof_path VARCHAR(255),
    verified_by VARCHAR(50),
    verified_at DATETIME,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    FOREIGN KEY (request_id) REFERENCES pembatalan_requests(request_id)
);

-- Audit trail for entire cancellation process
CREATE TABLE IF NOT EXISTS pembatalan_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    log_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    action_type VARCHAR(50) NOT NULL,
    performed_by VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    FOREIGN KEY (request_id) REFERENCES pembatalan_requests(request_id)
);
```

### Table Modifications

```sql
-- Modify existing data_pembatalan table
ALTER TABLE data_pembatalan
ADD COLUMN initiated_by VARCHAR(50) AFTER alasan,
ADD COLUMN penalty_amount DECIMAL(12,2) AFTER initiated_by,
ADD COLUMN payment_status VARCHAR(20) DEFAULT 'PENDING' AFTER penalty_amount,
ADD COLUMN payment_verified_date DATETIME AFTER payment_status,
ADD COLUMN payment_verified_by VARCHAR(50) AFTER payment_verified_date,
ADD COLUMN special_circumstances TEXT AFTER payment_verified_by;
```

## User Interface Requirements

### Admin Cancellation Initiation Interface

- Client search field (by NIK, name, or phone)
- Client profile summary display
- Package and payment history overview
- Cancellation reason dropdown
- Special circumstances input field
- Cancellation initiation button with confirmation dialog

### Penalty Calculation Interface

- Dynamic calculation display
- Penalty breakdown by components
- Override capabilities for authorized admins
- Preview of client communication

### Payment Verification Interface

- Payment proof document viewer
- Expected vs received amount comparison
- Transaction details verification fields
- Approval/rejection options with comment field
- Email preview before sending

## Business Logic Implementation

### Penalty Calculation Algorithm

```php
function calculateCancellationPenalty($packageDetails, $paymentStatus, $daysBeforeDeparture, $specialCircumstances = []) {
    // Base calculation
    $baseAmount = $paymentStatus['totalPaid'];
    $penaltyRate = 0;
    
    // Determine penalty rate based on package type and timing
    if ($packageDetails['type'] === 'UMRAH') {
        if ($daysBeforeDeparture > 180) $penaltyRate = 0.10;
        else if ($daysBeforeDeparture > 90) $penaltyRate = 0.30;
        else if ($daysBeforeDeparture > 60) $penaltyRate = 0.50;
        else if ($daysBeforeDeparture > 30) $penaltyRate = 0.70;
        else $penaltyRate = 0.90;
    } else if ($packageDetails['type'] === 'HAJI') {
        if ($daysBeforeDeparture > 180) $penaltyRate = 0.10;
        else if ($daysBeforeDeparture > 90) $penaltyRate = 0.40;
        else if ($daysBeforeDeparture > 60) $penaltyRate = 0.70;
        else $penaltyRate = 0.90;
    }
    
    // Apply special circumstances adjustments
    if (in_array('MEDICAL', $specialCircumstances)) {
        $penaltyRate = max(0.05, $penaltyRate - 0.20); // Reduce but ensure minimum 5%
    }
    
    // Calculate final penalty
    $penaltyAmount = $baseAmount * $penaltyRate;
    
    // Ensure minimum penalty for DP-only cases
    if ($paymentStatus['isDepositOnly'] && $packageDetails['type'] === 'HAJI') {
        $exchangeRate = getLatestExchangeRate('USD', 'IDR');
        $minPenalty = 500 * $exchangeRate;
        $penaltyAmount = max($penaltyAmount, $minPenalty);
    }
    
    return [
        'originalAmount' => $baseAmount,
        'penaltyRate' => $penaltyRate,
        'penaltyAmount' => $penaltyAmount,
        'refundAmount' => $baseAmount - $penaltyAmount,
        'calculationFactors' => [
            'packageType' => $packageDetails['type'],
            'daysBeforeDeparture' => $daysBeforeDeparture,
            'specialCircumstances' => $specialCircumstances
        ]
    ];
}
```

### Email Notification System

```php
function generateCancellationEmail($stage, $clientData, $cancellationDetails, $paymentDetails) {
    $emailData = [];
    
    // Initial penalty notification
    if ($stage === 'INITIATED') {
        $emailData = [
            'subject' => "Informasi Denda Pembatalan - {$clientData['name']}",
            'template' => 'penalty_notification',
            'variables' => [
                'clientName' => $clientData['name'],
                'packageName' => $cancellationDetails['packageName'],
                'departureDate' => formatDate($cancellationDetails['departureDate']),
                'penaltyAmount' => formatCurrency($paymentDetails['penaltyAmount']),
                'penaltyBreakdown' => generatePenaltyBreakdown($paymentDetails),
                'paymentInstructions' => generatePaymentInstructions(),
                'deadlineDate' => formatDate(calculateDeadline(7)) // 7 days to pay
            ],
            'attachments' => [
                [
                    'filename' => "Invoice_Pembatalan_{$clientData['nik']}.pdf",
                    'content' => generateInvoicePDF($clientData, $paymentDetails)
                ]
            ]
        ];
    }
    
    // Final receipt after verification
    if ($stage === 'COMPLETED') {
        $emailData = [
            'subject' => "Konfirmasi Pembatalan - {$clientData['name']}",
            'template' => 'cancellation_completed',
            'variables' => [
                'clientName' => $clientData['name'],
                'referenceNumber' => $cancellationDetails['referenceNumber'],
                'completionDate' => formatDate(new Date()),
                'refundAmount' => formatCurrency($paymentDetails['refundAmount']),
                'refundProcess' => getRefundProcessText()
            ],
            'attachments' => [
                [
                    'filename' => "Kwitansi_Pembatalan_{$clientData['nik']}.pdf",
                    'content' => generateReceiptPDF($clientData, $paymentDetails, $cancellationDetails)
                ]
            ]
        ];
    }
    
    return $emailData;
}
```

## Testing Procedure

### Unit Testing

- Penalty calculation for different scenarios
- Email template generation
- Database operations for new tables
- File upload and storage functionality

### Integration Testing

- End-to-end cancellation workflow
- Email delivery and tracking
- Payment verification process
- Database integrity across operations

### Business Rule Testing

Test scenarios for various business conditions:
- Cancellation timing (different days before departure)
- Package types (Haji vs Umrah)
- Payment statuses (deposit only vs full payment)
- Special circumstances handling

### Usability Testing

- Admin interface usability
- Process efficiency measurement
- Error handling and recovery
- Edge case handling

## Deployment Strategy

### Pre-Deployment Tasks

- Full database backup
- Create rollback script
- Document all changes
- Train administrative staff

### Deployment Steps

1. Deploy database changes
2. Deploy new backend code
3. Deploy admin interface updates
4. Disable old cancellation form routes
5. Enable new cancellation workflow
6. Verify all functionalities in production

### Post-Deployment Monitoring

- Monitor system for errors
- Track admin usage patterns
- Gather feedback for improvements
- Measure process efficiency metrics

## Conclusion

This implementation plan provides a structured approach to transition from the current client-initiated cancellation system to an admin-controlled process. The new system will offer better control over cancellations, improved financial tracking, and enhanced customer communication while removing direct client access to cancellation functionality.
