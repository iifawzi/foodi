# Foodi - An example approach for implementing a Clean/Hexagonal Architecture In PHP with Laravel

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
the stock of each of the ingredients so it reflects the amounts consumed.
Also when any of the ingredients' stock level reaches 50%, the system should send an
email message to alert the merchant that they need to buy more of this ingredient.

In the sections below, I will explain the architecture, decisions, and thoughts.

## Installation

The system is configured in a way that it can work with `MySQL`, `Postgres`, and `SQLite`. if you're willing to run it manually you can follow the following steps:

1. Install composer dependencies:

    ```bash
    composer install
    ```

2. Create your configuration file `.env`:

    ```
    cp .env.example .env
    ```
3. Create Application key
    ```
    php artisan key:generate        
    ```
4. Configure the database connections as you wish
5. Configure the SMTP mailing server for mailing notifications.
6. Run database migrations and seeders
    ```
    php artisan migrate --seed
    ```
7. Run the application in your preferred way, either it's `valet`, `serve`, or any other way.
    ```bash
    php artisan serve
    ```
8. Start the queue and schedular workers to handle notifications

    ```
    ./vendor/bin/sail php artisan queue:work
    ```

    ```
     ./vendor/bin/sail php artisan schedule:work
    ```

9. Enjoy your order!

### Using Sail

The project also comes with Laravel Sail that runs `MySQL` by default. if you wish, you can follow the following steps:

1. Install composer dependencies:

    ```bash
    composer install
    ```

2. Create your configuration file `.env`:

    ```
    cp .env.example .env
    ```

3. Create Application key
    ```
    php artisan key:generate        
    ```

4. Configure the SMTP mailing server for mailing notifications.

5. Start the Docker containers:

    ```
    ./vendor/bin/sail up -d
    ```

6. Run database migrations and seeders:

    ```
    ./vendor/bin/sail php artisan migrate --seed
    ```

7. Start the queue and schedular workers to handle notifications

    ```
    ./vendor/bin/sail php artisan queue:work
    ```

    ```
     ./vendor/bin/sail php artisan schedule:work
    ```

8. Enjoy your order!

## tl;dr

### Create Order Endpoint:

```
POST /api/v1/orders
```

Request:

```json
{
    "merchantId": 1,
    "products": [
        {
            "product_id": 1,
            "quantity": 1
        }
    ]
}
```

## Deep-Dive!

The system is built to be dependable, able to handle many orders at once, and be straightforward to test while maintaining high-quality standards.

### System Architecture

