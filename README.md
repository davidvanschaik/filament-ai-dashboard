# AI Monitoring Dashboard
A Laravel/Filament dashboard for monitoring AI activities from your OpenAI API. The dashboard has 4 widgets that show OpenAI 
API statistics. The plugin has a local API with mock data to test the dashboard and detailpages. 

## About the Project
This project is all about monitoring your OpenAI API activity. The dashboard is designed so the admin can monitor the total 
activity of the models, usage, storage and tasks. (The dashboard is originally designed for a system that uses AI to execute 
specific type of tasks). The widgets are all clickable to redirect the user to the detailpage of the widget.

### Tech Stack

* **Backend**: Laravel 12, PHP 8.2+, MySQL
* **Frontend**: Filament 4.0, Livewire, TailwindCSS 4
* **API**: OpenAI API (with Mock API for development)

## Dashboard Features

### Widgets (Overview Page)
* **Models**: Top 3 most-used AI models
* **Usage**: Total token consumption and costs
* **Storage**: Vector store and file statistics
* **Jobs**: Task execution frequency

### Detail Pages

* **Models Detail**: Requests and token usage per model over time
* **Usage Detail**: Usage per project and per model
* **Jobs Detail**: Executions, token usage, and response time per task
* **Storage Detail:** Manage storage records from OpenAI.

## Installation Steps

### Requirements
- **PHP ^8.2:**
- **Laravel framework ^12.0**
- **Filament ^4.0**
- **Tailwind CSS ^4.0**

To use the dashboard create a new Laravel/Filament project or install it in you existing project by running the command:
```
composer require davidvanschaik/filament-ai-dashboard:dev-main
```

### Commands
When the plugin is installed there are several commands to execute to help you set up the package. These commands will:
- Publish the migrations, config files and  data files
- Create a Filament theme or when a theme already exists, add the **@source** tag to the `theme.css` file so all the views will
  be compiled by tailwind
- Able to use the local API to test the dashboard with mock data or use the OpenAI endpoints to retrieve real time data
- Add the env variables to your `.env` file.

#### Install command
To publish the migrations, config and data files run the command:
```php
php artisan filament-ai-dashboard:install
```

This will publish the config files:
* **filament-ai=dashboard.php:** Customize the dashboard by widget order, navigation_group and heading.
* **filament-ai-dashboard-api.php:** Retrieves all the `.env` variables.

Notes
- Models job alleen deze maand ophalen.
- Fallback op laatste maand van activiteit
- 2 grafiekn, requests per maand over hele jaar en tokens
- 
