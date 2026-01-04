# AI Monitoring Dashboard

A Laravel/Filament dashboard for monitoring AI activities within **Baudex**, an AI platform for construction companies.
The dashboard provides insight into model usage, token consumption, costs, and task executions.

## 📋 About the Project

### What does this dashboard do?

Baudex is an AI platform that helps construction companies analyze project documents, risk analyses, and cost estimations.
This dashboard monitors:

* Which AI models are used and how often
* Token usage and costs per model and per project
* Storage usage (vector stores and files)
* Performance of executed tasks

### Tech Stack

* **Backend**: Laravel 12, PHP 8.2+, MySQL
* **Frontend**: Filament 4.0, Livewire, TailwindCSS 4
* **API**: OpenAI API (with Mock API for development)

## 📊 Database Structure

### Models

#### **Project**

Contains Baudex projects for which AI analyses are performed.

* `id`, `name`, `created_at`, `updated_at`

#### **Message**

Stores all AI interactions including token information.

* `id`, `project_id`, `type`, `metadata` (JSON)
* `input_tokens`, `input_cached_tokens`, `output_tokens`
* `created_at`, `updated_at`

#### **Task**

Defines AI tasks such as "Risk Analysis" or "Cost Estimation".

* `id`, `name`, `created_at`, `updated_at`

#### **TaskRun**

Records each task execution with performance metrics.

* `id`, `task_id`, `message_id`, `duration` (seconds)
* `created_at`, `updated_at`

## 📁 Project Structure

```
app/
├── Filament/
│   ├── Pages/                 # Dashboard pages (AiDashboard, ModelsDetail, UsageDetail, JobsDetail)
│   └── Widgets/               # Dashboard widgets (Models, Usage, Storage, Jobs)
│
├── Models/                    # Eloquent models (Project, Message, Task, TaskRun)
│
├── Services/                  # Business logic
│   ├── AiModelService.php     # Model usage processing
│   ├── UsageService.php       # Token/cost calculations
│   ├── StorageService.php     # Storage statistics
│   └── FilterService.php      # Time-period filter logic
│
├── Repositories/              # Database queries
│   ├── MessageRepository.php
│   └── TaskRunRepository.php
│
├── OpenAI/
│   └── OpenAiClient.php       # OpenAI API wrapper
│
└── Jobs/                      # Asynchronous jobs
    ├── FetchVectorStoreJob.php
    └── FetchFileJob.php

config/
├── api.php                    # API endpoint configuration
└── pricing.php                # Model token pricing

database/
├── migrations/                # Database schema
├── factories/                 # Test data factories
└── seeders/                   # Database seeders

tests/
├── Unit/                      # Unit tests for individual classes
└── Integration/               # Integration tests for component interaction
```

## 🧪 Testing

The project uses PHPUnit for automated testing.

### Test Coverage

* ✅ **Services** – Data transformation and aggregation logic
* ✅ **Repositories** – Database query correctness
* ✅ **API Client** – API response handling
* ✅ **Widgets** – Data processing for dashboard widgets
* ✅ **Filters** – Filter functionality

### Running Tests

```bash
# Run all tests
php artisan test
```

## 🚀 Local Setup

### Requirements

* PHP >= 8.2
* Composer >= 2.0
* Node.js >= 18.x
* MySQL >= 8.0

### Installation Steps

```bash
# 1. Clone repository
git clone <repository-url>
cd portfolio

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=ai_monitoring
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 5. Database setup
php artisan migrate
php artisan db:seed

# 6. Create admin user
php artisan make:filament-user

# 7. Build assets
npm run dev

# 8. Start development server
php artisan serve

# 9. Start API server
php artisan serve --port=3000

# 10. Queue worker for async jobs
php artisan queue:work --timeout=300 --sleep=3 --tries=3
```

Then log in using your credentials at: [http://localhost:8000/admin](http://localhost:8000/admin)

### Development URLs

* **Dashboard**: [http://localhost:8000/admin](http://localhost:8000/admin)
* **Login** with the credentials created in step 6

## 📝 Dashboard Features

### Widgets (Overview Page)

* **Models**: Top 3 most-used AI models
* **Usage**: Total token consumption and costs
* **Storage**: Vector store and file statistics
* **Jobs**: Task execution frequency

### Detail Pages

* **Models Detail**: Requests and token usage per model over time
* **Usage Detail**: Usage per project and per model
* **Jobs Detail**: Executions, token usage, and response time per task

All pages support time-period filters and toggling between tokens and euros.
