# 🏥 CabManage - Medical Office Management System

## 📋 Overview

CabManage is a comprehensive web-based management system designed for medical laboratories specializing in pathology and cytology. This PHP-based application streamlines patient management, specimen tracking, billing, and reporting workflows to enhance operational efficiency in medical offices.

## ✨ Features

- **🧑‍⚕️ User Role Management**
  - Multi-level access for doctors, assistants, and administrators
  - Secure authentication system with role-based permissions

- **👨‍👩‍👧‍👦 Patient Management**
  - Complete patient profile creation and management
  - Medical history tracking and record-keeping
  - Insurance information management
  - Advanced search functionality by name, ID, or date of birth

- **🧪 Specimen Management**
  - Track specimens from collection to results
  - Manage multiple examination types
  - Barcode generation for each specimen
  - Link specimens to specific patients and doctors

- **💰 Billing System**
  - Create and manage invoices
  - Track payments (full, partial, or unpaid)
  - Apply price reductions and advance payments
  - Generate financial reports and statistics

- **📊 Reporting System**
  - Generate comprehensive medical reports
  - Create specimen collection receipts
  - Print invoices with customizable templates
  - Save report templates for future use

- **🔍 External Doctor Integration**
  - Maintain a database of referring physicians
  - Link specimens and results to external doctors
  - Share reports with referring physicians

## 🔧 Technology Stack

- **Backend**: PHP 8.3
- **Database**: MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript, jQuery
- **PDF Generation**: TCPDF
- **Styling Framework**: Custom CSS

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/cabmanage.git
   cd cabmanage
   ```

2. **Database Setup**
   - Create a new MySQL/MariaDB database
   - Import the SQL file from `cabmanage.sql`
   - Configure database connection in `config.php`

3. **Server Configuration**
   - Configure your web server (Apache/Nginx) to point to the project directory
   - Ensure PHP 8.3+ is installed with required extensions

4. **Dependencies**
   - Install required PHP libraries:
   ```bash
   composer install
   ```

## 🚀 Usage

1. **Login**
   - Access the application through your web browser
   - Use the following default credentials:
     - Admin: `admin@gmail.com` / `password123`
     - Doctor: `D1@example.com` / `password123`
     - Assistant: `A1@example.com` / `password123`

2. **Patient Management**
   - Create new patient profiles
   - Search for existing patients
   - Update patient information

3. **Specimen Management**
   - Register new specimens
   - Link specimens to patients and doctors
   - Track specimen status

4. **Billing**
   - Create invoices for patient services
   - Process payments
   - Generate financial reports

5. **Reporting**
   - Create medical reports for specimens
   - Generate and print patient documentation
   - Save report templates for future use

## 📊 Dashboard

The dashboard provides at-a-glance information including:
- Total number of patients
- Total number of specimens
- Daily financial statistics
- Payment status analytics
- Patient history with payment details

## 🔐 Security Features

- Password hashing for secure user authentication
- Role-based access control
- Input validation and sanitization
- Session management and protection

## 🛠️ Development

### Project Structure

```
cabmanage_v1/
├── auth.php               # Authentication system
├── config.php             # Database configuration
├── DocteurExterieur.php   # External doctor management
├── Examen.php             # Examination types
├── Facture.php            # Billing and invoices
├── Patient.php            # Patient management
├── Prelevement.php        # Specimen management
├── Template.php           # Report templates
├── Front/                 # Frontend assets
│   ├── imag/              # Images
│   └── *.css              # Stylesheets
├── *.php                  # Application pages
└── vendor/                # Dependencies
```

### Key Classes

- `Patient`: Manages patient records and history
- `Prelevement`: Handles specimen collection and tracking
- `Facture`: Manages billing and payment tracking
- `Examen`: Defines examination types and prices
- `Template`: Manages report templates

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributors

- Initial development by Twarga
## 📞 Support

For support, please contact
