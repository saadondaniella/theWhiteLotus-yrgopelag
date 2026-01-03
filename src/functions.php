<?php

declare(strict_types=1);

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function renderJanuaryCalendar(int $roomId, array $bookedDaysByRoomId, string $title): void
{
    $bookedDays = $bookedDaysByRoomId[$roomId] ?? [];

    echo '<article class="availability-card">';
    echo '<h3 class="availability-title">' . escapeHtml($title) . '</h3>';

    echo '<div class="availability-weekdays">
        <span>mon</span><span>tue</span><span>wed</span><span>thu</span>
        <span>fri</span><span>sat</span><span>sun</span>
    </div>';

    // Jag hårdkodar för jan 2026 startar på torsdag 
    $offset = 3;

    echo '<div class="availability-grid">';

    for ($i = 0; $i < $offset; $i++) {
        echo '<div class="day day--empty"></div>';
    }

    for ($day = 1; $day <= 31; $day++) {
        $isBooked = isset($bookedDays[$day]);
        $class = $isBooked ? 'day day--booked' : 'day day--available';

        echo '<div class="' . $class . '">' . $day . '</div>';
    }

    echo '</div>';

    echo '<div class="availability-legend">
        <span><span class="legend-box legend-box--booked"></span> booked</span>
        <span><span class="legend-box legend-box--available"></span> available</span>
    </div>';

    echo '</article>';
}
