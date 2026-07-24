<?php
declare(strict_types=1);

final class MantenimientosController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('mantenimientos/index', [
            'title' => 'Mantenimientos',
            'mantenimientos' => (new Mantenimiento())->all(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $motoId = (int)($_GET['moto_id'] ?? 0);
        $this->view('mantenimientos/form', [
            'title' => 'Registrar mantenimiento',
            'motos' => (new Moto())->all(),
            'motoId' => $motoId,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        Csrf::validate();

        $motoId = (int)($_POST['moto_id'] ?? 0);
        $moto = (new Moto())->find($motoId);
        if (!$moto) {
            flash('error', 'Seleccione una motocicleta válida.');
            redirect('mantenimientos/crear');
        }

        $kilometraje = max(0, (int)($_POST['kilometraje'] ?? 0));
        if ($kilometraje < (int)$moto['kilometraje_actual']) {
            flash('error', 'El kilometraje no puede ser menor que el registrado actualmente.');
            redirect('mantenimientos/crear?moto_id=' . $motoId);
        }

        $data = [
            'moto_id' => $motoId,
            'fecha' => (string)($_POST['fecha'] ?? date('Y-m-d')),
            'kilometraje' => $kilometraje,
            'tipo' => (string)($_POST['tipo'] ?? 'Preventivo'),
            'diagnostico' => trim((string)($_POST['diagnostico'] ?? '')) ?: null,
            'trabajos_realizados' => trim((string)($_POST['trabajos_realizados'] ?? '')) ?: null,
            'repuestos_utilizados' => trim((string)($_POST['repuestos_utilizados'] ?? '')) ?: null,
            'responsable' => trim((string)($_POST['responsable'] ?? '')),
            'proximo_km' => ($_POST['proximo_km'] ?? '') !== '' ? (int)$_POST['proximo_km'] : null,
            'proxima_fecha' => ($_POST['proxima_fecha'] ?? '') ?: null,
            'estado' => (string)($_POST['estado'] ?? 'En proceso'),
            'creado_por' => (int)Auth::user()['id'],
        ];

        if ($data['responsable'] === '') {
            flash('error', 'Indique el responsable del mantenimiento.');
            redirect('mantenimientos/crear?moto_id=' . $motoId);
        }

        (new Mantenimiento())->create($data);
        flash('success', 'Mantenimiento registrado correctamente.');
        redirect('motos/ver?id=' . $motoId);
    }
}
