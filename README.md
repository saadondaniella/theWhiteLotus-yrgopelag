# Yrgopelag – Cozea Island

This project is part of the Yrgopelag assignment and represents a hotel booking system for Cozea Island, home to the exclusive resort The White Lotus.

The application allows tourists to explore rooms, check availability, select island features, and complete a booking using the Central Bank system.

---

## Project Overview

The focus of this project is to combine a clear booking flow with a simple game mechanic and a hotel manager strategy that encourages tourists to select additional features.

The solution is desktop-only, as specified in the assignment.

---

## Key Features

- View available rooms with descriptions and prices
- Check room availability for January 2026
- Select island features connected to categories and tiers
- Automatic price calculation based on room, nights, and selected features
- Tier-based discount when completing a full tier
- Booking confirmation using the Central Bank API
- Consistent visual design across all pages

---

## Game Mechanics – Tier Collection

Each feature belongs to:

- a category (water, games, wheels, hotel-specific)
- a tier (economy, basic, premium, superior)

A complete tier is achieved by selecting one feature from each category within the same tier.

When a tourist completes a full tier, a 20 percent discount is automatically applied to the total booking price.

This implementation aligns with the tier-based point system described in the Yrgopelag rules.

---

## Hotel Manager Strategy

The hotel offers a discounted package price for bookings that include both a room and a complete tier of island features.

This fulfills the star requirement in the assignment and demonstrates how game mechanics can be used to increase feature usage and revenue.

---

## Central Bank Integration

The booking flow integrates with the Central Bank system in the following steps:

1. The tourist calculates the total price
2. A transfer code is created in the Central Bank
3. The transfer code is validated
4. A receipt is sent to the Central Bank
5. The amount is deposited to the hotel

Sensitive configuration values are handled using environment variables.

---

## Environment Variables

The project uses a .env file for sensitive configuration such as API keys.

Example:

```env
CENTRALBANK_API_KEY=your_api_key_here
HOTEL_OWNER_USER=your_hotel_username
```

## Tech Stack

- PHP
- SQLite or MySQL
- HTML and CSS
- JavaScript (minimal usage)
- Central Bank API

## Code review from Hanna
index.php: 10-15 - You could save images in database to make it more dynamic, than rewriting the code

index.php: 19-26 - Another way is to skip making an array and use the database query response directly in the code for displaying the rooms on line 78-95.

theisland.php: 11 - You could save island-info as the name in this case in database or as saved array to require to reuse throughout the project, for easier update if some info changes.

features.php: 5-6 - you could have an autoload in your project to require files that needs to be used through the whole project

features.php: 8-38 - you could save the array in database to maybe make it more dynamic if some or all info changes to make it simpler to update

Contact.php: 5 - you could use $_SESSION to save errors and such if you use sessionstart() in your project

Root: you could put some of the root files in own directory, ex contact.php, theisland.php, features.php, you could put config in 
