<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $motoModel = new Moto();
        $alerts = (new Mantenimiento())->upcomingAlerts();

        $this->view('dashboard/index', [
            'title' => 'Panel de mando',
            'total' => $motoModel->total(),
            'counts' => $motoModel->countsByStatus(),
            'alerts' => $alerts,
        ]);
    }
}
