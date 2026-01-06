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
