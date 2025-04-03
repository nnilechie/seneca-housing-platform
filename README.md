# seneca-housing-platform
A secure housing application connecting international students and professionals with Ontario landlords. Features document validation, alternative credit scoring, and rental guarantees, eliminating barriers for tenants while providing security for property owners.

---

# Seneca Housing Platform

We are **TEAM X**, a group of six students at **Seneca College**, and this is our final project for the **Business Information Technology** program, specifically the **Programming Project (PRG800)** course. Our goal is to address a real-world issue: the housing challenges that international students and expatriate professionals face in Ontario.

Many newcomers struggle to secure rentals due to the lack of local credit history and references. At the same time, landlords hesitate to rent without reliable verification. Our platform bridges this gap through **document validation**, **credit assessment**, and **rental mediation services**, fostering a trusted ecosystem where tenants can find housing and property owners feel secure.

This project isn’t just about meeting academic requirements—it’s about making a real difference in Ontario’s housing landscape.

---

## Table of Contents
- [Why We Built This](#why-we-built-this)
- [What It Does](#what-it-does)
- [How It’s Built](#how-its-built)
- [Project Structure](#project-structure)
- [How to Use It](#how-to-use-it)
- [Challenges We Faced](#challenges-we-faced)
- [What’s Next](#whats-next)
- [Let’s Collaborate](#lets-collaborate)

---

## Why We Built This
As Seneca students, we’ve heard too many stories of international students struggling to find housing in Ontario. No credit history, no local references—just rejection. Meanwhile, landlords face risks renting to tenants they can’t verify. Our platform **democratizes housing access** for newcomers while **protecting property owners** through advanced verification systems and financial guarantees.

---

## What It Does
Here’s how the Seneca Housing Platform helps:
- **Document Validation**: Verifies tenant information to ensure legitimacy.
- **Credit Assessment**: Evaluates international tenants who lack a Canadian credit history.
- **Rental Mediation Services**: Facilitates communication between tenants and landlords.
- **Financial Guarantees**: Offers landlords a safety net when renting to newcomers.

Our goal is to build trust—helping tenants find a home while giving landlords the confidence to rent to them.

---

## How It’s Built
Our platform is built using a customized **WordPress** setup, leveraging the **Houzez real estate theme**. Our tech stack includes:
- **WordPress & PHP**: Custom templates and functions that power the platform.
- **MySQL Database**: Manages user and rental application data.
- **Custom Plugins**: Handles core functionalities like 'Seneca Housing Dependencies'.
- **Shortcodes & Widgets**: Implements elements like the 'Rental Applicant Dashboard' via `[raf_applicant_dashboard]`.
- **JavaScript & jQuery**: Adds dynamic features, such as AJAX-powered content updates.
- **CSS**: Ensures a clean and consistent UI across the platform.

---

## Project Structure
Here's a breakdown of key folders in this repository:
```
/seneca-housing-platform
│── public_html/themes/houzez-child            # Custom child theme for Houzez
│── public_html/themes/houzez-child/template   # Custom template overrides for theme customization
│── public_html/plugins/                       # Custom plugins handling core functionalities
│── public_html/wp-includes/assets             # Stores CSS, JS, and images for UI improvements
│── public_html/wpconfig.php                   # The base configuration for WordPress and Database initialization scripts 
└── README.md                                  # Project documentation
```
This structure ensures modularity, making it easier to extend the project.

---

## How to Use It
- **For Tenants**:
  1. **Create an account** and log in.
  2. **Search for properties** based on preferences and budget.
  3. **Apply for a property**, submit rental applications, and upload documents.
  4. **Make necessary payments** for rent, deposit, or services.
  5. **Sign rental contracts** digitally within the platform.
  6. **Communicate with landlords or agents** via the built-in messaging system.

- **For Landlords**:
  1. **Create an account** and log in.
  2. **List properties**, including descriptions, images, and rental conditions.
  3. **Review tenant applications** and verify submitted documents.
  4. **Approve or reject applications** based on tenant background checks.
  5. **Communicate with potential tenants** for further clarifications.
  6. **Manage rental agreements** and ensure digital contract signing.
  7. **Subscribe to a membership plan** for enhanced listing features and priority placements.

- **Debugging**:
  - If issues arise, check `wp-content/debug.log` or use browser developer tools (F12).

---

## Challenges We Faced
- **Menu Visibility Issue**: The "Rental Applications" menu item sometimes disappears due to Javascript conflicts with AJAX-based rendering system. We implemented a JavaScript workaround but are still refining it.
- **AJAX Loading**: Making the dashboard dynamic without breaking WordPress hooks proved challenging, especially when integrating it with defauolt theme AJAX framework.
- **Custom Credit Scoring**: Implementing a fair, data-driven alternative credit assessment model remains an ongoing challenge.
- **Membership System**: Designing a flexible subscription model for landlords that allows tiered access to platform features.

---

## What’s Next
We plan to improve the platform with:
- **Smarter Credit Assessment**: A more robust scoring model for international tenants.
- **External API Integrations**: Faster verification through document validation services.
- **Better Financial Guarantees**: Rental insurance or deposit alternatives.
- **Role-Based Access**: Restricting certain features based on user type (tenant, landlord, admin).
- **Optimized Membership Tiers**: Offering different levels of features based on landlord subscriptions.

---

[GitHub Repo](https://github.com/nnilechie/seneca-housing-platform)  

Let’s make housing in Ontario more inclusive together!

---

### **Final Thoughts from TEAM X**
Building the Seneca Housing Platform was more than a project assignment, it was about solving a real issue that international students and expatriates face. We poured our hearts into this project, and we hope it contributes to a more accessible housing market in Ontario. Thanks for checking out our work!

