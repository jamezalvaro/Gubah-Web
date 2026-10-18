# 🚀 GuBah (SaaS Innovation Assistant)

[![Status](https://img.shields.io/badge/status-active_development-brightgreen)](#)

*GuBah is a cloud-based Software as a Service (SaaS) platform designed as a comprehensive innovation assistant for MSMEs (UMKM). It bridges available raw materials with AI power to generate product innovation ideas and business plans instantly, while ensuring 100% accuracy in financial projections.*

## 📌 Table of Contents
- [Key Features](#-key-features)
- [Prerequisites](#-prerequisites)
- [Installation Guide](#️-installation-guide)
- [Usage Guide](#-usage-guide)
- [Architecture & Tech Stack](#️-architecture--tech-stack)

## ✨ Key Features
- **Frictionless UI/UX:** Dynamic, comma-separated input forms optimized for high performance and mobile devices (Mobile-First Design).
- **AI Ideation Engine (Free Tier):** Instantly generates product names and complete recipes based on user-inputted raw materials using LLM integration.
- **Deterministic HPP Calculator & Scale-up (Premium Tier):** Calculates Cost of Goods Sold (COGS/HPP), target margins, and production scale mathematically in the backend, completely isolated from AI hallucination risks.
- **Advanced Business Strategy (Premium Tier):** Generates packaging strategies and promotional copywriting based on the calculated recipes.
- **Blueprint PDF Export (Premium Tier):** Downloads a complete summary of recipes, financial calculations, and marketing strategies in a professional PDF format.
- **Automated Subscription Upgrade:** Real-time account upgrade from Free to Premium via asynchronous Webhook integration with Payment Gateways.

## 📋 Prerequisites
Before you begin, ensure you have the following installed on your local machine or server:
- PHP (version 8.1 or higher)
- Composer
- Docker & Docker Desktop (for isolated PostgreSQL environment)
- Node.js (version 18 or higher) & NPM/Yarn
- Web Server (Nginx or Apache / built-in PHP server for local testing)
- Git

## ⚙️ Installation Guide
Clone this repository and install the dependencies:

```bash
# Clone the repository
git clone [https://github.com/yourusername/gubah-app.git](https://github.com/yourusername/gubah-app.git)

# Enter the project directory
cd gubah-app

# Install PHP dependencies
composer install

# Install NPM dependencies and build assets
npm install
npm run build

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Configure your `.env` file with your PostgreSQL credentials, LLM API keys (e.g., Gemini/OpenAI), and Payment Gateway credentials (e.g., Midtrans/Xendit) before proceeding.

```bash
# Start the PostgreSQL database container via Docker
docker compose up -d

# Run database migrations
php artisan migrate

# (Optional) Seed the database with initial SuperAdmin and dummy data
php artisan db:seed
```

## 🚀 Usage Guide
To run the project in development mode:

```bash
php artisan serve
```

```markdown
> 💡 **Tip:** You can access the application at `http://localhost:8000`. Ensure your Docker desktop is running and the PostgreSQL container is active (default mapped to port 5432 or 5433 based on your `.env`). For testing payment webhooks locally, you might need tunneling tools like [Ngrok](https://ngrok.com/).

## 🏗️ Architecture & Tech Stack
- **Backend Framework:** Laravel (PHP)
- **Database:** PostgreSQL (utilizing `JSONB` support for dynamic LLM responses)
- **Frontend:** HTML, CSS, JavaScript (Mobile-First approach with responsive UI)
- **AI Integration:** Google Gemini API / OpenAI API (via HTTP POST)
- **Payment Gateway:** Midtrans / Xendit (Webhook based with Signature Key validation)
- **Design Principle:** Strict architectural separation of **Probabilistic Logic** (AI text generation) and **Deterministic Logic** (Financial backend calculation via server-side scripting).