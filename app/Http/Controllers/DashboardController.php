<?php
require_once __DIR__ . '/../../Models/Dashboard.php';

class DashboardController {
    private $kpiModel;

    public function __construct($db) {
        $this->kpiModel = new Dashboard($db);
    }

    public function getDashboardData() {
        return [
            'totalFlights' => $this->kpiModel->getTotalFlights(),
            'totalBookings' => $this->kpiModel->getTotalBookings(),
            'totalRevenue' => $this->kpiModel->getTotalRevenue(),
            'upcomingFlights' => $this->kpiModel->getUpcomingFlights(),
            'recentBookings' => $this->kpiModel->getRecentBookings(),
            'bookingStatusCounts' => $this->kpiModel->getBookingStatusCounts(),
        ];
    }
}
