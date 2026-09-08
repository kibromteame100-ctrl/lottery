# Lottery Platform — Mobile API Documentation

**Version:** v1  
**Base URL:** `https://your-domain.com/api/v1`  
**Authentication:** Laravel Sanctum (Bearer Token)  
**Content-Type:** `application/json` (except file uploads: `multipart/form-data`)

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Response Format](#response-format)
4. [Error Codes](#error-codes)
5. [Rate Limiting](#rate-limiting)
6. [Endpoints](#endpoints)
   - [Auth — Register](#1-register)
   - [Auth — Login](#2-login)
   - [Auth — Logout](#3-logout)
   - [Auth — Get Profile](#4-get-profile)
   - [Auth — Update Preferences](#5-update-preferences)
   - [Lotteries — List](#6-list-lotteries)
   - [Lotteries — Get One](#7-get-lottery)
   - [Tickets — Submit Purchase](#8-submit-ticket-purchase)
   - [Tickets — List My Tickets](#9-list-my-tickets)
   - [Tickets — Get One Ticket](#10-get-ticket)
   - [Notifications — List](#11-list-notifications)
   - [Notifications — Mark One Read](#12-mark-notification-read)
   - [Notifications — Mark All Read](#13-mark-all-notifications-read)
7. [Workflow Guide](#workflow-guide)
8. [Notification Types](#notification-types)
9. [Ticket Status Reference](#ticket-status-reference)
10. [Language Support](#language-support)

---

## Overview

This API is used exclusively by the **Lottery Platform mobile application**. It allows customers to:

- Register and log in
- Browse active lotteries
- Submit payment proof (transaction ID + screenshot)
- Track their ticket status and lottery number
- Receive in-app notifications

All authenticated endpoints require a **Sanctum Bearer Token** obtained from `/auth/login` or `/auth/register`.

---

## Authentication

After registering or logging in, include the token in **every protected request**:

```
Authorization: Bearer {your_token_here}
```

Tokens do not expire automatically. They are invalidated when you call `/auth/logout`.

---

## Response Format

All responses follow this consistent structure:

**Success**
```json
{
    "success": true,
    "message": "Human-readable description.",
    "data": { ... }
}
```

**Error**
```json
{
    "success": false,
    "message": "Human-readable error description.",
    "errors": {
        "field_name": ["Validation error message."]
    }
}
```

**Paginated**
```json
{
    "success": true,
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "total": 98
    }
}
```

---

## Error Codes

| HTTP Code | Meaning |
|-----------|---------|
| `200` | OK — request successful |
| `201` | Created — resource created successfully |
| `401` | Unauthenticated — missing or invalid token |
| `403` | Forbidden — account suspended or unauthorized |
| `404` | Not Found — resource does not exist |
| `422` | Unprocessable Entity — validation failed |
| `429` | Too Many Requests — rate limit exceeded |
| `500` | Server Error — contact support |

---

## Rate Limiting

| Endpoint | Limit |
|----------|-------|
| `POST /auth/register` | 10 requests / minute |
| `POST /auth/login` | 10 requests / minute |
| All other endpoints | Standard (60 / minute) |

When exceeded, response:
```json
{
    "message": "Too Many Attempts."
}
```
HTTP Status: `429`

---

## Endpoints

---

### 1. Register

Create a new customer account.

```
POST /api/v1/auth/register
```

**Authentication:** None  
**Rate Limit:** 10/min

**Headers:**
| Header | Required | Description |
|--------|----------|-------------|
| `Content-Type` | Yes | `application/json` |
| `Accept-Language` | No | `en`, `am`, or `ti` — sets preferred locale |

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `name` | string | Yes | max 100 characters |
| `phone` | string | Conditional | Required if no `email`. Max 20 chars. Must be unique. |
| `email` | string | Conditional | Required if no `phone`. Valid email. Must be unique. |
| `password` | string | Yes | Min 8 characters |
| `password_confirmation` | string | Yes | Must match `password` |

> At least one of `phone` or `email` is required.

**Example Request:**
```json
{
    "name": "Alula Tesfay",
    "phone": "+251912345678",
    "password": "secret123",
    "password_confirmation": "secret123"
}
```

**Success Response — `201 Created`:**
```json
{
    "success": true,
    "message": "Account registered successfully.",
    "data": {
        "user": {
            "id": 12,
            "name": "Alula Tesfay",
            "phone": "+251912345678",
            "email": null,
            "preferred_locale": "en",
            "theme_preference": "light",
            "status": "active"
        },
        "token": "3|xKj9mN2pQr7sVtUwYzA..."
    }
}
```

**Validation Error — `422`:**
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "phone": ["The phone has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

### 2. Login

Authenticate an existing customer and get a token.

```
POST /api/v1/auth/login
```

**Authentication:** None  
**Rate Limit:** 10/min

**Request Body:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `phone` | string | Conditional | Required if no `email` |
| `email` | string | Conditional | Required if no `phone` |
| `password` | string | Yes | Account password |

**Example Request (phone):**
```json
{
    "phone": "+251912345678",
    "password": "secret123"
}
```

**Example Request (email):**
```json
{
    "email": "alula@example.com",
    "password": "secret123"
}
```

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "user": {
            "id": 12,
            "name": "Alula Tesfay",
            "phone": "+251912345678",
            "email": null,
            "preferred_locale": "am",
            "theme_preference": "dark",
            "status": "active"
        },
        "token": "5|aBcDeFgHiJkLmNoPqR..."
    }
}
```

**Wrong Credentials — `422`:**
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "phone": ["These credentials do not match our records."]
    }
}
```

**Account Suspended — `403`:**
```json
{
    "success": false,
    "message": "Your account has been suspended. Please contact support."
}
```

---

### 3. Logout

Invalidate the current access token.

```
POST /api/v1/auth/logout
```

**Authentication:** Required

**Request Body:** None

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "message": "You have been logged out."
}
```

---

### 4. Get Profile

Return the authenticated customer's profile.

```
GET /api/v1/me
```

**Authentication:** Required

**Request Body:** None

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "data": {
        "id": 12,
        "name": "Alula Tesfay",
        "phone": "+251912345678",
        "email": null,
        "preferred_locale": "am",
        "theme_preference": "dark",
        "status": "active"
    }
}
```

---

### 5. Update Preferences

Update the customer's language or theme preference.

```
PATCH /api/v1/me/preferences
```

**Authentication:** Required

**Request Body:**
| Field | Type | Required | Allowed Values |
|-------|------|----------|----------------|
| `preferred_locale` | string | No | `en`, `am`, `ti` |
| `theme_preference` | string | No | `light`, `dark`, `system` |

At least one field must be provided.

**Example Request:**
```json
{
    "preferred_locale": "ti",
    "theme_preference": "dark"
}
```

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "message": "Preferences updated successfully.",
    "data": {
        "id": 12,
        "name": "Alula Tesfay",
        "phone": "+251912345678",
        "email": null,
        "preferred_locale": "ti",
        "theme_preference": "dark",
        "status": "active"
    }
}
```

---

### 6. List Lotteries

Get all currently active lotteries available for purchase.

```
GET /api/v1/lotteries
```

**Authentication:** Required

**Request Body:** None  
**Query Parameters:** None

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Grand New Year Lottery 2026",
            "description": "Win big in the grand new year lottery draw.",
            "ticket_price": "50.00",
            "draw_date": "2026-10-01T10:00:00+00:00",
            "status": "active",
            "number_prefix": "LOT",
            "tickets_sold": 145,
            "max_tickets": 1000,
            "remaining": 855
        },
        {
            "id": 2,
            "name": "Weekly Lucky Draw",
            "description": "Weekly lottery with great prizes.",
            "ticket_price": "25.00",
            "draw_date": "2026-09-14T10:00:00+00:00",
            "status": "active",
            "number_prefix": "WLD",
            "tickets_sold": 88,
            "max_tickets": 500,
            "remaining": 412
        }
    ]
}
```

**Field Descriptions:**
| Field | Description |
|-------|-------------|
| `ticket_price` | Price per ticket in ETB |
| `draw_date` | ISO 8601 datetime of the draw |
| `tickets_sold` | Total pending + approved tickets |
| `max_tickets` | Maximum allowed tickets (`null` = unlimited) |
| `remaining` | Remaining tickets available (`null` = unlimited) |

---

### 7. Get Lottery

Get details of a single lottery by ID.

```
GET /api/v1/lotteries/{id}
```

**Authentication:** Required

**URL Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Lottery ID |

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Grand New Year Lottery 2026",
        "description": "Win big in the grand new year lottery draw.",
        "ticket_price": "50.00",
        "draw_date": "2026-10-01T10:00:00+00:00",
        "status": "active",
        "number_prefix": "LOT",
        "tickets_sold": 145,
        "max_tickets": 1000,
        "remaining": 855
    }
}
```

**Not Found — `404`:**
```json
{
    "message": "No query results for model [App\\Models\\Lottery] 99"
}
```

---

### 8. Submit Ticket Purchase

Submit payment proof to enter a lottery. The screenshot is stored privately and reviewed by an admin.

```
POST /api/v1/ticket-purchases
```

**Authentication:** Required  
**Content-Type:** `multipart/form-data`

**Form Fields:**
| Field | Type | Required | Rules |
|-------|------|----------|-------|
| `lottery_id` | integer | Yes | Must be an active lottery ID |
| `transaction_id` | string | Yes | Max 100 chars. **Must be unique** across all purchases. |
| `payment_method` | string | Yes | Max 50 chars. E.g. `bank_transfer`, `mobile_money`, `CBE`, `Telebirr` |
| `screenshot` | file | Yes | JPG, JPEG, PNG, or WebP. Max 5 MB. |

> **Important:** `transaction_id` must be unique. Submitting the same transaction ID twice will return a validation error.

**Example Request (multipart/form-data):**
```
lottery_id:      1
transaction_id:  TXN-CBE-20260907-98765
payment_method:  CBE
screenshot:      [binary file]
```

**Success Response — `201 Created`:**
```json
{
    "success": true,
    "message": "Payment proof submitted successfully.",
    "data": {
        "id": 23,
        "status": "pending",
        "transaction_id": "TXN-CBE-20260907-98765"
    }
}
```

**Lottery Not Active — `422`:**
```json
{
    "success": false,
    "message": "This lottery is not currently active."
}
```

**Lottery Sold Out — `422`:**
```json
{
    "success": false,
    "message": "This lottery is sold out."
}
```

**Validation Error — `422`:**
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "transaction_id": ["The transaction id has already been taken."],
        "screenshot": ["The screenshot may not be greater than 5120 kilobytes."]
    }
}
```

---

### 9. List My Tickets

Get all ticket purchases for the authenticated customer (paginated).

```
GET /api/v1/ticket-purchases
```

**Authentication:** Required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number (20 items per page) |

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 23,
            "lottery": {
                "id": 1,
                "name": "Grand New Year Lottery 2026",
                "draw_date": "2026-10-01T10:00:00+00:00"
            },
            "ticket_price": "50.00",
            "payment_method": "CBE",
            "transaction_id": "TXN-CBE-20260907-98765",
            "status": "pending",
            "rejection_reason": null,
            "lottery_number": null,
            "reviewed_at": null,
            "created_at": "2026-09-07T08:30:00+00:00"
        },
        {
            "id": 18,
            "lottery": {
                "id": 2,
                "name": "Weekly Lucky Draw",
                "draw_date": "2026-09-14T10:00:00+00:00"
            },
            "ticket_price": "25.00",
            "payment_method": "Telebirr",
            "transaction_id": "TXN-TLB-20260901-11223",
            "status": "approved",
            "rejection_reason": null,
            "lottery_number": "WLD-2026-000042",
            "reviewed_at": "2026-09-02T11:15:00+00:00",
            "created_at": "2026-09-01T09:00:00+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "total": 25
    }
}
```

---

### 10. Get Ticket

Get details of a single ticket purchase. Shows the lottery number if approved, or rejection reason if rejected.

```
GET /api/v1/ticket-purchases/{id}
```

**Authentication:** Required

**URL Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Ticket purchase ID |

> Customers can only access their own tickets. Attempting to access another customer's ticket returns `403`.

**Success Response — Pending — `200 OK`:**
```json
{
    "success": true,
    "data": {
        "id": 23,
        "lottery": {
            "id": 1,
            "name": "Grand New Year Lottery 2026",
            "draw_date": "2026-10-01T10:00:00+00:00"
        },
        "ticket_price": "50.00",
        "payment_method": "CBE",
        "transaction_id": "TXN-CBE-20260907-98765",
        "status": "pending",
        "rejection_reason": null,
        "lottery_number": null,
        "reviewed_at": null,
        "created_at": "2026-09-07T08:30:00+00:00"
    }
}
```

**Success Response — Approved (with lottery number) — `200 OK`:**
```json
{
    "success": true,
    "data": {
        "id": 23,
        "lottery": {
            "id": 1,
            "name": "Grand New Year Lottery 2026",
            "draw_date": "2026-10-01T10:00:00+00:00"
        },
        "ticket_price": "50.00",
        "payment_method": "CBE",
        "transaction_id": "TXN-CBE-20260907-98765",
        "status": "approved",
        "rejection_reason": null,
        "lottery_number": "LOT-2026-000123",
        "reviewed_at": "2026-09-07T10:45:00+00:00",
        "created_at": "2026-09-07T08:30:00+00:00"
    }
}
```

**Success Response — Rejected — `200 OK`:**
```json
{
    "success": true,
    "data": {
        "id": 23,
        "lottery": {
            "id": 1,
            "name": "Grand New Year Lottery 2026",
            "draw_date": "2026-10-01T10:00:00+00:00"
        },
        "ticket_price": "50.00",
        "payment_method": "CBE",
        "transaction_id": "TXN-CBE-20260907-98765",
        "status": "rejected",
        "rejection_reason": "Transaction ID could not be verified in our bank records.",
        "lottery_number": null,
        "reviewed_at": "2026-09-07T10:50:00+00:00",
        "created_at": "2026-09-07T08:30:00+00:00"
    }
}
```

**Access Denied — `403`:**
```json
{
    "success": false,
    "message": "You are not authorized to perform this action."
}
```

---

### 11. List Notifications

Get all notifications for the authenticated customer (paginated, newest first).

```
GET /api/v1/notifications
```

**Authentication:** Required

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number (20 items per page) |

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "data": [
        {
            "id": 5,
            "type": "ticket_approved",
            "title": "Payment Approved",
            "message": "Your payment has been approved. Your lottery number is: LOT-2026-000123",
            "data": {
                "ticket_purchase_id": 23,
                "lottery_number": "LOT-2026-000123"
            },
            "read_at": null,
            "created_at": "2026-09-07T10:45:00+00:00"
        },
        {
            "id": 3,
            "type": "ticket_submitted",
            "title": "Payment Submitted",
            "message": "Your payment for Grand New Year Lottery 2026 has been submitted and is pending review.",
            "data": {
                "ticket_purchase_id": 23
            },
            "read_at": "2026-09-07T09:00:00+00:00",
            "created_at": "2026-09-07T08:30:00+00:00"
        }
    ],
    "unread_count": 1
}
```

**Field Descriptions:**
| Field | Description |
|-------|-------------|
| `type` | Notification type (see [Notification Types](#notification-types)) |
| `data` | Extra structured data relevant to the notification |
| `read_at` | ISO 8601 timestamp when read. `null` = unread |
| `unread_count` | Total unread notifications for this user |

---

### 12. Mark Notification Read

Mark a single notification as read.

```
PATCH /api/v1/notifications/{id}/read
```

**Authentication:** Required

**URL Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | integer | Notification ID |

**Request Body:** None

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "message": "Notification marked as read."
}
```

**Not Found — `404`:**
```json
{
    "message": "No query results for model..."
}
```

---

### 13. Mark All Notifications Read

Mark all unread notifications as read in one call.

```
PATCH /api/v1/notifications/read-all
```

**Authentication:** Required

**Request Body:** None

**Success Response — `200 OK`:**
```json
{
    "success": true,
    "message": "All notifications marked as read."
}
```

---

## Workflow Guide

This is the complete flow for a customer from registration to receiving a lottery number:

```
1.  POST /auth/register        → Get token
2.  GET  /lotteries            → Browse available lotteries
3.  GET  /lotteries/{id}       → View details + remaining tickets
4.  [Customer makes payment via bank / mobile money]
5.  POST /ticket-purchases     → Submit transaction ID + screenshot
                                 Response: { id, status: "pending" }
6.  GET  /ticket-purchases/{id} → Poll for status update
                                  OR listen for push notification
7.  [Admin reviews payment in the web admin panel]
    → Admin approves: status becomes "approved", lottery_number generated
    → Admin rejects:  status becomes "rejected", rejection_reason set
8.  GET  /ticket-purchases/{id}  → status: "approved"
                                   lottery_number: "LOT-2026-000123"
9.  GET  /notifications         → Shows approval notification with number
```

---

## Notification Types

| Type | Trigger | Key `data` fields |
|------|---------|------------------|
| `ticket_submitted` | Customer submits payment proof | `ticket_purchase_id` |
| `ticket_approved` | Admin approves the ticket | `ticket_purchase_id`, `lottery_number` |
| `ticket_rejected` | Admin rejects the ticket | `ticket_purchase_id`, `rejection_reason` |

---

## Ticket Status Reference

| Status | Meaning |
|--------|---------|
| `pending` | Submitted, waiting for admin review |
| `approved` | Payment verified, lottery number generated |
| `rejected` | Payment could not be verified |
| `cancelled` | Ticket was cancelled |

---

## Language Support

The API respects the user's `preferred_locale` for all human-readable messages.

Supported locales:
| Code | Language |
|------|----------|
| `en` | English |
| `am` | Amharic (አማርኛ) |
| `ti` | Tigrinya (ትግርኛ) |

**Setting locale at registration:**
Pass `Accept-Language: am` header on the register request to auto-set the preferred locale.

**Changing locale after registration:**
```
PATCH /api/v1/me/preferences
{ "preferred_locale": "ti" }
```

---

## Complete Endpoint Reference

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/v1/auth/register` | No | Register new customer |
| `POST` | `/api/v1/auth/login` | No | Login and get token |
| `POST` | `/api/v1/auth/logout` | Yes | Invalidate token |
| `GET` | `/api/v1/me` | Yes | Get my profile |
| `PATCH` | `/api/v1/me/preferences` | Yes | Update language/theme |
| `GET` | `/api/v1/lotteries` | Yes | List active lotteries |
| `GET` | `/api/v1/lotteries/{id}` | Yes | Get single lottery |
| `POST` | `/api/v1/ticket-purchases` | Yes | Submit payment proof |
| `GET` | `/api/v1/ticket-purchases` | Yes | List my tickets |
| `GET` | `/api/v1/ticket-purchases/{id}` | Yes | Get single ticket |
| `GET` | `/api/v1/notifications` | Yes | List my notifications |
| `PATCH` | `/api/v1/notifications/{id}/read` | Yes | Mark one as read |
| `PATCH` | `/api/v1/notifications/read-all` | Yes | Mark all as read |

---

## Testing with cURL

**Register:**
```bash
curl -X POST https://your-domain.com/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Test User","phone":"+251912345678","password":"secret123","password_confirmation":"secret123"}'
```

**Login:**
```bash
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"phone":"+251912345678","password":"secret123"}'
```

**Submit ticket (multipart):**
```bash
curl -X POST https://your-domain.com/api/v1/ticket-purchases \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json" \
  -F "lottery_id=1" \
  -F "transaction_id=TXN-CBE-20260907-12345" \
  -F "payment_method=CBE" \
  -F "screenshot=@/path/to/receipt.jpg"
```

**Get ticket status:**
```bash
curl https://your-domain.com/api/v1/ticket-purchases/23 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

*Documentation generated for Lottery Platform v1 — September 2026*
