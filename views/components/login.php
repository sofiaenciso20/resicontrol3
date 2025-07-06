
<div class="body-login">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-image">
                <div class="logo-superior">
                    <img src="assets/img/logo2.png" alt="Logo ResiControl" style="height: 50px;">
                </div>
            </div>
            <div class="login-form">
                <div class="text-center mb-4">
                    <h4 class="mt-2">Iniciar sesión</h4>
                </div>
                <form method="POST" action="/login.php" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required
                            placeholder="ejemplo@correo.com"
                            value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-login">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" required
                                placeholder="Tu contraseña">
                            <button class="btn btn-outline-secondary border-login" type="button"
                                onclick="togglePassword()">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button id="loginButton" type="submit" class="btn btn-login">Iniciar sesión</button>
                    <div class="mb-3 text-end">
                        <a href="recuperar_contra.php">¿Olvidaste la contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.querySelector('.btn-outline-secondary i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
// Validación del formulario en el cliente
document.getElementById('loginForm').addEventListener('submit', function(event) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    let isValid = true;

    // Validar email
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        alert('Por favor, ingresa un correo electrónico válido');
        isValid = false;
    }

    // Validar contraseña
    if (password.length < 8) {
        alert('La contraseña debe tener al menos 8 caracteres');
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    } else {
        // Deshabilitar el botón durante el envío
        document.getElementById('loginButton').disabled = true;
        document.getElementById('loginButton').innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Iniciando sesión...
        `;
    }
});
</script>