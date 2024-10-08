```md
# Laravel Developer Showdown - API Integration

## Overview

This project demonstrates how to handle API rate-limited batch processing and individual user attribute updates using Laravel. The application listens for changes in user attributes and triggers API requests to synchronize the updated data with a third-party provider, adhering to the provider's request limits.

## Features

- **User Change Detection**: Automatically detects when a user's attributes (name, email, timezone) are updated and queues them for API synchronization.
- **Batch API Requests**: Collects user changes into batches of up to 1,000 users and sends them via the API. Limits batch requests to 50 per hour as per the API provider's constraints.
- **Individual Requests (optional)**: Supports sending individual updates to the API with a rate limit of up to 3,600 requests per hour.
- **Command Scheduling**: Uses Laravel's scheduler to ensure batch requests are sent every minute, respecting API rate limits.
- **Queue Processing**: Leverages Laravel’s queue system to handle asynchronous API requests for both batch and individual updates.

## Setup

### Prerequisites

- PHP >= 8.0
- Composer
- Laravel 9.x
- MySQL or any database supported by Laravel
- A third-party API to handle user updates

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Alexanderndi/laravel-developer-showdown_ndifreke_eduo.git
   cd laravel-developer-showdown_ndifreke_eduo
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up environment variables**:
   - Duplicate `.env.example` and rename it to `.env`.
   - Update your database and API credentials in the `.env` file:
     ```bash
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database
     DB_USERNAME=your_username
     DB_PASSWORD=your_password

     API_URL=https://api.example.com
     API_KEY=your_api_key
     ```

4. **Run database migrations**:
   ```bash
   php artisan migrate
   ```

5. **Seed the database** with users:
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

6. **Start the Laravel server**:
   ```bash
   php artisan serve
   ```

7. **Start the queue worker**:
   ```bash
   php artisan queue:work
   ```

### API Integration

The project integrates with a third-party API to update user attributes. The API has the following limitations:
- **50 batch requests per hour**, where each batch can contain up to **1,000 records**.
- **3,600 individual requests per hour**.

Batch API payload example:
```json
{
    "batches": [{
      "subscribers": [
        {
          "email": "alex@acme.com",
          "time_zone": "Europe/Amsterdam"
        },
        {
          "email": "hellen@acme.com",
          "name": "Hellen",
          "time_zone": "America/Los_Angeles"
        }
      ]
    }]
}
```

## Usage

### Detecting User Updates

The application automatically detects user attribute changes (e.g., name, email, timezone) and queues them for API synchronization using a Laravel job. This is done by listening for the `User::updated` event.

### Batch Updates

Batch updates are handled via a Laravel Artisan command:
```bash
php artisan send:user-batches
```

This command groups users into batches of 1,000 and sends them to the API, adhering to the limit of **50 batch requests per hour**.

### Scheduling Batch Updates

The batch updates are scheduled to run every minute to ensure that we do not exceed the limit of 50 batch requests per hour. The scheduler is defined in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('send:user-batches')->everyMinute();
}
```

To start the Laravel scheduler, run:
```bash
php artisan schedule:work
```

## Testing

You can test the API integration by:
1. Updating user attributes (e.g., via tinker or directly through a web form).
2. Monitoring the logs or using a tool like `ngrok` to ensure the API requests are being sent correctly.

### Example Test:
- Update a user's name or timezone using Laravel Tinker:
  ```bash
  php artisan tinker
  ```
  ```php
  $user = \App\Models\User::first();
  $user->name = 'New Name';
  $user->timezone = 'CST';
  $user->save();
  ```

This will trigger a job to send the updated user details to the third-party API.

## Contribution

Feel free to fork the repository, make improvements, and open pull requests. Suggestions and contributions are highly encouraged!

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