![Untitled Diagram drawio (2)](https://github.com/iifawzi/foodi/assets/46695441/a6f7d9b2-3a02-48aa-86e9-e45ec4f50dc8)

Anticipating and mitigating Murphy's Law `if anything can go wrong, it will`, the system architecture takes into account potential challenges:

-   `Concurrency` Challenge: when multiple orders are happening at once, there's a chance they could all think there are enough ingredients, leading to issues like overselling or running out of stock.

-   Mailing Service Reliability: Proactive measures are in place to address potential issues with sending emails. This includes scenarios where the mailing service is non-operational or the mailing queue experiences downtime.

For the concurrency challenge it depends on a lot of factors, what the business is expecting? is it required to respond immediately to the user? or can we `queue` it and respond later with either confirmation or cancellation? I chose to respond immediately and synchronously.

to handle this, I used `transactions` with `exclusive locks`. All operations involved in processing the order, from checking the ingredient stocks to confirmation, are encapsulated within a transaction. This ensures that either all steps succeed, maintaining data consistency, or the entire transaction fails, preventing inconsistent order confirmations. 

In addition to that, an `exclusive` lock is acquired when checking ingredient stocks. This lock ensures that only one order can access and modify the stock data at a time, preventing multiple orders from concurrently depleting the stock. The exclusive lock remains in place until the transaction is committed, safeguarding against race conditions during the critical confirmation phase.

https://github.com/iifawzi/foodi/blob/bf18902bd3a1d7f1a07700d68ceaf0feda75d472/src/Infrastructure/repositories/Eloquent/EloquentStockRepository.php#L16

on the other side, for second challenge, it's critical to notify the merchant about low stock. However, blocking order processing due to notification failures is not an option. 

Initially, the dispatcher was kept outside of the transaction to avoid hindering order flow. But, what if the system went down after order confirmation or if the notification queue was unavailable? This raised concerns about potential data loss.

- `Transactional Outbox`:
To ensure data integrity, the idea of an `outbox` table was introduced. Besides keeping the dispatcher separate, now, the notification log itself is part of the actual transactions. When an order is confirmed, the system logs the notification details in an `low_stock_notification` table if any ingredients ran low. If everything runs smoothly, this log is committed with the transaction. Later, when the worker performs the send mail action, it marks it as `SENT`. otherwise, it's still pending. 

now what happens for pending notifications if they stuck?

- `Scheduler for Resilience`:
To handle scenarios where the queue might be down or the system faces disruptions after order confirmation - notifications are still pending in db - a scheduler was implemented. This scheduler regularly checks the `low_stock_notification`, specifically the outbox table, every 15 minutes. If it discovers any stuck notifications (those not marked as SENT, hasn't been updated for 30 min ), it dispatches them to the queue for processing.

We also needed to keep in mind that it might fail for the second time, we need to ensure that the job is dispatched only once. for that, we're retrieving and updating the updated_at in a transaction, so we're sure the logic that pulls from db, doesn't pull it twice after it passes 30 min. 

https://github.com/iifawzi/foodi/blob/a7691f72648df499dfbcb219960a14081ed5ef8b/src/Infrastructure/repositories/Eloquent/EloquentStockNotificationRepository.php#L30-L47

This way, we're always sure that it can self-recover from failures, but still, if it can go wrong, it it will. We need to keep in account that a job might for any reason be dispatched twice. We don't need to send the email twice `Idempotency`. For this, the job logic is not only retrieving by the notification id, but it also find by the status, and it update it immediately when sent. 

https://github.com/iifawzi/foodi/blob/a7691f72648df499dfbcb219960a14081ed5ef8b/src/Infrastructure/repositories/Eloquent/EloquentStockNotificationRepository.php#L49-L55

Some race conditions might still happen, the mail services usually can ensure `idempotency` as well, ensuring they're not sending the email twice. 

### Code Architecture

![Untitled-2023-12-02-0248](https://github.com/iifawzi/foodi/assets/46695441/36370bab-b3a9-4677-9471-eb21711daac1)

The way I've organized the code follows `SOLID` and `Hexagonal Architecture` principles, while isolating the domain layer following `Domain Driven Design` techniques, making the code modular, testable, and easier to maintain.

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

The business logic — the rules and processes we all understand — is encapsulated within the `Domain` directory. This is the common language that resonates with developers, stakeholders, program managers, and everyone involved in the project. It serves as a foundational agreement that unites us in our shared understanding. This also helped in testing and verifying the entire domain logic before thinking about any infrastructure details.

##### Key Components in the Domain

-   Entities:

The heart of the domain is the entities. These hold essential data, representing real-world concepts like orders, ingredients, and the specifics of the food we love. These entities act as the backbone of the system, defining what data we work with and how it relates.

Implemented entities: `Merchant`, `Item`, `Ingredient`, `stockItem`, `Order`, and `StockTransactions`. Stock Transaction contains all the transactions (logs) that occurs on the stocks.   

-   Use Cases:

Within the `use cases`, we zoom in on specific scenarios, like creating an order. Here, use cases focus on the detailed steps and logic involved in executing a particular use case. This approach keeps our business logic organized and easy to follow.

The only use-case is `CreateOrder`, it's responsible for checking the stocks and allocate the ingredients (`consume` call)

https://github.com/iifawzi/foodi/blob/5b2a2136debde1d6aadbfe33e4a5774d434c7741/src/Domain/Services/OrderUseCases.php#L18-L36

-   Isolation and Dependency Management

The domain is deliberately isolated, meaning it operates independently of any infrastructure-related logic. it allows us to maintain a clear distinction between what our system does (business logic) and how it does it (infrastructure logic).

https://github.com/iifawzi/foodi/blob/0aa62ae42c20c732d817cde111b30b846647c1e0/src/Application/services/OrderService.php#L15-L26

As you see, the dependencies are inverted, the service is communicating with the abstractions, and thanks to the Service providers, they're injected. 

https://github.com/iifawzi/foodi/blob/dbb5593ed7f34d5b0d6c237c870ca5e8f64fba39/app/Providers/AppServiceProvider.php#L32-L42

-   Dependency Injection:

as the code above shows, to facilitate this separation, we adopt a dependency injection approach. Instead of the application layer reaching out to infrastructure components, dependencies are injected into it, thanks to the defined interfaces.

This ensures flexibility and simplifies testing, as we can substitute real implementations with mocks, as we did in the integration tests.
where the entire business logic is tested using `in-memory` database. More on that, in the `Testing and Quality` section below.

https://github.com/iifawzi/foodi/blob/0aa62ae42c20c732d817cde111b30b846647c1e0/tests/Integration/Application/OrderServiceTest.php#L40-L54

The actual implementation of the repositories is on the infrastructure layer, where we can decide what to use, whether are we using `Eloquent` or any other solution, it doesn't matter. as long as they implement the repositories interfaces.

##### Application layer:

It mediates communication between core business logic (domain) and external systems (infrastructure), when ere I'm defining the `driven` ports, for external components to interact with the application layer.

The application layer is the layer that's responsible for the communication between the domain and the infrastructure, it defines the `driven` and `driving` ports.
for simplification in this project, I didn't implement any `driving` ports, the application service communicates directly with the domain's service. `driven` ports are defined in the repositories directory and the mail service. these ports must be implemented by anyone willing to interact/to be managed with/by the domain.

The application services are also infrastructure agnostic, hence you will notice that no `HTTP` errors are thrown for example, but instead, domain responses are returned.

https://github.com/iifawzi/foodi/blob/878ce9645f1655b725797233e122e71c468d004a/src/Application/services/OrderService.php#L57-L76

This gives us the flexibility of choosing any adapter in the infra, whether it's RPC, REST, or even socket layer. it doesn't matter.

##### Infrastructure layer:

The infrastructure layer serves as the foundation for a software system, housing implementations of the adapters both, the `repositories` and the `mailing service`. In this layer, you'll find the `eloquent` repositories implementations. on the other side, the `driven` adapters are defined in the core directory `app`. The infrastructure layer handles the technical and operational aspects that support the application's functionality.

### Testing and Quality - Continuous Integration

In the collaborative landscape of open source, I've gleaned invaluable insights into the pivotal role tests play. They not only enhance the reliability of code but also foster a collaborative and sustainable development environment. hence I always try to give testing a priority, I experienced a mess when we needed to do manual regression tests on systems that have been written for years. 

The domain logic is secured with focused `unit tests` validating each entity, while the use-case is ensured through `integration` tests employing a mocked database - thanks to di - to verify that it's working as expected, while the entire end-to-end functionality is verified using comprehensive `end-to-end` tests. Achieving a total coverage of 87% with 100% coverage of the core logic!

<img width="934" alt="Screenshot 2023-12-14 at 02 04 21" src="https://github.com/iifawzi/foodi/assets/46695441/7962d6d5-2b78-4df0-8e11-ceb6e3bc4f4f">

you can run the coverage test using: 

```php
php artisan test --coverage-html /coverage    
```

- please note that after running any tests, you need to refill the database if you will use it again outside the tests.

When it comes to the quality, PHPStan knows better. PHPStan for used for static analysis to enforce accurate typings and coding standards. Additionally, both PHPStan and PHPUnit are integrated into the Continuous Integration pipeline, triggering checks whenever any PHP file is pushed on main. 

<img width="1353" alt="image" src="https://github.com/iifawzi/foodi/assets/46695441/fdc98f09-cf5d-4e65-8d80-1f10e4e6052d">

## Thank you! 

Thank you for reading all of this, What makes a great code base and improves it, is having feedback from colleagues and experienced people like YOU (YES, the reader)👨🏻‍💻. if you came by this, I would love to hear your opinions/feedback and discuss different approaches, with you. 
