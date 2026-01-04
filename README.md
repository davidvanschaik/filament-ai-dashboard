# AI Monitoring Dashboard

Een Laravel/Filament dashboard voor het monitoren van AI-activiteiten binnen Baudex, een AI-platform voor bouwbedrijven. 
Het dashboard geeft inzicht in model-gebruik, token-verbruik, kosten en taak-uitvoeringen.

## 📋 Over het Project

### Wat doet dit dashboard?
Baudex is een AI-platform dat bouwbedrijven helpt bij het analyseren van projectdocumenten, risico-analyses en calculaties.
Dit dashboard monitort:
- Welke AI-modellen worden gebruikt en hoe vaak
- Token-verbruik en kosten per model en project
- Opslag-gebruik (vector stores en bestanden)
- Prestaties van uitgevoerde taken

### Tech Stack
- **Backend**: Laravel 12, PHP 8.2+, MySQL
- **Frontend**: Filament 4.0, Livewire, TailwindCSS 4
- **API**: OpenAI API (met Mock API voor development)

## 📊 Database Structuur

### Models

#### **Project**
Bevat Baudex projecten waarvoor AI-analyses worden uitgevoerd.
- `id`, `name`, `created_at`, `updated_at`

#### **Message**
Slaat alle AI-interacties op met token-informatie.
- `id`, `project_id`, `type`, `metadata` (JSON)
- `input_tokens`, `input_cached_tokens`, `output_tokens`
- `created_at`, `updated_at`

#### **Task**
Definieert AI-taken zoals "Risico Analyse" of "Calculatie".
- `id`, `name`, `created_at`, `updated_at`

#### **TaskRun**
Registreert elke taak-uitvoering met performance metrics.
- `id`, `task_id`, `message_id`, `duration` (seconds)
- `created_at`, `updated_at`

## 📁 Project Structuur
```
app/
├── Filament/
│   ├── Pages/                 # Dashboard pagina's (AiDashboard, ModelsDetail, UsageDetail, JobsDetail)
│   └── Widgets/               # Dashboard widgets (Models, Usage, Storage, Jobs)
│
├── Models/                    # Eloquent models (Project, Message, Task, TaskRun)
│
├── Services/                  # Business logica
│   ├── AiModelService.php     # Model usage verwerking
│   ├── UsageService.php       # Token/kosten berekeningen
│   ├── StorageService.php     # Opslag statistieken
│   └── FilterService.php      # Filter logica voor tijd-periodes
│
├── Repositories/              # Database queries
│   ├── MessageRepository.php
│   └── TaskRunRepository.php
│
├── OpenAI/
│   └── OpenAiClient.php       # OpenAI API wrapper
│
└── Jobs/                      # Asynchrone taken
    ├── FetchVectorStoreJob.php
    └── FetchFileJob.php

config/
├── api.php                    # API endpoints configuratie
└── pricing.php                # Model token pricing

database/
├── migrations/                # Database schema
├── factories/                 # Test data factories
└── seeders/                   # Database seeders

tests/
├── Unit/                      # Unit tests voor individuele classes
└── Integration/               # Integration tests voor component interactie
```

## 🧪 Testing

Het project gebruikt PHPUnit voor geautomatiseerde tests:

### Test Coverage
- ✅ **Services** - Data transformatie en aggregatie logica
- ✅ **Repositories** - Database query correctheid
- ✅ **API Client** - API response handling
- ✅ **Widgets** - Data processing voor dashboard
- ✅ **Filters** - Filter functionaliteit

### Tests Uitvoeren

```bash
# Alle tests
php artisan test
```

## 🚀 Lokale Setup

### Vereisten
- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x
- MySQL >= 8.0

### Installatie Stappen

```bash
# 1. Clone repository
git clone <repository-url>
cd portfolio

# 2. Installeer dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configureer database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=ai_monitoring
# DB_USERNAME=root
# DB_PASSWORD=jouw_wachtwoord

# 5. Database setup
php artisan migrate
php artisan db:seed

# 6. Maak admin user
php artisan make:filament-user

# 7. Build assets
npm run dev

# 8. Start development server
php artisan serve

# 9. Start API server
php artisan serve --port=3000

# 10. Queue worker voor async jobs
 php artisan queue:work --timeout=300 --sleep=3 --tries=3      
```
Log vervolgens met je credentials in op: http://localhost:8000/admin

### Development URLs
- **Dashboard**: http://localhost:8000/admin
- **Login** met de credentials die je bij stap 6 hebt aangemaakt

## 📝 Dashboard Features

### Widgets (Overzichtspagina)
- **Models**: Top 3 meest gebruikte AI-modellen
- **Usage**: Totaal token-verbruik en kosten
- **Storage**: Vector stores en bestand-statistieken
- **Jobs**: Taak-uitvoerings frequentie

### Detail Pagina's
- **Models Detail**: Requests en token-verbruik per model over tijd
- **Usage Detail**: Verbruik per project en per model
- **Jobs Detail**: Uitvoeringen, token-verbruik en responstijd per taak

Alle pagina's ondersteunen filters op tijdsperiode en toggle tussen tokens/euro's.
