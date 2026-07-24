<?php
declare(strict_types=1);

final class MotosController extends Controller
{
    private Moto $model;

    public function __construct()
    {
        $this->model = new Moto();
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = trim((string)($_GET['q'] ?? ''));
        $this->view('motos/index', [
            'title' => 'Motocicletas',
            'motos' => $this->model->all($search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('motos/form', ['title' => 'Registrar motocicleta', 'moto' => null]);
    }

    public function store(): void
    {
        $this->requireAuth();
        Csrf::validate();
        $data = $this->validatedData();
        $this->model->create($data);
        clear_old();
        flash('success', 'Motocicleta registrada correctamente.');
        redirect('motos');
    }

    public function show(): void
    {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $moto = $this->model->find($id);
        if (!$moto) {
            http_response_code(404);
            require APP_PATH . '/views/errors/404.php';
            return;
        }

        $this->view('motos/show', [
            'title' => 'Expediente de motocicleta',
            'moto' => $moto,
            'mantenimientos' => (new Mantenimiento())->byMoto($id),
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $moto = $this->model->find($id);
        if (!$moto) {
            flash('error', 'Motocicleta no encontrada.');
            redirect('motos');
        }
        $this->view('motos/form', ['title' => 'Editar motocicleta', 'moto' => $moto]);
    }

    public function update(): void
    {
        $this->requireAuth();
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        if (!$this->model->find($id)) {
            flash('error', 'Motocicleta no encontrada.');
            redirect('motos');
        }
        $this->model->update($id, $this->validatedData());
        clear_old();
        flash('success', 'Motocicleta actualizada correctamente.');
        redirect('motos/ver?id=' . $id);
    }

    public function destroy(): void
    {
        $this->requireAuth();
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $this->model->delete($id);
        flash('success', 'Motocicleta eliminada.');
        redirect('motos');
    }

    private function validatedData(): array
    {
        $required = ['codigo_qr', 'marca', 'modelo', 'placa', 'unidad_asignada', 'estado'];
        foreach ($required as $field) {
            if (trim((string)($_POST[$field] ?? '')) === '') {
                $_SESSION['_old'] = $_POST;
                flash('error', 'Complete todos los campos obligatorios.');
                redirect($_POST['id'] ?? null ? 'motos/editar?id=' . (int)$_POST['id'] : 'motos/crear');
            }
        }

        return [
            'codigo_qr' => trim((string)$_POST['codigo_qr']),
            'marca' => trim((string)$_POST['marca']),
            'modelo' => trim((string)$_POST['modelo']),
            'anio' => ($_POST['anio'] ?? '') !== '' ? (int)$_POST['anio'] : null,
            'placa' => trim((string)$_POST['placa']),
            'numero_motor' => trim((string)($_POST['numero_motor'] ?? '')) ?: null,
            'numero_chasis' => trim((string)($_POST['numero_chasis'] ?? '')) ?: null,
            'unidad_asignada' => trim((string)$_POST['unidad_asignada']),
            'fecha_ingreso' => ($_POST['fecha_ingreso'] ?? '') ?: null,
            'kilometraje_actual' => max(0, (int)($_POST['kilometraje_actual'] ?? 0)),
            'tipo_mantenimiento' => (string)($_POST['tipo_mantenimiento'] ?? 'Mixto'),
            'estado' => (string)$_POST['estado'],
            'observaciones' => trim((string)($_POST['observaciones'] ?? '')) ?: null,
        ];
    }
}
