# Backend Validation Summary

## Overview

Comprehensive backend validation has been integrated across the entire application to maintain data integrity and enhance security.

## New File

### `/backend/config/validators.php`

A centralized file containing reusable validation helper functions used throughout the system.

## Implemented Validation Rules

### Patient Registration & Updates

**Required Fields**

* First Name (f_name)
* Last Name (l_name)
* Date of Birth (dob)
* Gender
* Address
* Email
* Username
* Password (for registration)

**Validation Details**

* **First & Last Name:** 2–50 characters, letters/spaces/apostrophes/hyphens only, cannot be empty.
* **Email:** Valid RFC 5322 format, max 100 characters, required, must be unique.
* **Username:** 3–50 characters, alphanumeric with underscores or hyphens, required, must be unique.
* **Password:** 6–255 characters, required.
* **Date of Birth:** Valid date, not in the future, age ≤150 years, required.
* **Gender:** Must be “Male”, “Female”, or “Other”.
* **Address:** 5–255 characters, required.

### Lab Registration & Updates

**Required Fields**

* Lab Name (lab_name)
* Location
* Contact Number (contact_num)
* Email
* Username
* Password (for registration)

**Validation Details**

* **Lab Name:** 2–100 characters, letters/spaces/apostrophes/hyphens only, required.
* **Location:** 5–100 characters, required.
* **Contact Number:** Valid Sri Lankan format (`0771234567`, `+94771234567`, or `94771234567`), spaces/hyphens removed automatically, required, must be unique.
* **Email:** Valid format, max 100 characters, required, must be unique.
* **Username:** 3–50 characters, alphanumeric with underscores or hyphens, required, must be unique.
* **Password:** 6–255 characters, required.

### Medical Report Creation

**Required Fields**
All listed fields are mandatory:
Patient ID, Hemoglobin, MCV, WBC, Neutrophils, FPG, eGFR, Creatinine, AST, ALT, HCT, RBC, MCH, MCHC, Lymphocytes, GGT, Albumin, Urea, Triglycerides, Total Cholesterol, HDL, LDL, ALP, Total Bilirubin, Direct Bilirubin.

**Validation Details**

* All values must be numeric and non-negative.
* Patient ID must be numeric and valid.
* Patient must exist and be linked to the reporting lab.

### Login Validation

**Patient Login**

* Username and password required.
* Credentials verified against the database.
* Inputs sanitized before processing.

**Lab Login**

* Username and password required.
* Credentials verified against the database.
* Inputs sanitized before processing.

## Security Features

### Input Sanitization

All inputs are processed through `sanitizeData()` which:

* Trims extra whitespace.
* Cleans nested arrays recursively.
* Runs before validation.

### SQL Injection Prevention

* All queries use `mysqli_real_escape_string()` and prepared statements.

### Password Security

* Passwords hashed with `password_hash()` using `PASSWORD_DEFAULT`.
* Verified using `password_verify()`.
* Plain text passwords never stored.

### Duplicate Data Prevention

* Unique checks for email and username (patients and labs).
* Unique check for contact number (labs).
* Duplicate validation during registration and updates.

### Authorization

* Session-based authentication.
* Role validation (patient/lab).
* Lab-patient linkage enforced for report creation.
* Protected routes ensure authentication.

## Modified Files

1. **`/backend/config/validators.php`** – New centralized validation utilities.
2. **`/backend/src/Models/Lab.model.php`** – Added `findLabByContactNum()` for duplicate checking.
3. **`/backend/src/Services/Auth.service.php`** – Improved registration/login validation and error handling.
4. **`/backend/src/Services/patient.service.php`** – Integrated centralized validators and duplicate checks.
5. **`/backend/src/Services/Report.service.php`** – Added numeric/negative validations with detailed error feedback.
6. **`/backend/src/Controllers/Lab.controller.php`** – Added validation and duplicate checks for patient creation by labs.

## Error Messages

All validation errors return concise, user-friendly messages such as:

* “First name is required.”
* “Invalid email format.”
* “Email already exists.”
* “Username already exists.”
* “Contact number already exists.”
* “Password must be at least 6 characters long.”
* “Date of birth cannot be in the future.”

## Testing Recommendations

**Registration Tests**

* Register with existing email, username, or phone.
* Use invalid email or phone formats.
* Leave required fields empty.
* Use short passwords.

**Update Tests**

* Update email/username to existing ones.
* Update with valid unique values.

**Login Tests**

* Try empty username or password.
* Try invalid credentials.

**Report Tests**

* Missing, non-numeric, or negative fields.
* Invalid or unlinked patient IDs.

## Benefits

* Ensures data integrity and security.
* Prevents malicious inputs and SQL injection.
* Provides clear and consistent error messages.
* Reduces code duplication through centralized validation.
* Simplifies future maintenance and scalability.

## Future Enhancements

* Email verification via confirmation link.
* Phone verification via SMS OTP.
* Login rate limiting and CAPTCHA.
* Password strength checks and two-factor authentication.
* Middleware-based validation for routes.
