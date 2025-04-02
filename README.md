# seneca-housing-platform
A secure housing application connecting international students and professionals with Ontario landlords. Features document validation, alternative credit scoring, and rental guarantees, eliminating barriers for tenants while providing security for property owners.


# Seneca Housing Platform

We are **TEAM X**, a group of six students at Seneca College, and this is our final project for [Your Course/Program Name]: the **Seneca Housing Platform**. As a team, we set out to address a challenge that hits close to home for many of us—housing struggles for international students and expatriate professionals in Ontario. We’ve seen friends and classmates face the frustration of finding a place to live without a local credit history or references, while landlords hesitate to rent without reliable verification. Our platform bridges that gap with **document validation**, **credit assessment**, and **rental mediation services**, creating a trusted ecosystem where newcomers can find housing and property owners feel secure.

This project isn’t just about meeting a school requirement for us, it’s about making a real difference in Ontario’s housing landscape, one tenant and landlord at a time.

---

## Table of Contents
- [Why We Built This](#why-we-built-this)
- [What It Does](#what-it-does)
- [How It’s Built](#how-its-built)
- [Getting Started](#getting-started)
- [How to Use It](#how-to-use-it)
- [Challenges We Faced](#challenges-we-faced)
- [What’s Next](#whats-next)
- [Let’s Collaborate](#lets-collaborate)

---

## Why We Built This
As students at Seneca, we’ve heard too many stories of international students struggling to find housing in Ontario. No credit history, no local references—just a lot of rejection. At the same time, landlords face risks renting to tenants they can’t easily verify. As **TEAM X**, we wanted to tackle this problem head-on. The Seneca Housing Platform **democratizes housing access** for newcomers in Canada while **protecting property owners** with advanced verification systems and financial guarantees. It’s a cause we’re passionate about because we believe everyone deserves a fair chance at a home, no matter where they’re from.

---

## What It Does
Here’s how the Seneca Housing Platform helps:
- **Document Validation**: Verifies tenant information to ensure it’s legitimate, giving landlords confidence.
- **Credit Assessment**: Evaluates credit for international tenants who lack a Canadian credit history, using custom tools.
- **Rental Mediation Services**: Facilitates communication and agreements between tenants and landlords.
- **Financial Guarantees**: Provides landlords with a safety net, like financial backing, when renting to newcomers.

Our goal is to build trust—helping tenants find a home and landlords feel secure in the process.

---

## How It’s Built
We built this platform as a custom theme on WordPress leveraging on the Houzez real estate theme, combining our skills to create a seamless experience. Here’s the tech stack we used:
- **WordPress & PHP**: For custom templates and functions that power the platform.
- **MySQL Database**: For database management, handling all users and site informations 
- **Custom Plugins**: For handling all PHP dependencies and API management like 'Seneca Housing Dependencies'
- **Shortcodes and Widgets**: Like 'rental applicant dashboard' 'WPforms' to display rental application data and custom forms.
- **JavaScript & jQuery**: For dynamic features, such as AJAX loading in the dashboard and other sections of the platform(still in progress).
- **CSS**: To ensure a clean and consistent look across the platform.

It took a lot of teamwork and late nights, but we learned so much about integrating custom features into an existing framework.

---

## Getting Started
Ready to check it out? Here’s how to set up the Seneca Housing Platform:
1. **Clone or Download the Repository**:
   ```bash
   git clone https://github.com/your-username/seneca-housing-platform.git
   ```
   Or download the ZIP file and extract it.

2. **Add to WordPress**:
   - Copy the `houzez-child` folder to `wp-content/themes/` in your WordPress installation.

3. **Activate the Child Theme**:
   - Go to `Appearance > Themes` in the WordPress admin dashboard and activate "Houzez Child."

4. **Set Up the Page**:
   - Ensure there’s a page titled "User Dashboard Rental Applications" with the slug `user-dashboard-rental-applications` and the `[raf_applicant_dashboard]` shortcode.

5. **Enable Debugging (Optional)**:
   - Add this to `wp-config.php` to capture logs:
     ```php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     define('WP_DEBUG_DISPLAY', false);
     ```

---

## How to Use It
- **For Tenants**: Log in, head to your dashboard, and click "Rental Applications" to submit or view your applications. The platform validates your documents and assesses your credit.
- **For Landlords**: Log in, review tenant applications, communicate with applicants, and manage agreements—all from the dashboard.

If something doesn’t work as expected, check the `wp-content/debug.log` or open your browser’s console (F12) for error messages.

---

## Challenges We Faced
As a team of six, we ran into our fair share of hurdles. One of the biggest was getting the "Rental Applications" menu item to appear consistently across all dashboard pages. Houzez’s JavaScript kept re-rendering the menu, which made our custom item disappear on some pages. We added a JavaScript fallback to force it back, but it’s still not perfect. Another challenge was implementing AJAX loading for the dashboard content—Houzez’s AJAX system is tricky, and we’re still working on getting it right. It was a lot of trial and error, but we learned so much about teamwork and problem-solving along the way.

---

## What’s Next
We’re proud of what we’ve built, but there’s more we want to do:
- **Smarter Credit Assessment**: Develop better tools to evaluate international tenants’ credit.
- **External APIs**: Integrate with document verification services for faster processing.
- **Enhanced Guarantees**: Offer options like rental insurance for landlords.
- **Fix AJAX Loading**: Make the dashboard fully dynamic, so content loads smoothly without page refreshes.
- **Role-Based Access**: Add restrictions so only certain users (e.g., tenants or landlords) see specific features.

This project is just the beginning—we’d love to see it grow into a real solution for Ontario’s housing challenges.

---

## Let’s Collaborate
This was our final project at Seneca, but we’d love for the Seneca Housing Platform to keep evolving. If you have ideas or want to contribute, fork the repo, make your changes, and send us a pull request. Let’s work together to make housing in Ontario more inclusive for everyone!

---

### A Final Thought from TEAM X
Building the Seneca Housing Platform was more than a school assignment for us—it’s about solving a problem we’ve seen too many people face. As a team, we poured our hearts into creating a tool that helps international students and expatriates find a home in Ontario, while giving landlords the trust they need to say yes. We hope this project is a small step toward a more welcoming housing market for newcomers. Thanks for checking out our work!