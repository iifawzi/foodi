# Foodi Orders!

# Requirements 📺

A Burger (Product) may have several ingredients:

-   150g Beef
-   30g Cheese
-   20g Onion

The system keeps the stock of each of these ingredients stored in the database. You
can use the following levels for seeding the database:

-   20kg Beef
-   5kg Cheese
-   1kg Onion

When a customer makes an order that includes a Burger. The system needs to update the
stock of each of the ingredients so it reflects the amounts consumed.
Also when any of the ingredients stock level reaches 50%, the system should send an
email message to alert the merchant they need to buy more of this ingredient.

In the sections below, I will explain the requirements have been approached, the architecture, decisions and thoughts.

# Installation

The system is configured in a way that it can work with MySQL, Postgres and sqlite. if you're willing to run it manually you can follow the following steps:

1. Install composer dependencies:

    ```bash
    composer update
    ```
2. Create your configuration file `.env`:

    ```
    cp .env.example .env
    ```
3. Configure the database connections as you wish
4. Configure the smtp mailing server for mailing notifications.
5. Run database migrations and seeders 
    ```
    php artisan migrate --seed
    ```
6. Run the application in your preferred way, either it's `valet`, `serve` or any other way. 
    ```bash
    php artisan serve
    ```
7. Start the queue and schedular workers to handle notifications

    ```
    ./vendor/bin/sail php artisan queue:work
    ```

    ```
     ./vendor/bin/sail php artisan schedule:work
    ```

7. Enjoy your order!

## Using Sail

Thr project also comes with Laravel Sail that runs mysql by default. if you wish you can follow the following steps: 

1. Create your configuration file `.env`:

    ```
    cp .env.example .env
    ```
2. Configure the smtp mailing server for mailing notifications.

3. Start the Docker containers:

    ```
    ./vendor/bin/sail up -d
    ```

4. Run database migrations and seeders:

    ```
    ./vendor/bin/sail php artisan migrate --seed
    ```

5. Start the queue and schedular workers to handle notifications

    ```
    ./vendor/bin/sail php artisan queue:work
    ```

    ```
     ./vendor/bin/sail php artisan schedule:work
    ```

# Tl;dr - Summary

The system is designed to be reliable, adept at handling multiple orders concurrently, and easy to test while maintaining a high-quality standard.

## Code Architecture

The code architecture strictly adheres to good design principles, ensuring modularity and maintainability. Rigorous testing of the core components fortifies the system's reliability.

![Untitled-2023-12-02-0248](https://github.com/iifawzi/foodi/assets/46695441/80e714ac-26fa-48cf-80a6-b86257ce6c53)

## System Architecture

Anticipating and mitigating Murphy's Law `if anything can go wrong, it will`, the system architecture takes into account potential challenges:

-   Concurrency Challenge: The system adeptly manages multiple orders simultaneously, ensuring data consistency even during peak times.

-   Mailing Service Reliability: Proactive measures are in place to address potential issues with sending emails. This includes scenarios where the mailing service is non-operational or the mailing queue experiences downtime.

By proactively addressing these challenges, the architecture aspires to furnish a Foodi Orders System characterized by reliability, resilience, and a seamless operational experience.
