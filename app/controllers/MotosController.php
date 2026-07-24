<?php
declare(strict_types=1);

final class MotosController extends Controller
{
    private const MAX_PHOTO_SIZE = 5 * 1024 * 1024;
    private const PHOTO_DIRECTORY = 'public/uploads/motos';

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

        $photo = $this->preparePhoto(null);
        $data = $this->validatedData();
        $data['foto'] = $photo['path'];

        try {
            $id = $this->model->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPhoto($photo['new_path']);
            throw $exception;
        }

        clear_old();
        flash('success', 'Motocicleta registrada correctamente.');
        redirect('motos/ver?id=' . $id);
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

        $host = preg_replace('/[^a-zA-Z0-9.\-:\[\]]/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
        $qrUrl = ($isHttps ? 'https' : 'http') . '://' . $host . base_url('motos/ver?id=' . $id);
        $qrLocalOnly = in_array(strtolower($host), ['localhost', '127.0.0.1', '[::1]'], true)
            || str_starts_with(strtolower($host), 'localhost:')
            || str_starts_with($host, '127.0.0.1:');

        $this->view('motos/show', [
            'title' => 'Expediente de motocicleta',
            'moto' => $moto,
            'mantenimientos' => (new Mantenimiento())->byMoto($id),
            'qrUrl' => $qrUrl,
            'qrLocalOnly' => $qrLocalOnly,
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
        $moto = $this->model->find($id);
        if (!$moto) {
            flash('error', 'Motocicleta no encontrada.');
            redirect('motos');
        }

        $photo = $this->preparePhoto($moto['foto'] ?? null);
        $data = $this->validatedData();
        $data['foto'] = $photo['path'];

        try {
            $this->model->update($id, $data);
        } catch (Throwable $exception) {
            $this->deleteStoredPhoto($photo['new_path']);
            throw $exception;
        }

        $this->deleteStoredPhoto($photo['old_to_delete']);
        clear_old();
        flash('success', 'Motocicleta actualizada correctamente.');
        redirect('motos/ver?id=' . $id);
    }

    public function destroy(): void
    {
        $this->requireAuth();
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $moto = $this->model->find($id);
        if ($moto) {
            $this->model->delete($id);
            $this->deleteStoredPhoto($moto['foto'] ?? null);
        }
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
                redirect($this->formRoute());
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

    private function preparePhoto(?string $currentPhoto): array
    {
        $removeCurrent = isset($_POST['remove_photo']) && $_POST['remove_photo'] === '1';
        $file = $_FILES['foto'] ?? null;
        $uploadError = is_array($file) ? (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;

        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            return [
                'path' => $removeCurrent ? null : $currentPhoto,
                'old_to_delete' => $removeCurrent ? $currentPhoto : null,
                'new_path' => null,
            ];
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            $this->photoError('No fue posible cargar la fotografía. Intente nuevamente.');
        }

        $temporaryName = (string)($file['tmp_name'] ?? '');
        $size = (int)($file['size'] ?? 0);
        if ($temporaryName === '' || !is_uploaded_file($temporaryName)) {
            $this->photoError('La fotografía recibida no es válida.');
        }
        if ($size <= 0 || $size > self::MAX_PHOTO_SIZE) {
            $this->photoError('La fotografía debe pesar menos de 5 MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($temporaryName);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mime])) {
            $this->photoError('Use una fotografía JPG, PNG o WEBP.');
        }

        $uploadDirectory = ROOT_PATH . '/' . self::PHOTO_DIRECTORY;
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
            $this->photoError('No se pudo preparar el directorio de fotografías.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        $relativePath = self::PHOTO_DIRECTORY . '/' . $filename;
        $destination = ROOT_PATH . '/' . $relativePath;

        if (!move_uploaded_file($temporaryName, $destination)) {
            $this->photoError('No se pudo guardar la fotografía en el servidor.');
        }

        return [
            'path' => $relativePath,
            'old_to_delete' => $currentPhoto,
            'new_path' => $relativePath,
        ];
    }

    private function photoError(string $message): never
    {
        $_SESSION['_old'] = $_POST;
        flash('error', $message);
        redirect($this->formRoute());
    }

    private function formRoute(): string
    {
        $id = (int)($_POST['id'] ?? 0);
        return $id > 0 ? 'motos/editar?id=' . $id : 'motos/crear';
    }

    private function deleteStoredPhoto(?string $relativePath): void
    {
        if (!$relativePath || !str_starts_with($relativePath, self::PHOTO_DIRECTORY . '/')) {
            return;
        }

        $file = ROOT_PATH . '/' . ltrim($relativePath, '/');
        $uploadDirectory = realpath(ROOT_PATH . '/' . self::PHOTO_DIRECTORY);
        $realFile = is_file($file) ? realpath($file) : false;

        if ($uploadDirectory && $realFile && str_starts_with($realFile, $uploadDirectory . DIRECTORY_SEPARATOR)) {
            @unlink($realFile);
        }
    }
}
