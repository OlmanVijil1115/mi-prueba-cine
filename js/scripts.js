// Función para validar el formulario de login
function validarLogin() {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    if (!email || !password) {
        alert("Por favor, completa todos los campos.");
        return false;
    }
    return true;
}

// Función para validar el formulario de registro
function validarRegistro() {
    let nombre = document.getElementById('nombre').value;
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    if (!nombre || !email || !password) {
        alert("Por favor, completa todos los campos.");
        return false;
    }
    return true;
}