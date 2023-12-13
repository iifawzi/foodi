# Foodi Orders!

![Untitled-2023-12-02-0248](https://github.com/iifawzi/foodi/assets/46695441/80e714ac-26fa-48cf-80a6-b86257ce6c53)

## Requirements 📺

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

## Installation

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

8. Enjoy your order!

### Using Sail

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

7. Enjoy your order!

## Deep-Dive!

The system is built to be dependable, handle many orders at once, and be straightforward to test while maintaining high-quality standards.

### Code Architecture

The way I've organized the code follows `SOLID` and `Hexagonal Architecture` principles while isolating the domain layer following ddd-design, making it easy to understand and maintain.

#### Files Structure
```
src
├── Application
│   ├── ports
│   │   └── infrastructure
│   │       ├── StockNotificationService.php
│   │       └── repositories
│   │           ├── MerchantRepository.php
│   │           ├── OrderRepository.php
│   │           ├── ProductRepository.php
│   │           ├── StockNotificationRepository.php
│   │           └── StockRepository.php
│   └── services
│       └── OrderService.php
├── Domain
│   ├── Entities
│   │   ├── Ingredient.php
│   │   ├── Item.php
│   │   ├── Merchant.php
│   │   ├── Order.php
│   │   ├── StockItem.php
│   │   └── StockTransaction.php
│   ├── Services
│   │   └── OrderUseCases.php
│   └── Types
│       ├── OrderStatus.php
│       ├── StockItemStatus.php
│       └── StockTransactionType.php
└── Infrastructure
    ├── MailingService.php
    ├── repositories
    │   └── Eloquent
    │       ├── EloquentMerchantRepository.php
    │       ├── EloquentOrderRepository.php
    │       ├── EloquentProductRepository.php
    │       ├── EloquentStockNotificationRepository.php
    │       └── EloquentStockRepository.php
    └── types
        └── LowStockNotificationType.php
```

The business logic — the rules and processes we all understand — is encapsulated within the `Domain` directory. This is the common language that resonates with developers, stakeholders, program managers, and everyone involved in the project. It serves as a foundational agreement that unites us in our shared understanding.

##### Key Components in the Domain
- Entities
The heart of the domain is the entities. These hold the essential data, representing real-world concepts like orders, ingredients, and the specifics of a the food we love. These entities act as the backbone of the system, defining what data we work with and how it relates.

- Use Cases
Within the `useCases`, we zoom in on specific scenarios, like creating an order. Here, use cases focus on the detailed steps and logic involved in executing a particular use case. This approach keeps our business logic organized and easy to follow.

- Isolation and Dependency Management
The domain is deliberately isolated, meaning it operates independently of any infrastructure-related logic. This isolation is intentional— it allows us to maintain a clear distinction between what our system does (business logic) and how it does it (infrastructure logic).

    - Dependency Injection
        To facilitate this separation, we adopt a dependency injection approach. Instead of the domain reaching out to infrastructure components, dependencies are injected into it, thanks to the defined interfaces. This ensures flexibility and simplifies testing, as we can substitute real implementations with mocks, as we did in the integration tests. 

        In essence, the domain is a self-contained, understandable, and flexible part of the system. It represents the language we collectively speak in the project.


### System Architecture

Anticipating and mitigating Murphy's Law `if anything can go wrong, it will`, the system architecture takes into account potential challenges:

-   Concurrency Challenge: The system adeptly manages multiple orders simultaneously, ensuring data consistency even during peak times.

-   Mailing Service Reliability: Proactive measures are in place to address potential issues with sending emails. This includes scenarios where the mailing service is non-operational or the mailing queue experiences downtime.

By proactively addressing these challenges, the architecture aspires to furnish a Foodi Orders System characterized by reliability, resilience, and a seamless operational experience.
