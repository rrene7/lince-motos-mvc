<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard');
        }
        $this->view('auth/login', ['title' => 'Iniciar sesión']);
    }

    public function login(): void
    {
        Csrf::validate();
        $correo = trim((string)($_POST['correo'] ?? ''));
        $clave = (string)($_POST['clave'] ?? '');
        $_SESSION['_old'] = ['correo' => $correo];

        $user = (new User())->findByEmail($correo);
        if (!$user || !password_verify($clave, $user['clave'])) {
            flash('error', 'Correo o contraseña incorrectos.');
            redirect('login');
        }

        Auth::login($user);
        clear_old();
        flash('success', 'Bienvenido al sistema.');
        redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('login');
    }
}
