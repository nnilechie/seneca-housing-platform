# seneca-housing-platform

A secure housing application connecting international students and professionals with Ontario landlords. It features document validation, alternative credit scoring, and rental guarantees, eliminating barriers for tenants while providing security for property owners.

---

## Overview

Welcome to the Seneca Housing Platform—a rental solution designed to connect Seneca Polytechnic students and expats (from the US, UK, EU, and Canada) with landlords in Ontario. Our mission is to create a fair, inclusive, and secure rental process that addresses the challenges of the traditional Ontario housing system. We aim to expand the rental market by accepting international credentials while mitigating landlord concerns such as delayed payments, property abuse, and misuse of Ontario rental policies.

This platform was conceived by **TEAM X**, a group of six students at **Seneca College**, as their final project for the **Business Information Technology** program (Programming Project PRG800). We want to make a real difference in Ontario’s housing landscape by bridging the gap between international newcomers and local landlords.

---

## Table of Contents
- [Overview](#overview)
- [Why We Built This](#why-we-built-this)
- [What It Does](#what-it-does)
- [Eligibility Criteria](#eligibility-criteria)
- [Application Process](#application-process)
- [Point System Explanation](#point-system-explanation)
- [Example Scenarios](#example-scenarios)
- [Landlord Protections](#landlord-protections)
- [Additional Policies](#additional-policies)
- [How It’s Built](#how-its-built)
- [Project Structure](#project-structure)
- [How to Use It](#how-to-use-it)
- [Challenges We Faced](#challenges-we-faced)
- [What’s Next](#whats-next)
- [Final Thoughts from TEAM X](#final-thoughts-from-team-x)
- [Contact Us](#contact-us)

---

## Why We Built This
As Seneca students, we’ve heard too many stories of international students and expatriate professionals struggling to secure housing in Ontario. With no local credit history or references, these newcomers face repeated rejections. Meanwhile, landlords are wary of renting without reliable verification. Our platform democratizes housing access for newcomers while protecting property owners with advanced verification systems and financial guarantees.

---

## What It Does
The Seneca Housing Platform offers:
- **Document Validation**: Verifies tenant information to ensure legitimacy.
- **Credit Assessment**: Evaluates international tenants who lack a Canadian credit history.
- **Rental Mediation Services**: Facilitates communication between tenants and landlords.
- **Financial Guarantees**: Provides landlords with a safety net when renting to newcomers.

Our goal is to build trust—helping tenants find a home while giving landlords the confidence to rent.

---

## Eligibility Criteria
We welcome applications from:
- **Seneca Polytechnic Students**: Must be currently enrolled or accepted at Seneca Polytechnic.
- **Expats**: Individuals from the US, UK, EU, or Canada, with valid residency in Canada (e.g., Permanent Resident, work permit, or pending application with proof of submission).

---

## Application Process
1. **Submission**: Complete the rental application form on the property listing page. Provide personal information, proof of eligibility, payment assurance, and legal documents.
2. **Verification**: We verify your identity, enrollment (for students), employment/credit (for expats), and payment capability using automated tools and APIs.
3. **Scoring**: Each application is scored out of 100 points based on the criteria below. A minimum score of 70 is required for approval.
4. **Landlord Review**: Approved applications are sent to the landlord for final confirmation. Landlords may request additional documents if needed.
5. **Payment & Contract**: Upon landlord approval, you’ll be prompted to make the initial payment and sign the rental contract digitally (using the Ontario Standard Form of Lease).

---

## Point System Explanation
To ensure fairness and consistency, we use a point-based system to evaluate applications:

### Identity Verification (30 points)
- A government-issued ID (e.g., passport, driver’s license) is required.
- Points are awarded if the ID is verified as legitimate via our third-party verification service (e.g., Onfido).
- Failure to verify the ID results in 0 points.

### Payment Assurance (40 points)
- **For Seneca Students**:
  - **Option 1**: Sponsor Support – Provide a sponsor’s bank statement and a notarized pledge to cover delayed payments.
  - **Option 2**: Canadian Bank Deposit – Provide proof of a deposit into a Canadian bank account.
- **For Expats**:
  - Provide proof of employment (e.g., pay stubs, tax returns) from Canada or your country of origin.
- Full points are awarded if the required documentation is fully provided and verified; missing or unverifiable payment proof results in 0 points.

### Credit and Rental History (30 points)
- **Credit Check (20 points)**:
  - Optional for students, required for expats.
  - A credit score of 650 or higher (from Canada, US, UK, or EU) earns full points.
  - If no credit check is authorized (for students), this portion is skipped.
- **Past Rental History (10 points)**:
  - Optional for all applicants.
  - Providing verifiable rental history (e.g., reference letter, payment receipts) earns a bonus of 10 points.
  - Without a credit check or rental history, 0 points are awarded in this category.

**Minimum Score for Approval**: Applications scoring 70 or higher are automatically approved and forwarded to the landlord for final review. Scores below 70 are flagged for review, with feedback provided and an option to resubmit.

---

## Example Scenarios
- **Seneca Student with Sponsor:**
  - ID verified: **30 points**
  - Sponsor bank statement + notarized pledge: **40 points**
  - No credit check, no rental history: **0 points**
  - **Total: 70 points – Approved**

- **Expat with Employment:**
  - ID verified: **30 points**
  - Pay stub provided: **40 points**
  - Credit score 700: **20 points**
  - Rental history provided: **10 points**
  - **Total: 100 points – Approved**

- **Seneca Student with Missing Docs:**
  - ID verified: **30 points**
  - No payment proof: **0 points**
  - No credit check, no rental history: **0 points**
  - **Total: 30 points – Flagged for review**

---

## Landlord Protections
To address common landlord concerns:
- **Delayed Payments**:
  - Students must provide sponsor pledges or bank deposits.
  - Quarterly payment options reduce the upfront financial burden.
  - Tenant agreements include a commitment to timely payments.
- **Property Abuse**:
  - An optional $500 refundable damage deposit is available to cover potential damages.
  - Tenant agreements require maintaining the property in good condition.
- **Policy Misuse**:
  - Legal ID and residency verification ensure compliance with Ontario laws.
  - Detailed application scoring reduces the risk of fraudulent tenants.

---

## Additional Policies
- **Damage Deposit**: If you opt for the $500 refundable damage deposit, it will be returned at the end of your lease—minus deductions for damages beyond normal wear and tear, per Ontario’s Residential Tenancies Act.
- **Quarterly Payments**: Selecting “Quarterly Payments (3 Months, Auto-Renewable)” means the lease auto-renews every 3 months unless canceled with 60 days’ notice, in accordance with Ontario law.
- **Data Privacy**: All personal information and documents are securely stored and encrypted, complying with GDPR and PIPEDA standards.
- **Non-Discrimination**: Applications are evaluated solely on the point system, ensuring no bias based on nationality, student status, or other factors.

---

## How It’s Built
Our platform is developed using a customized **WordPress** setup with the **Houzez real estate theme**. The tech stack includes:
- **WordPress & PHP**: Custom templates and functions that power the platform.
- **MySQL Database**: Manages user and rental application data.
- **Custom Plugins**: Handle core functionalities (e.g., 'Seneca Housing Dependencies').
- **Shortcodes & Widgets**: Implement dynamic elements like the 'Rental Applicant Dashboard' via `[raf_applicant_dashboard]`.
- **JavaScript & jQuery**: Enhance the user experience with AJAX-powered content updates.
- **CSS**: Maintains a clean and consistent UI.

---

## Project Structure
Here's a breakdown of key folders in this repository:
```
/seneca-housing-platform
│── public_html/themes/houzez-child                   # Custom child theme for Houzez
│── public_html/themes/houzez-child/template          # Custom template overrides for theme customization
│── public_html/plugins/                              # Custom plugins handling core functionalities
│── public_html/plugins/seneca-housing-dependencies   # Custom pluginto handling API Management and additional PHP dependencies
│── public_html/wp-includes/assets                    # Stores CSS, JS, and images for UI improvements
│── public_html/wpconfig.php                          # The base configuration for WordPress and Database initialization scripts 
└── README.md                                         # Project documentation
```
This structure ensures modularity and facilitates future extensions.

---

## How to Use It
### For Tenants:
1. **Create an account** and log in.
2. **Search for properties** based on preferences and budget.
3. **Apply for a property** by submitting rental applications and uploading documents.
4. **Make necessary payments** for rent, deposit, or services.
5. **Sign rental contracts** digitally within the platform.
6. **Communicate with landlords or agents** via the built-in messaging system.

### For Landlords:
1. **Create an account** and log in.
2. **List properties**, including descriptions, images, and rental conditions.
3. **Review tenant applications** and verify submitted documents.
4. **Approve or reject applications** based on background checks.
5. **Communicate with potential tenants** for further clarifications.
6. **Manage rental agreements** and facilitate digital contract signing.
7. **Subscribe to a membership plan** for enhanced listing features and priority placements.

*For debugging issues, check `wp-content/debug.log` or use your browser’s developer tools (F12).*

---

## Challenges We Faced
- **Menu Visibility Issue**: The "Rental Applications" menu item sometimes disappears due to JavaScript conflicts with the AJAX-based rendering system. A JavaScript workaround has been implemented, but further refinements are ongoing.
- **AJAX Loading**: Dynamically updating the dashboard without breaking WordPress hooks has been challenging—especially with integration into the default theme’s AJAX framework.
- **Custom Credit Scoring**: Developing a fair, data-driven alternative credit assessment model remains an ongoing challenge.
- **Membership System**: Designing a flexible subscription model for landlords that supports tiered access to platform features.

---

## What’s Next
Future improvements include:
- **Smarter Credit Assessment**: Enhancing the scoring model for international tenants.
- **External API Integrations**: Incorporating faster verification through external document validation services.
- **Better Financial Guarantees**: Exploring options like rental insurance or alternative deposit models.
- **Role-Based Access**: Restricting platform features based on user type (tenant, landlord, admin).
- **Optimized Membership Tiers**: Offering different levels of features based on landlord subscription plans.

---

## Final Thoughts from TEAM X
Building the Seneca Housing Platform was more than a project assignment—it was about addressing a real issue that international students and expatriates face in Ontario. We poured our hearts into this project, and we hope it contributes to a more accessible and inclusive housing market.

---

## Contact Us
If you have questions about the application process, scoring, or policies, please reach out to us.

---

[GitHub Repo](https://github.com/nnilechie/seneca-housing-platform)
