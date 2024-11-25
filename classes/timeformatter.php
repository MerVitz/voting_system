<?php
class TimeFormatter {
    public static function formatTimestamp(int $timestamp): string {
        $currentTimestamp = time();
        $difference = $currentTimestamp - $timestamp;

        // If timestamp is within the last 12 months (365 days * 24 hours * 60 minutes * 60 seconds)
        if ($difference < 365 * 24 * 60 * 60) {
            // Handle relative time
            if ($difference < 60) {
                return $difference . " seconds ago";
            } elseif ($difference < 3600) {
                $minutes = floor($difference / 60);
                return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
            } elseif ($difference < 86400) {
                $hours = floor($difference / 3600);
                return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
            } else {
                $days = floor($difference / 86400);
                return $days . " day" . ($days > 1 ? "s" : "") . " ago";
            }
        } else {
            // Return formatted date for timestamps older than 12 months
            return date("M d, Y", $timestamp);
        }
    }
}
?>

