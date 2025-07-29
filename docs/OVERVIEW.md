# CabManage Developer Overview

This document provides an orientation for new developers joining the project. It summarises the structure of the repository and the main responsibilities of each component so that the system can be reimplemented or extended.

## Project Goals

CabManage is a web-based management system for pathology and cytology laboratories. It handles patients, specimen tracking, billing, and reporting. A feature overview is available in the main `README.md` and includes user role management, patient management, specimen management, billing, reporting and external doctor integration.

## Repository Layout

```
cabmanage_v1/
├── auth.php               # Authentication helper
├── config.php             # Database connection
├── DocteurExterieur.php   # External doctor records
├── Examen.php             # Examination (analysis) definitions
├── Facture.php            # Billing logic
├── Patient.php            # Patient CRUD and queries
├── Prelevement.php        # Specimen records
├── Template.php           # Report templates
├── Front/                 # Front‑end assets (HTML/CSS/JS)
├── *.php                  # Individual pages and actions
└── vendor/                # Composer dependencies
```

The PHP files at the root of `cabmanage_v1` implement pages for login, dashboards and CRUD operations (for example `create_patient.php`, `edit_prelevement.php`, `statistics_assistance.php`).

## Key Classes

- **Patient** – manages patient details and performs queries such as search and counting total patients.
- **Prelevement** – handles specimen collection, including barcodes and links to patients and doctors.
- **Facture** – represents invoices and tracks payment status and sums.
- **Examen** – defines examination types and synchronises related billing data.
- **Template** – stores and retrieves report templates.
- **DocteurExterieur** – database of referring physicians.
- **Auth** – user registration and login helper.

These classes use MySQL via `mysqli` and are instantiated inside the various page scripts.

## Setup Summary

Basic installation steps are described in `README.md` and include cloning the repository, importing `cabmanage.sql` into MySQL/MariaDB and configuring `config.php` with connection details. PHP dependencies can be installed with `composer install`.

## Rebuilding or Extending the Project

1. **Study the existing pages** – Each `.php` file in `cabmanage_v1` corresponds to an action or view (e.g. patient management, specimen creation, dashboards). Review these to understand the application flow.
2. **Database schema** – The SQL file in the project root defines tables for users, patients, prélèvements, factures, examens and templates. It is the backbone of the application.
3. **Front‑end assets** – Located under `cabmanage_v1/Front`, containing style sheets and static HTML templates for dashboards.
4. **PDF/Reports** – The project uses TCPDF and FPDI for PDF generation, included via Composer in the `vendor` directory. Files such as `doctor_template.php` demonstrate how reports are created.

When redesigning or rewriting the system, keep the modular separation of concerns (patients, prélèvements, examens, factures) and maintain secure practices such as prepared statements and password hashing.

## Additional References

- Main user documentation and feature descriptions can be found in `README.md` lines 1–38.
- The technology stack and installation instructions appear in `README.md` lines 42–67.

